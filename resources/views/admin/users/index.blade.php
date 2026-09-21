@extends('layouts.admin')

@section('title', 'کاربران')
@section('page-title', 'کاربران')

@section('content')
    <div class="bg-white border border-ink/10 rounded overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-paper text-ink/50 text-xs">
                <tr>
                    <th class="text-right px-5 py-3 font-medium">نام</th>
                    <th class="text-right px-5 py-3 font-medium">ایمیل</th>
                    <th class="text-right px-5 py-3 font-medium">نقش</th>
                    <th class="text-right px-5 py-3 font-medium">موجودی</th>
                    <th class="text-left px-5 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t border-ink/10">
                        <td class="px-5 py-4">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-ink/60">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs px-2 py-1 rounded {{ $user->role->value === 'admin' ? 'bg-seal/10 text-sealdark' : 'bg-ink/5 text-ink/50' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-4">{{ number_format($user->wallet->balance ?? 0) }} تومان</td>
                        <td class="px-5 py-4 text-left">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-sealdark hover:underline">جزئیات</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endsection
