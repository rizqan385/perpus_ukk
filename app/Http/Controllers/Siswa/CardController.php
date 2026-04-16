<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CardController extends Controller
{
    /**
     * Show the member card for the logged-in student.
     */
    public function show(): Response
    {
        $user   = auth()->user();
        $member = $user->member;

        if (!$member) {
            return Inertia::render('Siswa/NeedMembership');
        }

        $member->load('user');

        return Inertia::render('Siswa/Card', [
            'member' => $member,
        ]);
    }
}
