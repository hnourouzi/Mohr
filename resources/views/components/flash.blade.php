@if (session('success'))
    <div class="mb-6 px-4 py-3 rounded bg-approve/10 text-approve text-sm border-r-2 border-approve">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 px-4 py-3 rounded bg-reject/10 text-reject text-sm border-r-2 border-reject">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
