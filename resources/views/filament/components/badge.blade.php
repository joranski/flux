@props([
    'color' => 'primary',
    'deleteButton' => null,
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => null,
    'iconSize' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'size' => null,
    'spaMode' => null,
    'tag' => 'span',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    // Map Filament badge colors to Flux colors
    $fluxColor = match ($color) {
        'primary', 'gray' => 'zinc',
        'success' => 'green',
        'warning' => 'amber',
        'danger' => 'red',
        'info' => 'blue',
        default => $color,
    };

    // Map size
    $fluxSize = match ($size?->value ?? $size) {
        'xs', 'sm' => 'sm',
        default => null,
    };

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

    $deleteButtonAttributes = $deleteButton instanceof \Illuminate\View\ComponentSlot
        ? $deleteButton->attributes
        : ($deleteButton instanceof \Illuminate\View\ComponentAttributeBag ? $deleteButton : null);

    $isDeletable = count($deleteButtonAttributes?->getAttributes() ?? []) > 0;
@endphp

<flux:badge
    :color="$fluxColor"
    :icon="$icon"
    :size="$fluxSize"
    {{
        $attributes
            ->except(['color', 'size', 'icon', 'deleteButton', 'disabled', 'form', 'formId', 'href', 'iconAlias', 'iconPosition', 'iconSize', 'keyBindings', 'loadingIndicator', 'spaMode', 'tag', 'target', 'tooltip', 'type'])
    }}
>
    {{ $slot }}

    @if ($isDeletable)
        @php
            $originalAttributes = $attributes;
            $attributes = $deleteButtonAttributes->class(['fi-badge-delete-btn']);
        @endphp

        <flux:badge.close {{ $attributes }} />

        @php
            $attributes = $originalAttributes;
        @endphp
    @endif
</flux:badge>


