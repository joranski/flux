@php
    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
    $type = $getType();

    // Map icons
    $prefixIcon = $getPrefixIcon();
    if ($prefixIcon) {
        if ($prefixIcon instanceof \BackedEnum) {
            $prefixIcon = $prefixIcon->value;
        } elseif (is_object($prefixIcon) && method_exists($prefixIcon, 'toHtml')) {
            $prefixIcon = $prefixIcon->toHtml();
        } elseif (is_object($prefixIcon) && method_exists($prefixIcon, '__toString')) {
            $prefixIcon = (string) $prefixIcon;
        }

        if (is_string($prefixIcon)) {
            $prefixIcon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $prefixIcon);
            if (str_starts_with($prefixIcon, 'o-')) {
                $prefixIcon = substr($prefixIcon, 2);
            } elseif (str_starts_with($prefixIcon, 'm-')) {
                $prefixIcon = substr($prefixIcon, 2);
            }
            if (! \Flux\Flux::componentExists("icon.{$prefixIcon}")) {
                $prefixIcon = null;
            }
        }
    }
    $suffixIcon = $getSuffixIcon();
    if ($suffixIcon) {
        if ($suffixIcon instanceof \BackedEnum) {
            $suffixIcon = $suffixIcon->value;
        } elseif (is_object($suffixIcon) && method_exists($suffixIcon, 'toHtml')) {
            $suffixIcon = $suffixIcon->toHtml();
        } elseif (is_object($suffixIcon) && method_exists($suffixIcon, '__toString')) {
            $suffixIcon = (string) $suffixIcon;
        }

        if (is_string($suffixIcon)) {
            $suffixIcon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $suffixIcon);
            if (str_starts_with($suffixIcon, 'o-')) {
                $suffixIcon = substr($suffixIcon, 2);
            } elseif (str_starts_with($suffixIcon, 'm-')) {
                $suffixIcon = substr($suffixIcon, 2);
            }
            if (! \Flux\Flux::componentExists("icon.{$suffixIcon}")) {
                $suffixIcon = null;
            }
        }
    }

    $prefixLabel = $getPrefixLabel();
    $suffixLabel = $getSuffixLabel();
    $hasGroup = filled($prefixLabel) || filled($suffixLabel);

    // Set up password reveal if revealable
    $isPasswordRevealable = $isPasswordRevealable();
    if ($isPasswordRevealable) {
        $type = 'password';
    }

    $inputAttributes = $getExtraInputAttributeBag()
        ->merge([
            'id' => $id,
            'placeholder' => $placeholder,
            'disabled' => $isDisabled,
            'readonly' => $isReadOnly(),
            'required' => $isRequired() && (! $isConcealed()),
            'step' => $getStep(),
            'min' => (! $isConcealed()) ? $getMinValue() : null,
            'max' => (! $isConcealed()) ? $getMaxValue() : null,
            'minlength' => (! $isConcealed()) ? $getMinLength() : null,
            'maxlength' => (! $isConcealed()) ? $getMaxLength() : null,
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
>
    @if ($hasGroup)
        <flux:input.group>
            @if (filled($prefixLabel))
                <flux:input.group.prefix>{{ $prefixLabel }}</flux:input.group.prefix>
            @endif

            <flux:input
                size="sm"
                :type="$type"
                :icon="$prefixIcon"
                :icon-trailing="$suffixIcon"
                :viewable="$isPasswordRevealable"
                :attributes="$inputAttributes"
            />

            @if (filled($suffixLabel))
                <flux:input.group.suffix>{{ $suffixLabel }}</flux:input.group.suffix>
            @endif
        </flux:input.group>
    @else
        <flux:input
            size="sm"
            :type="$type"
            :icon="$prefixIcon"
            :icon-trailing="$suffixIcon"
            :viewable="$isPasswordRevealable"
            :attributes="$inputAttributes"
        />
    @endif
</x-dynamic-component>
