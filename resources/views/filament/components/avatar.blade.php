@props([
    'circular' => true,
    'size' => 'md',
])

@php
    $fluxSize = match ($size) {
        'sm' => 'sm',
        'md' => 'md',
        'lg' => 'lg',
        default => 'md',
    };
@endphp

<flux:avatar
    :circle="$circular"
    :square="! $circular"
    :size="$fluxSize"
    {{ $attributes }}
/>

