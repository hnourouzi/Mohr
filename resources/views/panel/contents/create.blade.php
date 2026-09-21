@extends('layouts.panel')

@section('title', 'ارسال محتوای جدید')
@section('page-title', 'ارسال محتوای جدید')

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6 px-4 py-3 rounded bg-seal/10 text-sealdark text-sm">
            هزینه هر بار بررسی: <strong>{{ number_format(config('moderation.cost_per_request', 1000)) }} تومان</strong>
            از موجودی کیف پول شما کسر می‌شود.
        </div>

        <form method="POST" action="{{ route('panel.contents.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1.5">متن محتوا</label>
                <textarea name="body" rows="8" required
                          class="w-full px-4 py-3 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition"
                          placeholder="متن خود را اینجا وارد کنید...">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-reject text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">کانال مقصد (اختیاری)</label>
                <select name="channel" class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                    <option value="">— بدون کانال مشخص —</option>
                    <option value="web" {{ old('channel') === 'web' ? 'selected' : '' }}>وب‌سایت</option>
                    <option value="linkedin" {{ old('channel') === 'linkedin' ? 'selected' : '' }}>لینکدین</option>
                    <option value="instagram" {{ old('channel') === 'instagram' ? 'selected' : '' }}>اینستاگرام</option>
                    <option value="twitter" {{ old('channel') === 'twitter' ? 'selected' : '' }}>توییتر / ایکس</option>
                </select>
                @error('channel')
                    <p class="text-reject text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="px-6 py-3 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                ارسال برای بررسی
            </button>
        </form>
    </div>
@endsection
