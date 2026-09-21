@extends('layouts.panel')

@section('title', 'محتواهای من')
@section('page-title', 'محتواهای من')

@section('content')
    <div class="flex items-center justify-end mb-6">
        <a href="{{ route('panel.contents.create') }}" class="text-sm px-4 py-2 rounded bg-ink text-paper hover:bg-ink/90 transition">
            + ارسال محتوای جدید
        </a>
    </div>

    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($contents as $content)
            <a href="{{ route('panel.contents.show', $content) }}"
               class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }} hover:bg-paper/50 transition">
                <div class="min-w-0 flex-1 ml-4">
                    <p class="truncate text-sm">{{ \Illuminate\Support\Str::limit($content->body, 70) }}</p>
                    <p class="text-xs text-ink/40 mt-1">
                        {{ $content->created_at->format('Y/m/d H:i') }}
                        @if ($content->channel)
                            · {{ $content->channel }}
                        @endif
                    </p>
                </div>
                <x-status-badge :status="$content->status" />
            </a>
        @empty
            <div class="px-5 py-16 text-center text-ink/40 text-sm">هنوز محتوایی ارسال نکرده‌اید.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $contents->links() }}
    </div>
@endsection
