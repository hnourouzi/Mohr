<?php

namespace App\Http\Controllers\Web;

use App\Actions\Wallet\TopUpWalletAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopUpWalletRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = $request->user()
            ->wallet
            ->transactions()
            ->latest()
            ->paginate(15);

        return view('panel.wallet.index', compact('transactions'));
    }

    public function topUp(TopUpWalletRequest $request, TopUpWalletAction $action): RedirectResponse
    {
        $action->execute($request->user(), $request->validated('amount'));

        return back()->with('success', 'حساب شما با موفقیت شارژ شد.');
    }
}