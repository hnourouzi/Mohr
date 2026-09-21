@extends('layouts.admin')

@section('title', 'لیست سیاه کلمات')
@section('page-title', 'لیست سیاه کلمات')

@section('content')
    <form method="POST" action="{{ route('admin.blacklist.store') }}" class="bg-white border border-ink/10 rounded p-5 mb-8">
        @csrf
        <div class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1.5">کلمه یا عبارت</label>
                <input type="text" name="word" required
                       class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">شدت</label>
                <select name="severity" class="px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                    <option value="block">مسدود کننده</option>
                    <option value="flag">فقط علامت‌گذاری</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                افزودن
            </button>
        </div>
        @error('word')
            <p class="text-reject text-sm mt-2">{{ $message }}</p>
        @enderror
    </form>

    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        @forelse ($words as $word)
            <div class="flex items-center justify-between px-5 py-4 {{ !$loop->last ? 'border-b border-ink/10' : '' }}">
                <div>
                    <span class="text-sm font-medium">{{ $word->word }}</span>
                    <span class="text-xs px-2 py-0.5 rounded {{ $word->severity->value === 'block' ? 'bg-reject/10 text-reject' : 'bg-flagged/10 text-flagged' }} mr-2">
                        {{ $word->severity->label() }}
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.blacklist.destroy', $word) }}" onsubmit="return confirm('حذف شود؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-reject hover:underline">حذف</button>
                </form>
            </div>
        @empty
            <div class="px-5 py-10 text-center text-ink/40 text-sm">لیست سیاه خالی است.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $words->links() }}
    </div>
@endsection
