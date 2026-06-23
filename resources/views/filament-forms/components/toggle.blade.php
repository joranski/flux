@php
    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isRequired = $isRequired();
    $isConcealed = $isConcealed();

    $attributes = (new \Illuminate\View\ComponentAttributeBag)
        ->merge([
            'id' => $id,
            'disabled' => $isDisabled,
            'required' => $isRequired && (! $isConcealed),
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false)
        ->merge($getExtraAttributes(), escape: false)
        ->merge($getExtraAlpineAttributes(), escape: false);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
            <flux:switch {{ $attributes }} />
        </x-slot>
    @else
        <flux:switch {{ $attributes }} />
    @endif
</x-dynamic-component>
