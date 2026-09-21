@props(['status'])

@php
    $colorMap = [
        'green' => 'bg-approve/10 text-approve',
        'red' => 'bg-reject/10 text-reject',
        'yellow' => 'bg-flagged/10 text-flagged',
        'gray' => 'bg-ink/10 text-ink/60',
        'blue' => 'bg-sealdark/10 text-sealdark',
    ];
    $classes = $colorMap[$status->color()] ?? $colorMap['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-medium $classes"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    {{ $status->label() }}
</span>
