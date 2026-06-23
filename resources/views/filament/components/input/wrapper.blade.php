@props([
    'alpineDisabled' => null,
    'alpineValid' => null,
    'disabled' => false,
    'inlinePrefix' => false,
    'inlineSuffix' => false,
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'prefixIconColor' => 'gray',
    'prefixIconAlias' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
    'suffixIconColor' => 'gray',
    'suffixIconAlias' => null,
    'valid' => true,
])

@php
    use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;

    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );

    $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefix);
    $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffix);

    $hasAlpineDisabledClasses = filled($alpineDisabled);
    $hasAlpineValidClasses = filled($alpineValid);
    $hasAlpineClasses = $hasAlpineDisabledClasses || $hasAlpineValidClasses;

    $wireTarget = $attributes->whereStartsWith(['wire:target'])->first();

    $hasLoadingIndicator = filled($wireTarget);

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget, ENT_QUOTES);
    }

    $hasFocusInputListener = $attributes->has('x-on:focus-input.stop');
    $canClickPrefixAffix = $hasFocusInputListener && ($prefixIcon || filled($prefix));
    $canClickSuffixAffix = $hasFocusInputListener && ($suffixIcon || filled($suffix));
@endphp

<div
    @if ($hasAlpineClasses)
        x-bind:class="{
            {{ $hasAlpineDisabledClasses ? "'opacity-50 cursor-not-allowed': {$alpineDisabled}," : null }}
            {{ $hasAlpineValidClasses ? "'border-red-500 ring-red-100 dark:ring-red-950': ! ({$alpineValid})," : null }}
        }"
    @endif
    {{
        $attributes
            ->except(['wire:target', 'tabindex'])
            ->class([
                'flex items-center w-full border rounded-lg bg-white dark:bg-white/10 shadow-sm border-zinc-200 border-b-zinc-300/80 dark:border-white/10 transition focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 dark:focus-within:border-indigo-500/50 dark:focus-within:ring-indigo-500/20',
                'opacity-75 bg-zinc-50 dark:bg-zinc-950 cursor-not-allowed' => (! $hasAlpineClasses) && $disabled,
                'border-red-500 ring-red-100 dark:ring-red-950' => (! $hasAlpineClasses) && (! $valid),
            ])
    }}
>
    @if ($hasPrefix || $hasLoadingIndicator)
        <div
            @if (! $hasPrefix)
                wire:loading.delay.{{ config('filament.livewire_loading_delay', 'default') }}.flex
                wire:target="{{ $loadingIndicatorTarget }}"
                wire:key="{{ \Illuminate\Support\Str::random() }}"
            @endif
            @if ($canClickPrefixAffix)
                x-on:click="$dispatch('focus-input')"
            @endif
            @class([
                'flex items-center gap-2 ps-3 pe-2 border-r border-zinc-200 dark:border-zinc-800 text-zinc-400 text-sm shrink-0',
                'fi-inline' => $inlinePrefix,
            ])
        >
            @if (count($prefixActions))
                <div class="flex items-center gap-1">
                    @foreach ($prefixActions as $prefixAction)
                        {{ $prefixAction }}
                    @endforeach
                </div>
            @endif

            {{
                \Filament\Support\generate_icon_html($prefixIcon, $prefixIconAlias, (new \Illuminate\View\ComponentAttributeBag)
                    ->merge([
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                        'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                    ], escape: false)
                    ->class(['w-4 h-4 text-zinc-400'])
                    ->color(IconComponent::class, $prefixIconColor))
            }}

            @if ($hasLoadingIndicator)
                {{
                    \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => $hasPrefix,
                        'wire:target' => $hasPrefix ? $loadingIndicatorTarget : null,
                    ]))->class(['w-4 h-4 text-zinc-400 animate-spin']))
                }}
            @endif

            @if (filled($prefix))
                <span>
                    {{ $prefix }}
                </span>
            @endif
        </div>
    @endif

    <div
        @if ($hasLoadingIndicator && (! $hasPrefix))
            @if ($inlinePrefix)
                wire:loading.delay.{{ config('filament.livewire_loading_delay', 'default') }}.class.remove="ps-3"
            @endif
            wire:target="{{ $loadingIndicatorTarget }}"
        @endif
        class="flex-1 min-w-0"
    >
        {{ $slot }}
    </div>

    @if ($hasSuffix)
        <div
            @if ($canClickSuffixAffix)
                x-on:click="$dispatch('focus-input')"
            @endif
            class="flex items-center gap-2 ps-2 pe-3 border-l border-zinc-200 dark:border-zinc-800 text-zinc-400 text-sm shrink-0"
        >
            @if (filled($suffix))
                <span>
                    {{ $suffix }}
                </span>
            @endif

            {{
                \Filament\Support\generate_icon_html($suffixIcon, $suffixIconAlias, (new \Illuminate\View\ComponentAttributeBag)
                    ->merge([
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                        'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                    ], escape: false)
                    ->class(['w-4 h-4 text-zinc-400'])
                    ->color(IconComponent::class, $suffixIconColor))
            }}

            @if (count($suffixActions))
                <div class="flex items-center gap-1">
                    @foreach ($suffixActions as $suffixAction)
                        {{ $suffixAction }}
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
