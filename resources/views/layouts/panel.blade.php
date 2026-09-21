<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل کاربری')</title>
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
    <style> body { font-family: 'Vazirmatn', sans-serif; } </style>
</head>
<body class="bg-paper text-ink">
    <div class="flex min-h-screen">
        <!-- Sidebar (right side in RTL) -->
        <aside class="w-64 bg-ink text-paper flex flex-col shrink-0">
            <div class="px-6 py-6 border-b border-paper/10">
                <a href="{{ route('panel.dashboard') }}" class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-paper/10 flex items-center justify-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-seal"></span>
                    </span>
                    <span class="font-bold">مُهر</span>
                </a>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 text-sm">
                <a href="{{ route('panel.dashboard') }}"
                   class="block px-3 py-2.5 rounded {{ request()->routeIs('panel.dashboard') ? 'bg-paper/10 text-seal' : 'text-paper/70 hover:bg-paper/5' }} transition">
                    داشبورد
                </a>
                <a href="{{ route('panel.contents.index') }}"
                   class="block px-3 py-2.5 rounded {{ request()->routeIs('panel.contents.*') ? 'bg-paper/10 text-seal' : 'text-paper/70 hover:bg-paper/5' }} transition">
                    محتواهای من
                </a>
                <a href="{{ route('panel.wallet.index') }}"
                   class="block px-3 py-2.5 rounded {{ request()->routeIs('panel.wallet.*') ? 'bg-paper/10 text-seal' : 'text-paper/70 hover:bg-paper/5' }} transition">
                    کیف پول
                </a>

                @if (auth()->user()?->role?->value === 'admin')
                    <div class="pt-4 mt-4 border-t border-paper/10">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2.5 rounded text-paper/70 hover:bg-paper/5 transition">
                            پنل ادمین
                        </a>
                    </div>
                @endif
            </nav>

            <div class="px-3 py-4 border-t border-paper/10">
                <div class="px-3 py-2 mb-2">
                    <p class="text-sm font-medium">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-paper/50">{{ auth()->user()?->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right px-3 py-2 rounded text-sm text-paper/70 hover:bg-paper/5 transition">
                        خروج از حساب
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
            <header class="bg-white border-b border-ink/10 px-8 py-5">
                <h1 class="text-xl font-bold">@yield('page-title', 'داشبورد')</h1>
            </header>

            <main class="px-8 py-8 max-w-5xl">
                <x-flash />
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
