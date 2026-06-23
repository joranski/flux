@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
    use Filament\Support\View\Components\BadgeComponent;
    use Filament\Support\View\Components\DropdownComponent\ItemComponent;
    use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'alpineDeferredBadgeData' => null,
    'alpineDeferredBadgeLoading' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeTooltip' => null,
    'color' => 'gray',
    'disabled' => false,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => null,
    'iconSize' => null,
    'image' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
])

@php
    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconColor ??= $color;

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }

    $hasDeferredBadge = filled($alpineDeferredBadgeData);
    $hasTooltip = filled($tooltip);

    // Map Flux icon
    if ($icon) {
        if ($icon instanceof \BackedEnum) {
            $icon = $icon->value;
        }
        if (is_string($icon)) {
            $icon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $icon);
            if (! \Flux\Flux::componentExists("icon.{$icon}")) {
                $icon = null;
            }
        }
    }
@endphp

{!! ($tag === 'form') ? ('<form ' . $attributes->only(['action', 'class', 'method', 'wire:submit'])->toHtml() . '>') : '' !!}

@if ($tag === 'form')
    @csrf
@endif

<{{ ($tag === 'form') ? 'button' : $tag }}
    @if (($tag === 'a') && (! ($disabled && $hasTooltip)))
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings)
        x-bind:id="$id('key-bindings')"
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id)?.click()"
    @endif
    @if ($hasTooltip)
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
            allowHTML: @js($tooltip instanceof \Illuminate\Contracts\Support\Htmlable),
        }"
    @endif
    {{
        $attributes
            ->when(
                $tag === 'form',
                fn (ComponentAttributeBag $attributes) => $attributes->except(['action', 'class', 'method', 'wire:submit']),
            )
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'disabled' => $disabled && blank($tooltip),
                'type' => match ($tag) {
                    'button' => 'button',
                    'form' => 'submit',
                    default => null,
                },
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->when(
                $disabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'flex items-center gap-2 px-2.5 py-1.5 w-full text-start text-sm font-medium rounded-md transition duration-150 disabled:opacity-50 disabled:pointer-events-none cursor-pointer',
                'text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 hover:text-zinc-950 dark:hover:text-white' => $color === 'gray',
                'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20' => $color === 'danger',
                'text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/20' => $color === 'primary',
                'fi-disabled' => $disabled,
            ])
    }}
>
    @if ($icon)
        <flux:icon :icon="$icon" class="w-4 h-4 shrink-0" />
    @endif

    @if ($image)
        <div
            class="w-5 h-5 rounded-full bg-cover bg-center shrink-0"
            style="background-image: url('{{ $image }}')"
            @if ($hasLoadingIndicator)
                wire:loading.remove.delay.{{ config('filament.livewire_loading_delay', 'default') }}
                wire:target="{{ $loadingIndicatorTarget }}"
            @endif
        ></div>
    @endif

    @if ($hasLoadingIndicator)
        @php
            $loadingAttributes = new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ]);
        @endphp
        <flux:icon
            icon="loading"
            :attributes="$loadingAttributes"
            class="w-4 h-4 text-zinc-400 animate-spin shrink-0"
        />
    @endif

    <span class="flex-1 min-w-0">
        {{ $slot }}
    </span>

    @if (filled($badge))
        @if ($badge instanceof \Illuminate\View\ComponentSlot)
            {{ $badge }}
        @else
            <flux:badge size="sm" :color="$badgeColor">
                {{ $badge }}
            </flux:badge>
        @endif
    @elseif ($hasDeferredBadge)
        <span
            x-show="{{ $alpineDeferredBadgeLoading }}"
            x-cloak
            class="fi-dropdown-list-item-badge-placeholder"
        >
            {{ \Filament\Support\generate_loading_indicator_html(size: \Filament\Support\Enums\IconSize::Small) }}
        </span>

        <template
            x-if="
                ! {{ $alpineDeferredBadgeLoading }} &&
                    {{ $alpineDeferredBadgeData }}?.badge != null
            "
        >
            <flux:badge size="sm" x-text="{{ $alpineDeferredBadgeData }}?.badge" />
        </template>
    @endif
</{{ ($tag === 'form') ? 'button' : $tag }}>

{!! ($tag === 'form') ? '</form>' : '' !!}
