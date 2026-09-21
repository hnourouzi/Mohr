@props(['label', 'value', 'accent' => false])

<div class="border border-ink/10 rounded p-5 {{ $accent ? 'bg-ink text-paper' : 'bg-white' }}">
    <p class="text-sm {{ $accent ? 'text-paper/60' : 'text-ink/50' }} mb-2">{{ $label }}</p>
    <p class="text-2xl font-bold {{ $accent ? 'text-seal' : '' }}">{{ $value }}</p>
</div>
