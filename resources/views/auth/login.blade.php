@extends('layouts.guest')

@section('title', 'ورود')

@section('content')
    <section class="max-w-md mx-auto px-6 py-20">
        <h1 class="text-2xl font-bold mb-2">ورود به حساب</h1>
        <p class="text-ink/60 mb-8">برای دسترسی به پنل خود وارد شوید.</p>

        @if (session('status'))
            <div class="mb-6 px-4 py-3 rounded bg-approve/10 text-approve text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1.5">ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                @error('email')
                    <p class="text-reject text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">رمز عبور</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                @error('password')
                    <p class="text-reject text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-ink/70">
                <input type="checkbox" name="remember" class="rounded border-ink/30">
                مرا به خاطر بسپار
            </label>

            <button type="submit" class="w-full py-3 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                ورود
            </button>
        </form>

        <p class="text-sm text-ink/60 mt-6">
            حساب ندارید؟
            <a href="{{ route('register') }}" class="text-seal font-medium">ثبت‌نام کنید</a>
        </p>
    </section>
@endsection
