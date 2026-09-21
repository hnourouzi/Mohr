@extends('layouts.panel')

@section('title', 'جزئیات محتوا')
@section('page-title', 'جزئیات محتوا #' . $content->id)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <x-status-badge :status="$content->status" class="!text-sm" />

        @if ($content->isRetryable())
            <form method="POST" action="{{ route('panel.contents.retry', $content) }}">
                @csrf
                <button type="submit" class="text-sm px-4 py-2 rounded border border-ink/20 hover:border-ink/40 transition">
                    تلاش مجدد ({{ $content->retry_count }}/۳)
                </button>
            </form>
        @endif
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

    <div class="grid grid-cols-3 gap-4 mb-8 text-sm">
        <div class="border border-ink/10 rounded p-4">
            <p class="text-ink/50 mb-1">هزینه</p>
            <p class="font-medium">{{ number_format($content->cost) }} تومان</p>
        </div>
        <div class="border border-ink/10 rounded p-4">
            <p class="text-ink/50 mb-1">کانال</p>
            <p class="font-medium">{{ $content->channel ?? '—' }}</p>
        </div>
        <div class="border border-ink/10 rounded p-4">
            <p class="text-ink/50 mb-1">تاریخ ارسال</p>
            <p class="font-medium">{{ $content->created_at->format('Y/m/d H:i') }}</p>
        </div>
    </div>

    <h2 class="font-bold mb-4">مسیر بررسی</h2>
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($content->logs as $log)
            <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }}">
                <div>
                    <p class="text-sm font-medium">{{ $log->layer->label() }}</p>
                    @if ($log->detail)
                        <p class="text-xs text-ink/40 mt-1">{{ json_encode($log->detail, JSON_UNESCAPED_UNICODE) }}</p>
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
