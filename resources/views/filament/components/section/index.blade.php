@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\Components\SectionComponent\IconComponent;

    use function Filament\Support\is_slot_empty;
@endphp

@props([
    'afterHeader' => null,
    'aside' => false,
    'collapsed' => false,
    'collapseId' => null,
    'collapsible' => false,
    'compact' => false,
    'contained' => true,
    'contentBefore' => false,
    'description' => null,
    'divided' => false,
    'footer' => null,
    'hasContentEl' => true,
    'heading' => null,
    'headingTag' => 'h2',
    'icon' => null,
    'iconColor' => 'gray',
    'iconSize' => null,
    'persistCollapsed' => false,
    'secondary' => false,
])

@php
    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $hasDescription = filled((string) $description);
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);
    $hasHeader = $hasIcon || $hasHeading || $hasDescription || $collapsible || (! is_slot_empty($afterHeader));
@endphp

<div
    x-data="{
        isCollapsed: @if ($persistCollapsed) $persist(@js($collapsed)).as(`section-${@js($collapseId) ?? $el.id}-isCollapsed`) @else @js($collapsed) @endif,
    }"
    @if ($collapsible)
        x-on:collapse-section.window="if ($event.detail.id == @js($collapseId) ?? $el.id) isCollapsed = true"
        x-on:expand="isCollapsed = false"
        x-on:expand-section.window="if ($event.detail.id == @js($collapseId) ?? $el.id) isCollapsed = false"
        x-on:open-section.window="if ($event.detail.id == @js($collapseId) ?? $el.id) isCollapsed = false"
        x-on:toggle-section.window="if ($event.detail.id == @js($collapseId) ?? $el.id) isCollapsed = ! isCollapsed"
        x-bind:class="isCollapsed && 'fi-collapsed'"
    @endif
    {{
        $attributes->class([
            'border border-zinc-200 dark:border-zinc-800/80 rounded-xl bg-white dark:bg-zinc-900 shadow-sm',
            'p-3' => $compact,
            'p-4' => ! $compact,
            'fi-section',
        ])
    }}
>
    @if ($hasHeader)
        <header
            @if ($collapsible)
                x-on:click="if (! $event.target.closest('.fi-section-header-after-ctn')) isCollapsed = ! isCollapsed"
            @endif
            class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-3 cursor-pointer"
        >
            <div class="flex items-center gap-2">
                {{
                    \Filament\Support\generate_icon_html($icon, attributes: (new \Illuminate\View\ComponentAttributeBag)
                        ->class(['w-5 h-5 text-zinc-400']), size: $iconSize ?? IconSize::Large)
                }}

                @if ($hasHeading || $hasDescription)
                    <div>
                        @if ($hasHeading)
                            <{{ $headingTag }} class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $heading }}
                            </{{ $headingTag }}>
                        @endif

                        @if ($hasDescription)
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                {{ $description }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            @if (! is_slot_empty($afterHeader))
                <div class="flex items-center gap-2">
                    {{ $afterHeader }}
                </div>
            @endif

            @if ($collapsible)
                <flux:button
                    variant="ghost"
                    size="sm"
                    icon="chevron-up"
                    x-on:click.stop="isCollapsed = ! isCollapsed"
                    class="transition-transform duration-200"
                    x-bind:class="isCollapsed && 'rotate-180'"
                />
            @endif
        </header>
    @endif

    @if ((! is_slot_empty($slot)) || (! is_slot_empty($footer)))
        <div
            @if ($collapsible)
                x-show="! isCollapsed"
                x-bind:aria-expanded="(! isCollapsed).toString()"
            @endif
            class="space-y-3"
        >
            {{ $slot }}

            @if (! is_slot_empty($footer))
                <footer class="border-t border-zinc-200 dark:border-zinc-800 pt-3 mt-3">
                    {{ $footer }}
                </footer>
            @endif
        </div>
    @endif
</div>
