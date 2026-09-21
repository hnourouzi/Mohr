@extends('layouts.guest')

@section('title', 'مُهر — اعتبارسنجی هوشمند محتوا')

@section('content')
    <section class="max-w-6xl mx-auto px-6 pt-20 pb-24">
        <div class="max-w-2xl">
            <p class="text-seal font-semibold mb-4">پیش از انتشار، مطمئن شوید</p>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                هر محتوا، قبل از انتشار
                <br>یک مُهر تایید می‌خواهد.
            </h1>
            <p class="text-ink/70 text-lg leading-8 mb-10 max-w-xl">
                متن‌هایتان را از دو لایه بررسی رد کنید: فیلتر سریع کلمات ممنوعه و تحلیل هوش مصنوعی.
                نتیجه، تاریخچه کامل، و گزارش شفاف — همه در یک پنل ساده.
            </p>
            <div class="flex items-center gap-4">
                <a href="{{ route('register') }}" class="px-6 py-3 rounded bg-ink text-paper font-medium hover:bg-ink/90 transition">
                    شروع رایگان
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded border border-ink/20 hover:border-ink/40 transition">
                    ورود به حساب
                </a>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-24">
            <div class="border-r-2 border-seal pr-5">
                <div class="text-2xl font-bold mb-2">۰۱</div>
                <h3 class="font-semibold mb-2">فیلتر اولیه</h3>
                <p class="text-ink/60 text-sm leading-7">کلمات و عبارات ممنوعه فوراً و بدون هزینه‌ی اضافه شناسایی می‌شوند.</p>
            </div>
            <div class="border-r-2 border-seal pr-5">
                <div class="text-2xl font-bold mb-2">۰۲</div>
                <h3 class="font-semibold mb-2">تحلیل هوش مصنوعی</h3>
                <p class="text-ink/60 text-sm leading-7">محتوایی که از لایه اول عبور کند، برای بررسی دقیق‌تر به مدل هوش مصنوعی سپرده می‌شود.</p>
            </div>
            <div class="border-r-2 border-seal pr-5">
                <div class="text-2xl font-bold mb-2">۰۳</div>
                <h3 class="font-semibold mb-2">گزارش شفاف</h3>
                <p class="text-ink/60 text-sm leading-7">دلیل رد یا تایید هر محتوا، همراه با زمان‌بندی هر مرحله، در دسترس شماست.</p>
            </div>
        </div>
    </section>
@endsection
