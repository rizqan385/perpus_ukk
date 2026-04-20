<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user();
        $user->load('member');

        return Inertia::render('Siswa/Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'member' => $user->member ? [
                    'id' => $user->member->id,
                    'no_anggota' => $user->member->no_anggota,
                    'kelas' => $user->member->kelas,
                    'alamat' => $user->member->alamat,
                    'telepon' => $user->member->telepon,
                    'foto_url' => $user->member->foto_url,
                ] : null,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::default()],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($member) {
            $member->alamat = $request->alamat;
            $member->telepon = $request->telepon;

            if ($request->hasFile('foto')) {
                if ($member->foto) {
                    Storage::disk('public')->delete($member->foto);
                }
                $path = $request->file('foto')->store('members', 'public');
                $member->foto = $path;
            }

            $member->save();
        }

        return redirect()->route('siswa.profile.success');
    }

    public function success(): Response
    {
        return Inertia::render('Siswa/SuccessUpdate');
    }
}
