@extends('layouts.guest')

@section('title', 'ثبت‌نام')

@section('content')
    <section class="max-w-md mx-auto px-6 py-20">
        <h1 class="text-2xl font-bold mb-2">ساخت حساب جدید</h1>
        <p class="text-ink/60 mb-8">در کمتر از یک دقیقه حساب خود را بسازید.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1.5">نام</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
                @error('name')
                    <p class="text-reject text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" required
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

            <div>
                <label class="block text-sm font-medium mb-1.5">تکرار رمز عبور</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded border border-ink/15 focus:border-seal focus:outline-none focus:ring-1 focus:ring-seal transition">
            </div>

            <button type="submit" class="w-full py-3 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                ساخت حساب
            </button>
        </form>

        <p class="text-sm text-ink/60 mt-6">
            قبلاً ثبت‌نام کرده‌اید؟
            <a href="{{ route('login') }}" class="text-seal font-medium">وارد شوید</a>
        </p>
    </section>
@endsection
