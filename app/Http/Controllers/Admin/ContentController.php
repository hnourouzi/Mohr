<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $contents = Content::with('user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.contents.index', compact('contents'));
    }

    public function show(Content $content): View
    {
        $content->load(['user', 'logs']);

        return view('admin.contents.show', compact('content'));
    }
}