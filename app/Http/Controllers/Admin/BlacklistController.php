<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlacklistWordRequest;
use App\Models\BlacklistWord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlacklistController extends Controller
{
    public function index(): View
    {
        $words = BlacklistWord::latest()->paginate(20);

        return view('admin.blacklist.index', compact('words'));
    }

    public function store(StoreBlacklistWordRequest $request): RedirectResponse
    {
        BlacklistWord::create($request->validated());

        return back()->with('success', 'کلمه به لیست سیاه اضافه شد.');
    }

    public function destroy(BlacklistWord $blacklistWord): RedirectResponse
    {
        $blacklistWord->delete();

        return back()->with('success', 'کلمه حذف شد.');
    }
}   