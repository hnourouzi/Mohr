<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
}
