@extends('layouts.admin')

@section('title', 'داشبورد ادمین')
@section('page-title', 'داشبورد کلی')

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <x-stat-card label="تعداد کاربران" value="{{ $stats['total_users'] }}" :accent="true" />
        <x-stat-card label="کل محتواها" value="{{ $stats['total_contents'] }}" />
        <x-stat-card label="در انتظار بررسی" value="{{ $stats['pending_contents'] }}" />
        <x-stat-card label="خطا در پردازش" value="{{ $stats['failed_contents'] }}" />
    </div>

    <h2 class="font-bold mb-4">آخرین محتواهای ارسالی</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($recentContents as $content)
            <a href="{{ route('admin.contents.show', $content) }}"
               class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }} hover:bg-paper/50 transition">
                <div class="min-w-0 flex-1 ml-4">
                    <p class="truncate text-sm">{{ \Illuminate\Support\Str::limit($content->body, 60) }}</p>
                    <p class="text-xs text-ink/40 mt-1">{{ $content->user->name }} · {{ $content->created_at->diffForHumans() }}</p>
                </div>
                <x-status-badge :status="$content->status" />
            </a>
        @empty
            <div class="px-5 py-10 text-center text-ink/40 text-sm">هنوز محتوایی ثبت نشده است.</div>
        @endforelse
    </div>
@endsection
