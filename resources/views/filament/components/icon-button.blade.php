@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Enums\Size;
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
    'iconSize' => null,
    'keyBindings' => null,
    'label' => null,
    'loadingIndicator' => true,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@if (filled($badge))
    {!! view()->file(
        base_path('vendor/filament/support/resources/views/components/icon-button.blade.php'),
        get_defined_vars(),
    )->render() !!}
@else
    @php
        if (! $size instanceof Size) {
            $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
        }

        if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
            $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
        }

        $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;
        $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

        if ($hasLoadingIndicator) {
            $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
        }

        $fluxSize = match ($size?->value ?? $size) {
            'xs' => 'xs',
            'sm' => 'xs',
            'md' => 'xs',
            'lg' => 'sm',
            'xl' => 'base',
            default => 'xs',
        };

        $fluxVariant = 'ghost';
        if ($color === 'danger') {
            $fluxVariant = 'danger';
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

    <flux:button
        :variant="$fluxVariant"
        :size="$fluxSize"
        :icon="$icon"
        :href="$href"
        :target="$target"
        :disabled="$disabled"
        :type="$tag === 'button' ? $type : null"
        square
        {{
            $attributes
                ->merge([
                    'form' => $formId,
                    'aria-label' => $label,
                    'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->class([
                    'fi-disabled' => $disabled,
                ])
        }}
    ></flux:button>
@endif
