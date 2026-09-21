@extends('layouts.admin')

@section('title', 'همه محتواها')
@section('page-title', 'همه محتواها')

@section('content')
    <div class="flex items-center gap-2 mb-6 text-sm flex-wrap">
        <a href="{{ route('admin.contents.index') }}"
           class="px-3 py-1.5 rounded {{ !request('status') ? 'bg-ink text-paper' : 'border border-ink/15' }}">همه</a>
        @foreach (\App\Enums\ContentStatus::cases() as $status)
            <a href="{{ route('admin.contents.index', ['status' => $status->value]) }}"
               class="px-3 py-1.5 rounded {{ request('status') === $status->value ? 'bg-ink text-paper' : 'border border-ink/15' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($contents as $content)
            <a href="{{ route('admin.contents.show', $content) }}"
               class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }} hover:bg-paper/50 transition">
                <div class="min-w-0 flex-1 ml-4">
                    <p class="truncate text-sm">{{ \Illuminate\Support\Str::limit($content->body, 60) }}</p>
                    <p class="text-xs text-ink/40 mt-1">{{ $content->user->name }} · {{ $content->created_at->format('Y/m/d H:i') }}</p>
                </div>
                <x-status-badge :status="$content->status" />
            </a>
        @empty
            <div class="px-5 py-16 text-center text-ink/40 text-sm">محتوایی یافت نشد.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $contents->links() }}
    </div>
@endsection
