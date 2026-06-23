@php
    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();

    $radioGroupAttributes = (new \Illuminate\View\ComponentAttributeBag)
        ->merge([
            'id' => $id,
            $applyStateBindingModifiers('wire:model') => $statePath,
        ], escape: false);
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <flux:radio.group :attributes="$radioGroupAttributes">
        @foreach ($getOptions() as $value => $label)
            @php
                $itemAttributes = (new \Illuminate\View\ComponentAttributeBag)
                    ->merge([
                        'value' => $value,
                        'label' => $label,
                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                    ], escape: false);

                if ($hasDescription($value)) {
                    $itemAttributes = $itemAttributes->merge(['description' => $getDescription($value)], escape: false);
                }
            @endphp
            <flux:radio :attributes="$itemAttributes" />
        @endforeach
    </flux:radio.group>
</x-dynamic-component>
