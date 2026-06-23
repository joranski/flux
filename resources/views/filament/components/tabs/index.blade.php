@props([
    'contained' => false,
    'label' => null,
    'vertical' => false,
])

<flux:tabs
    {{
        $attributes
            ->merge([
                'aria-label' => $label,
            ])
    }}
>
    {{ $slot }}
</flux:tabs>
