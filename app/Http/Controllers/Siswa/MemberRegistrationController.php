<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberRegistrationController extends Controller
{
    /**
     * Show the member registration form.
     */
    public function create(): Response
    {
        $user = auth()->user();

        if ($user->member) {
            return Inertia::render('Siswa/AlreadyMember', [
                'member' => $user->member,
            ]);
        }

        return Inertia::render('Siswa/Register', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created member profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->member) {
            return redirect()->route('siswa.dashboard')
                ->with('info', 'Anda sudah terdaftar sebagai anggota.');
        }

        $validated = $request->validate([
            'kelas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
        ]);

        Member::create([
            'user_id' => $user->id,
            'no_anggota' => Member::generateMemberNumber(),
            'kelas' => $validated['kelas'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'status' => 'aktif',
        ]);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Selamat! Anda telah terdaftar sebagai anggota perpustakaan.');
    }
}
