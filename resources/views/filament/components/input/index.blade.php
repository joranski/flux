@props([
    'inlinePrefix' => false,
    'inlineSuffix' => false,
])

<input
    {{
        $attributes->class([
            'w-full bg-transparent border-0 px-3 py-2 text-base sm:text-sm h-10 leading-[1.375rem] text-zinc-800 dark:text-white placeholder-zinc-400 focus:ring-0 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed',
            'ps-1' => $inlinePrefix,
            'pe-1' => $inlineSuffix,
        ])
    }}
/>
