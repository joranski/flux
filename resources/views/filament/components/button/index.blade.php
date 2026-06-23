@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
    use Filament\Support\View\Components\BadgeComponent;
    use Filament\Support\View\Components\ButtonComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeSize' => Size::ExtraSmall,
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
    'iconSize' => null,
    'keyBindings' => null,
    'labeledFrom' => null,
    'labelSrOnly' => false,
    'loadingIndicator' => true,
    'outlined' => false,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $size instanceof Size) {
        $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
    }

    if (! $badgeSize instanceof Size) {
        $badgeSize = filled($badgeSize) ? (Size::tryFrom($badgeSize) ?? $badgeSize) : null;
    }

    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasFormProcessingLoadingIndicator = $type === 'submit' && filled($form);
    $hasLoadingIndicator = filled($wireTarget) || $hasFormProcessingLoadingIndicator;

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);

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

    // Map Filament colors/variants to Flux variants/colors
    $fluxVariant = 'outline';
    $fluxColor = null;

    if ($color === 'danger') {
        $fluxVariant = 'danger';
    } elseif ($color === 'primary' && ! $outlined) {
        $fluxVariant = 'primary';
    } elseif ($outlined) {
        $fluxVariant = 'outline';
        $fluxColor = match ($color) {
            'primary' => null,
            'gray' => null,
            default => $color,
        };
    } else {
        // e.g. gray, info, warning, success
        $fluxVariant = 'filled';
        $fluxColor = match ($color) {
            'gray' => null,
            'success' => 'green',
            'warning' => 'amber',
            'info' => 'blue',
            default => $color,
        };
    }

    // Map size
    $fluxSize = match ($size?->value ?? $size) {
        'xs' => 'xs',
        'sm' => 'xs',
        'md' => 'xs',
        'lg' => 'sm',
        'xl' => 'base',
        default => 'xs',
    };

    // Map icon position
    $iconLeading = $iconPosition !== IconPosition::After ? $icon : null;
    $iconTrailing = $iconPosition === IconPosition::After ? $icon : null;
@endphp

@if ($labeledFrom)
    <x-filament::icon-button
        :badge="$badge"
        :badge-color="$badgeColor"
        :badge-size="$badgeSize"
        :color="$color"
        :disabled="$disabled"
        :form="$form"
        :form-id="$formId"
        :href="$href"
        :icon="$icon"
        :icon-alias="$iconAlias"
        :icon-size="$iconSize"
        :key-bindings="$keyBindings"
        :label="$slot"
        :loading-indicator="$loadingIndicator"
        :size="$size"
        :spa-mode="$spaMode"
        :tag="$tag"
        :target="$target"
        :tooltip="$tooltip"
        :type="$type"
        :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    />
@else
    @if ($tag === 'label')
        @php
            $fluxClasses = "relative items-center font-medium justify-center gap-2 whitespace-nowrap inline-flex cursor-pointer select-none transition-colors duration-150 fi-color-{$color}";
            
            $fluxClasses .= match ($fluxSize) {
                'xs' => ' h-6 text-xs rounded-md px-2',
                'sm' => ' h-8 text-sm rounded-md px-3',
                default => ' h-10 text-sm rounded-lg ' . ($iconLeading || $iconTrailing ? 'ps-3 pe-3' : 'px-4'),
            };
            
            if ($disabled) {
                $fluxClasses .= ' opacity-75 cursor-default pointer-events-none';
            }
        @endphp
        <label
            {{
                $attributes
                    ->class([$fluxClasses])
            }}
        >
            @if ($iconLeading)
                <flux:icon :icon="$iconLeading" class="size-4 shrink-0" />
            @endif
            
            @if (! $labelSrOnly)
                <span>{{ $slot }}</span>
            @endif

            @if ($iconTrailing)
                <flux:icon :icon="$iconTrailing" class="size-4 shrink-0" />
            @endif

            @if (filled($badge))
                <flux:badge size="sm" class="ml-1.5 shrink-0">
                    {{ $badge }}
                </flux:badge>
            @endif
        </label>
    @else
        <flux:button
            :variant="$fluxVariant"
            :color="$fluxColor"
            :size="$fluxSize"
            :icon="$iconLeading"
            :icon-trailing="$iconTrailing"
            :href="$href"
            :target="$target"
            :disabled="$disabled"
            :type="$tag === 'button' ? $type : null"
            {{
                $attributes
                    ->merge([
                        'form' => $formId,
                        'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                        'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                    ], escape: false)
                    ->class([
                        'fi-disabled' => $disabled,
                    ])
            }}
        >
            @if (! $labelSrOnly)
                {{ $slot }}
            @endif

            @if (filled($badge))
                <flux:badge size="sm" class="ml-1.5 shrink-0">
                    {{ $badge }}
                </flux:badge>
            @endif
        </flux:button>
    @endif
@endif
