@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\DescriptionComponent;
    use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent;
    use Illuminate\View\ComponentAttributeBag;

    $chartColor = $getChartColor() ?? 'gray';
    $descriptionColor = $getDescriptionColor() ?? 'gray';
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $chartDataChecksum = $generateChartDataChecksum();

    $filter = $getFilter();
    $options = $getOptions();

    $hasFilter = filled($filter) || filled($options);
@endphp

<{!! $tag !!}
    @if ($url)
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'relative flex flex-col justify-between border border-zinc-200 dark:border-zinc-800/80 rounded-xl bg-white dark:bg-zinc-900 shadow-sm p-6 overflow-hidden',
            ])
    }}
>
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
            <div class="flex items-center gap-2">
                @if ($getIcon())
                    {{ \Filament\Support\generate_icon_html($getIcon(), attributes: (new ComponentAttributeBag)->class(['w-4 h-4 text-zinc-400 dark:text-zinc-500'])) }}
                @endif
                <span>{{ $getLabel() }}</span>
            </div>
            
            @if ($hasFilter)
                <flux:select
                    variant="custom"
                    size="sm"
                    wire:model.live="filter"
                    class="w-32"
                >
                    @foreach ($options as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </flux:select>
            @endif
        </div>

        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-50 tracking-tight">
            {{ $getValue() }}
        </div>

        @if ($description = $getDescription())
            @php
                $descColorClasses = match ($descriptionColor) {
                    'success' => 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/30 border-green-100 dark:border-green-900/30',
                    'danger' => 'text-red-650 dark:text-red-400 bg-red-50 dark:bg-red-950/30 border-red-100 dark:border-red-900/30',
                    'warning' => 'text-amber-655 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 border-amber-100 dark:border-amber-900/30',
                    'info' => 'text-blue-650 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/30 border-blue-100 dark:border-blue-900/30',
                    default => 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/30 border-zinc-100 dark:border-zinc-700/30',
                };
            @endphp
            <div
                {{ (new ComponentAttributeBag)->class(['inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-md border ' . $descColorClasses]) }}
            >
                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::Before, 'before']))
                    {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['w-3.5 h-3.5'])) }}
                @endif

                <span>
                    {{ $description }}
                </span>

                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::After, 'after']))
                    {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['w-3.5 h-3.5'])) }}
                @endif
            </div>
        @endif
    </div>

    @if ($chart = $getChart())
        <div x-data="{ statsOverviewStatChart() {} }" class="absolute bottom-0 inset-x-0 h-10 overflow-hidden rounded-b-xl">
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/stat/chart', 'filament/widgets') }}"
                x-data="statsOverviewStatChart({
                            dataChecksum: @js($chartDataChecksum),
                            labels: @js(array_keys($chart)),
                            values: @js(array_values($chart)),
                        })"
                {{ (new ComponentAttributeBag)->color(StatsOverviewWidgetStatChartComponent::class, $chartColor)->class(['w-full h-full']) }}
                style="height: 40px;"
            >
                <canvas x-ref="canvas" class="w-full h-full"></canvas>

                <span
                    x-ref="backgroundColorElement"
                    class="opacity-10 dark:opacity-20"
                    style="color: var(--chart-color, #a1a1aa);"
                ></span>

                <span
                    x-ref="borderColorElement"
                    style="color: var(--chart-color, #71717a);"
                ></span>
            </div>
        </div>
    @endif
</{!! $tag !!}>
