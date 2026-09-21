<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'دستیار اعتبارسنجی محتوا')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Vazirmatn', 'sans-serif'] },
                    colors: {
                        ink: '#0F1729',
                        paper: '#F1F3F1',
                        seal: '#C89B3C',
                        sealdark: '#A9822F',
                        approve: '#2F8F5B',
                        reject: '#C4432E',
                        flagged: '#CC8B2C',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-paper text-ink min-h-screen">
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-ink/10">
            <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-ink flex items-center justify-center">
                        <span class="w-3 h-3 rounded-full bg-seal"></span>
                    </span>
                    <span class="font-bold text-lg">مُهر</span>
                </a>
                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('panel.dashboard') }}" class="px-4 py-2 rounded border border-ink/15 hover:border-ink/40 transition">پنل کاربری</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 hover:text-seal transition">ورود</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded bg-ink text-paper hover:bg-ink/90 transition">ثبت‌نام</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-ink/10 py-6">
            <div class="max-w-6xl mx-auto px-6 text-sm text-ink/50">
                مُهر — سرویس اعتبارسنجی هوشمند محتوا
            </div>
        </footer>
    </div>
</body>
</html>
