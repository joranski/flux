@php
    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();
    $attributes = $attributes
        ->merge([
            'autofocus' => $isAutofocused(),
            'disabled' => $isDisabled(),
            'id' => $getId(),
            'required' => $isRequired() && (! $isConcealed()),
            'wire:loading.attr' => 'disabled',
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false)
        ->merge($getExtraAttributes(), escape: false)
        ->merge($getExtraInputAttributes(), escape: false);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
            <flux:checkbox {{ $attributes }} />
        </x-slot>
    @else
        <flux:checkbox {{ $attributes }} />
    @endif
</x-dynamic-component>
