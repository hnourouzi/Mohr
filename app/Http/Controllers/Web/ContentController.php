<?php

namespace App\Http\Controllers\Web;

use App\Actions\Content\RetryModerationAction;
use App\Actions\Content\SubmitContentAction;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentRequest;
use App\Models\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $contents = $request->user()
            ->contents()
            ->latest()
            ->paginate(10);

        return view('panel.contents.index', compact('contents'));
    }

    public function create(): View
    {
        return view('panel.contents.create');
    }

    public function store(StoreContentRequest $request, SubmitContentAction $action): RedirectResponse
    {
        try {
            $content = $action->execute(
                user: $request->user(),
                body: $request->validated('body'),
                channel: $request->validated('channel'),
            );
        } catch (InsufficientBalanceException $e) {
            return back()->withErrors(['balance' => $e->getMessage()]);
        }

        return redirect()
            ->route('panel.contents.show', $content)
            ->with('success', 'محتوا با موفقیت برای بررسی ارسال شد.');
    }

    public function show(Request $request, Content $content): View
    {
        $this->authorize('view', $content);

        $content->load('logs');

        return view('panel.contents.show', compact('content'));
    }

    public function retry(Request $request, Content $content, RetryModerationAction $action): RedirectResponse
    {
        $this->authorize('retry', $content);

        $action->execute($content);

        return back()->with('success', 'درخواست بررسی مجدد ارسال شد.');
    }
}