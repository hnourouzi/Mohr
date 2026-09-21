<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'balance' => $user->wallet->balance,
            'total_contents' => $user->contents()->count(),
            'approved_count' => $user->contents()->where('status', \App\Enums\ContentStatus::Approved)->count(),
            'rejected_count' => $user->contents()->whereIn('status', [
                \App\Enums\ContentStatus::Rejected,
                \App\Enums\ContentStatus::Layer1Rejected,
            ])->count(),
        ];

        $recentContents = $user->contents()->latest()->limit(5)->get();

        return view('panel.dashboard', compact('stats', 'recentContents'));
    }
}