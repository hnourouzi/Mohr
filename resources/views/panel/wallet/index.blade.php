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
            <div class="bg-white border border-ink/10 rounded p-6 text-sm text-ink/70">
                شارژ آنلاین کیف پول از طریق پنل کاربری فعال نیست. برای افزایش موجودی با پشتیبانی تماس بگیرید یا از مدیر سیستم درخواست شارژ دستی کنید.
            </div>
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
                    <p class="font-medium {{ in_array($tx->type->value, ['credit', 'refund'], true) ? 'text-approve' : 'text-reject' }}">
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
