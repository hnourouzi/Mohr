<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_contents' => Content::count(),
            'pending_contents' => Content::where('status', ContentStatus::Pending)->count(),
            'failed_contents' => Content::where('status', ContentStatus::Failed)->count(),
        ];

        $recentContents = Content::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentContents'));
    }
}