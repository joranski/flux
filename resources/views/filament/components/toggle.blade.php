@props([
    'state',
])

<ui-switch
    x-data="{ state: {{ $state }} }"
    x-bind:checked="state"
    x-on:click="state = ! state"
    x-bind:data-checked="state ? '' : null"
    {{
        $attributes
            ->except(['checked', 'state', 'role'])
            ->merge(['class' => 'group h-5 w-8 min-w-8 relative inline-flex items-center outline-offset-2 rounded-full transition bg-zinc-800/15 [&[disabled]]:opacity-50 dark:bg-transparent dark:border dark:border-white/20 dark:[&[disabled]]:border-white/10 [print-color-adjust:exact] data-checked:bg-[var(--color-accent)] data-checked:border-0'])
    }}
    data-flux-control
    data-flux-switch
>
    <span class="size-3.5 rounded-full transition translate-x-[0.1875rem] dark:translate-x-[0.125rem] rtl:-translate-x-[0.1875rem] dark:rtl:-translate-x-[0.125rem] bg-white group-data-checked:translate-x-[0.9375rem] rtl:group-data-checked:-translate-x-[0.9375rem] dark:group-data-checked:translate-x-[0.9375rem] dark:rtl:group-data-checked:-translate-x-[0.9375rem] group-data-checked:bg-(--color-accent-foreground)"></span>
</ui-switch>
