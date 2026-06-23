@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\Size;
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
    'labelSrOnly' => false,
    'loadingIndicator' => true,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'a',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
    'weight' => null,
])

@php
    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;
    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

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

<flux:link
    :href="$href"
    :target="$target"
    :disabled="$disabled"
    {{
        $attributes
            ->merge([
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->class([
                'fi-disabled' => $disabled,
            ])
    }}
>
    @if ($icon && $iconPosition !== IconPosition::After)
        <flux:icon :icon="$icon" class="mr-1 inline-block h-4 w-4 align-text-bottom" />
    @endif

    @if (! $labelSrOnly)
        {{ $slot }}
    @endif

    @if ($icon && $iconPosition === IconPosition::After)
        <flux:icon :icon="$icon" class="ml-1 inline-block h-4 w-4 align-text-bottom" />
    @endif

    @if (filled($badge))
        <flux:badge size="sm" class="ml-1 shrink-0">
            {{ $badge }}
        </flux:badge>
    @endif
</flux:link>
