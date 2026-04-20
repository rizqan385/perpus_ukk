<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Admin goes to admin panel, students stay on home
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            }
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Generate 6 digit OTP
        $otpCode = (string) random_int(100000, 999999);

        // 1. Store registration data in session
        $request->session()->put('registration_data', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);
        $request->session()->put('registration_otp', $otpCode);
        $request->session()->put('registration_otp_expires', now()->addMinutes(10));

        // 2. Try to send notifications (WhatsApp & Email)
        // Wrapped in try-catch so it doesn't "stuck" if API/SMTP is slow/down
        $waSent = false;
        $emailSent = false;

        try {
            // WhatsApp (Fonnte)
            if (config('services.fonnte.token')) {
                $fonnte = new FonnteService();
                $waMessage = "Halo *{$validated['name']}*! 👋\n\nKode OTP pendaftaran Anda adalah: *{$otpCode}*";
                $waSent = $fonnte->send($validated['phone'], $waMessage);
            }

            // Email
            Mail::raw("Halo {$validated['name']}! 👋\n\nKode OTP Anda adalah: {$otpCode}", function ($message) use ($validated) {
                $message->to($validated['email'])->subject('OTP E-Perpustakaan');
            });
            $emailSent = true;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OTP SEND ERROR: ' . $e->getMessage());
        }

        // 3. Always redirect to OTP page regardless of notification success
        // User can still see OTP in Debug Mode if enabled
        $statusMsg = 'Proses pendaftaran berhasil. ';
        if ($waSent || $emailSent) {
            $statusMsg .= 'Kode OTP telah dikirim.';
        } else {
            $statusMsg .= 'Silakan cek OTP di bawah (Debug Mode) atau hubungi admin.';
        }

        return redirect()->route('siswa.otp.page')->with('success', $statusMsg);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $sessionOtp = $request->session()->get('registration_otp');
        $sessionExpires = $request->session()->get('registration_otp_expires');
        $regData = $request->session()->get('registration_data');

        if (!$sessionOtp || !$regData || now()->greaterThan($sessionExpires)) {
            return back()->withErrors(['otp' => 'Sesi pendaftaran telah berakhir atau OTP kadaluarsa. Silakan daftar ulang.']);
        }

        if ($request->otp !== $sessionOtp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        // OTP is valid! Create the user and mark as verified.
        $user = User::create([
            'name' => $regData['name'],
            'email' => $regData['email'],
            'phone' => $regData['phone'],
            'password' => $regData['password'],
            'role' => 'siswa',
        ]);

        $user->markEmailAsVerified();

        // Clear session data
        $request->session()->forget(['registration_data', 'registration_otp', 'registration_otp_expires']);

        Auth::login($user);

        return redirect('/')->with('success', 'Akun berhasil diverifikasi dan dibuat.');
    }

    public function resendOtp(Request $request)
    {
        $regData = $request->session()->get('registration_data');

        if (!$regData) {
            return redirect()->route('siswa.register.page')->withErrors(['error' => 'Sesi pendaftaran tidak ditemukan.']);
        }

        $otpCode = (string) random_int(100000, 999999);
        $request->session()->put('registration_otp', $otpCode);
        $request->session()->put('registration_otp_expires', now()->addMinutes(10));

        $fonnte = new FonnteService();
        $message = "Halo *{$regData['name']}*! 👋\n\n"
            . "Ini adalah kode OTP *BARU* pendaftaran Anda:\n\n"
            . "*{$otpCode}*\n\n"
            . "Kode ini berlaku selama 10 menit.";

        $sent = $fonnte->send($regData['phone'], $message);

        if (!$sent) {
            return back()->withErrors(['error' => 'Gagal mengirim OTP via WhatsApp. Pastikan token Fonnte sudah benar atau hubungi admin.']);
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }

    public function resendOtpEmail(Request $request)
    {
        $regData = $request->session()->get('registration_data');

        if (!$regData) {
            return redirect()->route('siswa.register.page')->withErrors(['error' => 'Sesi pendaftaran tidak ditemukan.']);
        }

        $otpCode = (string) random_int(100000, 999999);
        $request->session()->put('registration_otp', $otpCode);
        $request->session()->put('registration_otp_expires', now()->addMinutes(10));

        try {
            Mail::raw("Halo {$regData['name']}! 👋\n\nIni adalah kode OTP pendaftaran Anda:\n\n{$otpCode}\n\nKode ini berlaku selama 10 menit.", function ($message) use ($regData) {
                $message->to($regData['email'])
                    ->subject('Kode OTP Pendaftaran E-Perpustakaan');
            });
            return back()->with('success', 'Kode OTP baru telah dikirim ke Email Anda.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMTP ERROR DETAIL: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }
}
