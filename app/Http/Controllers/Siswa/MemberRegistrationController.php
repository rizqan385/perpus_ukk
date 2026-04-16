<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MemberRegistrationController extends Controller
{
    /**
     * Show the member registration / profile completion form.
     */
    public function create(Request $request): Response
    {
        $user   = auth()->user();
        $member = $user->member()->first(); // fresh query, no caching

        return Inertia::render('Siswa/Register', [
            'user'       => $user,
            'member'     => $member,                          // null jika belum ada
            'incomplete' => $request->boolean('incomplete'),
        ]);
    }

    /**
     * Create or update the member profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $user   = auth()->user();
        $member = $user->member()->first(); // fresh query

        $validated = $request->validate([
            'kelas'         => 'required|string|max:50',
            'alamat'        => 'required|string|max:255',
            'telepon'       => 'required|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ── Handle foto upload ────────────────────────────────
        $fotoPath = $member?->foto; // keep existing if not re-uploaded
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($member?->foto) {
                Storage::disk('public')->delete($member->foto);
            }
            $fotoPath = $request->file('foto')->store('members', 'public');
        }

        // ── updateOrCreate: aman untuk create ulang / update ──
        Member::updateOrCreate(
            ['user_id' => $user->id],
            [
                'no_anggota'    => $member?->no_anggota ?? Member::generateMemberNumber(),
                'kelas'         => $validated['kelas'],
                'alamat'        => $validated['alamat'],
                'telepon'       => $validated['telepon'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'foto'          => $fotoPath,
                'status'        => 'aktif',
            ]
        );

        $message = $member
            ? 'Profil anggota berhasil diperbarui.'
            : 'Selamat! Anda telah terdaftar sebagai anggota perpustakaan.';

        return redirect()->route('siswa.dashboard')->with('success', $message);
    }
}
