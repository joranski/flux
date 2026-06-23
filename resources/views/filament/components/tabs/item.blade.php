@php
    use Filament\Support\Enums\IconPosition;
@endphp

@props([
    'active' => false,
    'alpineActive' => null,
    'alpineDeferredBadgeData' => null,
    'alpineDeferredBadgeLoading' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'badgeIcon' => null,
    'badgeIconPosition' => IconPosition::Before,
    'href' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconPosition' => IconPosition::Before,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    $hasAlpineActiveClasses = filled($alpineActive);

    if ($icon) {
        if ($icon instanceof \BackedEnum) {
            $icon = $icon->value;
        } elseif (is_object($icon) && method_exists($icon, 'toHtml')) {
            $icon = $icon->toHtml();
        } elseif (is_object($icon) && method_exists($icon, '__toString')) {
            $icon = (string) $icon;
        }

        if (is_string($icon)) {
            $icon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $icon);
            if (str_starts_with($icon, 'o-')) {
                $icon = substr($icon, 2);
            } elseif (str_starts_with($icon, 'm-')) {
                $icon = substr($icon, 2);
            }
            if (! \Flux\Flux::componentExists("icon.{$icon}")) {
                $icon = null;
            }
        }
    }
@endphp

<flux:tab
    :href="$href"
    :target="$target"
    :selected="$active"
    {{
        $attributes
            ->when($hasAlpineActiveClasses, fn ($atts) => $atts->merge([
                'x-bind:selected' => $alpineActive,
            ], escape: false))
    }}
>
    @if ($icon && $iconPosition !== IconPosition::After)
        <flux:icon :icon="$icon" class="mr-1 inline-block h-4 w-4 align-text-bottom" />
    @endif

    <span>{{ $slot }}</span>

    @if ($icon && $iconPosition === IconPosition::After)
        <flux:icon :icon="$icon" class="ml-1 inline-block h-4 w-4 align-text-bottom" />
    @endif

    @if (filled($badge))
        <flux:badge size="sm" class="ml-1.5 shrink-0">
            {{ $badge }}
        </flux:badge>
    @endif
</flux:tab>
