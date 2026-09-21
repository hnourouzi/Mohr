@extends('layouts.panel')

@section('title', 'کیف پول')
@section('page-title', 'کیف پول')

@section('content')
    <div class="grid md:grid-cols-3 gap-6 mb-10">
        <div class="md:col-span-1">
            <div class="bg-ink text-paper rounded p-6">
                <p class="text-paper/60 text-sm mb-2">موجودی فعلی</p>
                <p class="text-3xl font-bold text-seal">{{ number_format(auth()->user()->wallet->balance) }}</p>
                <p class="text-paper/40 text-xs mt-1">تومان</p>
            </div>
        </div>

        <div class="md:col-span-2">
            <form method="POST" action="{{ route('panel.wallet.topup') }}" class="bg-white border border-ink/10 rounded p-6">
                @csrf
                <label class="block text-sm font-medium mb-2">شارژ کیف پول</label>
                <div class="flex gap-3">
                    <input type="number" name="amount" min="1000" step="1000" value="{{ old('amount') }}"
                           placeholder="مبلغ به تومان" required
                           class="flex-1 px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                    <button type="submit" class="px-6 py-2.5 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                        شارژ حساب
                    </button>
                </div>
                @error('amount')
                    <p class="text-reject text-sm mt-2">{{ $message }}</p>
                @enderror
                <div class="flex gap-2 mt-3">
                    @foreach ([5000, 10000, 20000, 50000] as $amount)
                        <button type="button" onclick="document.querySelector('input[name=amount]').value = {{ $amount }}"
                                class="text-xs px-3 py-1.5 rounded border border-ink/15 hover:border-seal transition">
                            {{ number_format($amount) }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <h2 class="font-bold mb-4">تاریخچه تراکنش‌ها</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($transactions as $tx)
            <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }}">
                <div>
                    <p class="text-sm">{{ $tx->description }}</p>
                    <p class="text-xs text-ink/40 mt-1">{{ $tx->created_at->format('Y/m/d H:i') }}</p>
                </div>
                <div class="text-left">
                    <p class="font-medium {{ $tx->type->value === 'credit' ? 'text-approve' : 'text-reject' }}">
                        {{ $tx->type->sign() }}{{ number_format($tx->amount) }}
                    </p>
                    <p class="text-xs text-ink/40 mt-1">موجودی: {{ number_format($tx->balance_after) }}</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-10 text-center text-ink/40 text-sm">هنوز تراکنشی ثبت نشده است.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
@endsection
