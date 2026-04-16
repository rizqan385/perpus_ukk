<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request): Response
    {
        $query = Member::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_anggota', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter members with fines
        if ($request->input('has_fine') === 'yes') {
            $query->whereHas('borrowings', function ($q) {
                $q->where('denda', '>', 0)->whereNull('payment_status');
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }

        $members = $query->latest()->paginate(10);

        // Add total_fines to each member
        $members->getCollection()->transform(function ($member) {
            $member->total_fines = $member->borrowings()
                ->where('denda', '>', 0)
                ->whereNull('payment_status')
                ->sum('denda');
            $member->pending_fines = $member->borrowings()
                ->where('denda', '>', 0)
                ->where('payment_status', 'pending')
                ->sum('denda');
            return $member;
        });

        $allKelas = Member::whereNotNull('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return Inertia::render('Admin/Members/Index', [
            'members'  => $members,
            'allKelas' => $allKelas,
            'filters'  => $request->only('search', 'status', 'has_fine', 'kelas', 'jenis_kelamin'),
        ]);
    }

    /**
     * Show the form for creating a new member.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Members/Create');
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'kelas'         => 'nullable|string|max:50',
            'alamat'        => 'nullable|string|max:255',
            'telepon'       => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'siswa',
            'phone'    => $validated['telepon'] ?? null,
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('members', 'public');
        }

        Member::create([
            'user_id'       => $user->id,
            'no_anggota'    => Member::generateMemberNumber(),
            'kelas'         => $validated['kelas'] ?? null,
            'alamat'        => $validated['alamat'] ?? null,
            'telepon'       => $validated['telepon'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'foto'          => $fotoPath,
            'status'        => 'aktif',
        ]);

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member): Response
    {
        $member->load(['user', 'borrowings.book']);

        return Inertia::render('Admin/Members/Show', [
            'member' => $member,
        ]);
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member): Response
    {
        $member->load('user');

        return Inertia::render('Admin/Members/Edit', [
            'member' => $member,
        ]);
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $member->user_id,
            'kelas'         => 'nullable|string|max:50',
            'alamat'        => 'nullable|string|max:255',
            'telepon'       => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $member->user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['telepon'] ?? null,
        ]);

        $updateData = [
            'kelas'         => $validated['kelas'],
            'alamat'        => $validated['alamat'],
            'telepon'       => $validated['telepon'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'status'        => $validated['status'],
        ];

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }
            $updateData['foto'] = $request->file('foto')->store('members', 'public');
        }

        $member->update($updateData);

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    /**
     * Show the printable member card for admin.
     */
    public function card(Member $member): Response
    {
        $member->load('user');

        return Inertia::render('Admin/Members/Card', [
            'member' => $member,
        ]);
    }

    /**
     * Bulk card printing page with filters.
     */
    public function cards(Request $request): Response
    {
        $query = Member::with('user')->where('status', 'aktif');

        if ($request->input('kelas')) {
            $query->where('kelas', 'like', '%' . $request->input('kelas') . '%');
        }

        if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_anggota', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // All distinct classes for filter dropdown
        $allKelas = Member::whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $members = $query->join('users', 'members.user_id', '=', 'users.id')
            ->orderBy('members.kelas')
            ->orderBy('users.name')
            ->select('members.*')
            ->get();

        return Inertia::render('Admin/Members/Cards', [
            'members'  => $members,
            'allKelas' => $allKelas,
            'filters'  => $request->only('kelas', 'search'),
        ]);
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member): RedirectResponse
    {
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }
        $member->user->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
