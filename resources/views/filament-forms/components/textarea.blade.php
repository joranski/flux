@php
    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
    $isRequired = $isRequired();
    $isConcealed = $isConcealed();
    $rows = $shouldAutosize() ? 'auto' : ($getRows() ?? 4);

    $textareaAttributes = $getExtraInputAttributeBag()
        ->merge([
            'id' => $id,
            'placeholder' => $placeholder,
            'disabled' => $isDisabled,
            'readonly' => $isReadOnly(),
            'required' => $isRequired && (! $isConcealed),
            'rows' => $rows,
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false)
        ->merge($getExtraAlpineAttributeBag()->getAttributes(), escape: false);
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
>
    <flux:textarea size="sm" :attributes="$textareaAttributes" />
</x-dynamic-component>
