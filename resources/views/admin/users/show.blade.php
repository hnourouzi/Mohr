@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
    <div class="grid md:grid-cols-3 gap-6 mb-10">
        <div class="bg-ink text-paper rounded p-6">
            <p class="text-paper/60 text-sm mb-2">موجودی فعلی</p>
            <p class="text-3xl font-bold text-seal">{{ number_format($user->wallet->balance) }}</p>
            <p class="text-paper/40 text-xs mt-1">تومان</p>
        </div>

        <div class="md:col-span-2 bg-white border border-ink/10 rounded p-6">
            <form method="POST" action="{{ route('admin.users.topup', $user) }}">
                @csrf
                <label class="block text-sm font-medium mb-2">شارژ دستی کیف پول کاربر</label>
                <div class="flex gap-3">
                    <input type="number" name="amount" min="1000" step="1000" required
                           placeholder="مبلغ به تومان"
                           class="flex-1 px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                    <button type="submit" class="px-6 py-2.5 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                        شارژ کاربر
                    </button>
                </div>
                @error('amount')
                    <p class="text-reject text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>

    <h2 class="font-bold mb-4">محتواهای کاربر</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden mb-10">
        @forelse ($user->contents as $content)
            <a href="{{ route('admin.contents.show', $content) }}"
               class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }} hover:bg-paper/50 transition">
                <p class="truncate text-sm flex-1 ml-4">{{ \Illuminate\Support\Str::limit($content->body, 60) }}</p>
                <x-status-badge :status="$content->status" />
            </a>
        @empty
            <div class="px-5 py-8 text-center text-ink/40 text-sm">هنوز محتوایی ندارد.</div>
        @endforelse
    </div>

    <h2 class="font-bold mb-4">تراکنش‌های کیف پول</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($user->wallet->transactions as $tx)
            <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }}">
                <div>
                    <p class="text-sm">{{ $tx->description }}</p>
                    <p class="text-xs text-ink/40 mt-1">{{ $tx->created_at->format('Y/m/d H:i') }}</p>
                </div>
                <p class="font-medium {{ $tx->type->value === 'credit' ? 'text-approve' : 'text-reject' }}">
                    {{ $tx->type->sign() }}{{ number_format($tx->amount) }}
                </p>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-ink/40 text-sm">هنوز تراکنشی ندارد.</div>
        @endforelse
    </div>
@endsection
