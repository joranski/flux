@props([
    'availableHeight' => null,
    'availableWidth' => null,
    'flip' => true,
    'maxHeight' => null,
    'offset' => 8,
    'placement' => null,
    'shift' => false,
    'size' => false,
    'sizePadding' => 16,
    'teleport' => false,
    'trigger' => null,
    'width' => null,
])

@php
    use Filament\Support\Enums\Width;

    $sizeConfig = collect([
        'availableHeight' => $availableHeight,
        'availableWidth' => $availableWidth,
        'padding' => $sizePadding,
    ])->filter()->toJson();

    if (is_string($width)) {
        $width = Width::tryFrom($width) ?? $width;
    }
@endphp

<div
    x-data="filamentDropdown"
    {{ $attributes->class(['fi-dropdown relative']) }}
>
    <div
        x-on:keyup.enter="toggle($event)"
        x-on:keyup.space="toggle($event)"
        x-on:mousedown="if ($event.button === 0) toggle($event)"
        {{ $trigger->attributes->class(['fi-dropdown-trigger cursor-pointer']) }}
    >
        {{ $trigger }}
    </div>

    @if (! \Filament\Support\is_slot_empty($slot))
        <div
            x-cloak
            x-float{{ $placement ? ".placement.{$placement}" : '' }}{{ $size ? '.size' : '' }}{{ $flip ? '.flip' : '' }}{{ $shift ? '.shift' : '' }}{{ $teleport ? '.teleport' : '' }}{{ $offset ? '.offset' : '' }}="{ offset: {{ $offset }}, {{ $size ? ('size: ' . $sizeConfig) : '' }} }"
            x-ref="panel"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave-end="opacity-0 scale-95"
            @if ($attributes->has('wire:key'))
                wire:ignore.self
                wire:key="{{ $attributes->get('wire:key') }}.panel"
            @endif
            @class([
                'fi-dropdown-panel z-50 p-1.5 rounded-lg shadow-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 focus:outline-hidden transition-all duration-100 ease-out',
                ($width instanceof Width) ? "fi-width-{$width->value}" : (is_string($width) ? $width : ''),
                'fi-scrollable overflow-y-auto' => $maxHeight || $size,
            ])
            @style([
                ('max-height: ' . e($maxHeight)) => $maxHeight,
            ])
        >
            {{ $slot }}
        </div>
    @endif
</div>
