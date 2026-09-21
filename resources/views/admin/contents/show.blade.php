@extends('layouts.admin')

@section('title', 'جزئیات محتوا')
@section('page-title', 'محتوا #' . $content->id)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <x-status-badge :status="$content->status" />
            <span class="text-sm text-ink/50 mr-2">
                کاربر:
                <a href="{{ route('admin.users.show', $content->user) }}" class="text-sealdark hover:underline">{{ $content->user->name }}</a>
            </span>
        </div>
    </div>

    <div class="bg-white border border-ink/10 rounded p-5 mb-6">
        <p class="text-sm text-ink/50 mb-2">متن محتوا</p>
        <p class="leading-8 whitespace-pre-wrap">{{ $content->body }}</p>
    </div>

    @if ($content->rejection_reason)
        <div class="mb-6 px-4 py-3 rounded bg-reject/10 text-reject text-sm border-r-2 border-reject">
            <strong>دلیل رد:</strong> {{ $content->rejection_reason }}
        </div>
    @endif

    @if ($content->ai_categories)
        <div class="mb-6 px-4 py-3 rounded bg-flagged/10 text-flagged text-sm border-r-2 border-flagged">
            <strong>دسته‌بندی‌های تشخیص داده شده:</strong> {{ implode('، ', $content->ai_categories) }}
        </div>
    @endif

    <h2 class="font-bold mb-4">مسیر بررسی</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($content->logs as $log)
            <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }}">
                <div>
                    <p class="text-sm font-medium">{{ $log->layer->label() }}</p>
                    @if ($log->detail)
                        <p class="text-xs text-ink/40 mt-1 font-mono">{{ json_encode($log->detail, JSON_UNESCAPED_UNICODE) }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if ($log->duration_ms)
                        <span class="text-xs text-ink/40">{{ $log->duration_ms }}ms</span>
                    @endif
                    <x-status-badge :status="$log->result" />
                </div>
            </div>
        @empty
            <div class="px-5 py-10 text-center text-ink/40 text-sm">هنوز پردازشی ثبت نشده است.</div>
        @endforelse
    </div>
@endsection
