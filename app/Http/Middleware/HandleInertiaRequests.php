<?php

namespace App\Http\Middleware;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $pendingBorrowCount = 0;
        if ($request->user()?->isAdmin()) {
            $pendingBorrowCount = Borrowing::where('status', 'menunggu_persetujuan')->count();
        }

        return [
            ...parent::share($request),
            'name'        => config('app.name'),
            'auth'        => ['user' => $request->user()],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'pendingBorrowCount' => $pendingBorrowCount,
        ];
    }
}
