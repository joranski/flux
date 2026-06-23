@props([
    'contained' => true,
    'label' => null,
    'labelHidden' => false,
    'required' => false,
])

<flux:fieldset
    :legend="(! $labelHidden) ? $label : null"
    {{
        $attributes->class([
            'border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 bg-white dark:bg-zinc-900 shadow-sm' => $contained,
        ])
    }}
>
    {{ $slot }}
</flux:fieldset>
