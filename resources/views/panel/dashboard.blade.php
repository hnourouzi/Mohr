@extends('layouts.panel')

@section('title', 'داشبورد')
@section('page-title', 'داشبورد')

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <x-stat-card label="موجودی کیف پول" value="{{ number_format($stats['balance']) }} تومان" :accent="true" />
        <x-stat-card label="تعداد کل محتوا" value="{{ $stats['total_contents'] }}" />
        <x-stat-card label="تایید شده" value="{{ $stats['approved_count'] }}" />
        <x-stat-card label="رد شده" value="{{ $stats['rejected_count'] }}" />
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold">آخرین محتواها</h2>
        <a href="{{ route('panel.contents.create') }}" class="text-sm px-4 py-2 rounded bg-ink text-paper hover:bg-ink/90 transition">
            + ارسال محتوای جدید
        </a>
    </div>

    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($recentContents as $content)
            <a href="{{ route('panel.contents.show', $content) }}"
               class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }} hover:bg-paper/50 transition">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm">{{ \Illuminate\Support\Str::limit($content->body, 60) }}</p>
                    <p class="text-xs text-ink/40 mt-1">{{ $content->created_at->diffForHumans() }}</p>
                </div>
                <x-status-badge :status="$content->status" />
            </a>
        @empty
            <div class="px-5 py-10 text-center text-ink/40 text-sm">هنوز محتوایی ارسال نکرده‌اید.</div>
        @endforelse
    </div>
@endsection
