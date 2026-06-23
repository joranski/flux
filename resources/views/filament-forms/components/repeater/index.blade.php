@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $cloneAction = $getAction($getCloneActionName());
    $collapseAllAction = $getAction($getCollapseAllActionName());
    $expandAllAction = $getAction($getExpandAllActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $hasItemNumbers = $hasItemNumbers();
    $hasItemHeaders = $hasItemHeaders();
    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
    $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();
    $persistCollapsed = $shouldPersistCollapsed();

    $key = $getKey();
    $statePath = $getStatePath();

    $itemLabelHeadingTag = $getHeadingTag();
    $isItemLabelTruncated = $isItemLabelTruncated();
    $labelBetweenItems = $getLabelBetweenItems();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'space-y-3',
                    'fi-fo-repeater',
                    'fi-collapsible' => $isCollapsible,
                ])
        }}
    >
        @if ($collapseAllActionIsVisible || $expandAllActionIsVisible)
            <div
                @class([
                    'flex items-center gap-2 justify-end',
                    'hidden' => count($items) < 2,
                ])
            >
                @if ($collapseAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                        class="inline-block"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')"
                        class="inline-block"
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if (count($items))
            <ul
                x-sortable
                {{
                    (new ComponentAttributeBag)
                        ->grid($getGridColumns())
                        ->merge([
                            'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                            'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                        ], escape: false)
                        ->class(['space-y-3'])
                }}
            >
                @foreach ($items as $itemKey => $item)
                    @php
                        $itemLabel = $getItemLabel($itemKey, $loop->index);
                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                        );
                        $cloneAction = $cloneAction(['item' => $itemKey]);
                        $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                        $deleteAction = $deleteAction(['item' => $itemKey]);
                        $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                        $moveDownAction = $moveDownAction(['item' => $itemKey])->disabled($loop->last);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                        $moveUpAction = $moveUpAction(['item' => $itemKey])->disabled($loop->first);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                        $hasItemHeader = $hasItemHeaders && ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || filled($itemLabel) || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions);
                    @endphp

                    <li
                        wire:ignore.self
                        wire:key="{{ $item->getLivewireKey() }}.item"
                        x-data="{
                            isCollapsed: @if ($persistCollapsed) $persist(@js($isCollapsed($item))).as(`repeater-${@js($key)}-${@js($itemKey)}-isCollapsed`) @else @js($isCollapsed($item)) @endif,
                        }"
                        x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                        x-on:expand="isCollapsed = false"
                        x-sortable-item="{{ $itemKey }}"
                        @class([
                            'border border-zinc-200 dark:border-zinc-800/80 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/50 shadow-xs p-3.5 pb-4',
                            'fi-fo-repeater-item',
                        ])
                        x-bind:class="{ 'fi-collapsed': isCollapsed }"
                    >
                        @if ($hasItemHeader)
                            <div
                                @if ($isCollapsible)
                                    x-on:click.stop="isCollapsed = !isCollapsed"
                                @endif
                                class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2.5 mb-3 cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible)
                                        <div class="flex items-center gap-1" x-on:click.stop>
                                            @if ($reorderActionIsVisible)
                                                <div class="cursor-move text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                                                    {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
                                                </div>
                                            @endif

                                            @if ($moveUpActionIsVisible)
                                                {{ $moveUpAction }}
                                            @endif

                                            @if ($moveDownActionIsVisible)
                                                {{ $moveDownAction }}
                                            @endif
                                        </div>
                                    @endif

                                    @if (filled($itemLabel))
                                        <{{ $itemLabelHeadingTag }}
                                            @class([
                                                'text-sm font-semibold text-zinc-900 dark:text-zinc-100',
                                                'truncate' => $isItemLabelTruncated,
                                            ])
                                        >
                                            {{ $itemLabel }}

                                            @if ($hasItemNumbers)
                                                #{{ $loop->iteration }}
                                            @endif
                                        </{{ $itemLabelHeadingTag }}>
                                    @endif
                                </div>

                                @if ($cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                                    <div class="flex items-center gap-1.5" x-on:click.stop>
                                        @foreach ($visibleExtraItemActions as $extraItemAction)
                                            {{ $extraItemAction(['item' => $itemKey]) }}
                                        @endforeach

                                        @if ($cloneActionIsVisible)
                                            {{ $cloneAction }}
                                        @endif

                                        @if ($deleteActionIsVisible)
                                            {{ $deleteAction }}
                                        @endif

                                        @if ($isCollapsible)
                                            <flux:button
                                                variant="ghost"
                                                size="sm"
                                                icon="chevron-up"
                                                x-on:click.stop="isCollapsed = !isCollapsed"
                                                class="transition-transform duration-200"
                                                x-bind:class="isCollapsed && 'rotate-180'"
                                            />
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div
                            x-show="! isCollapsed"
                            class="space-y-3"
                        >
                            {{ $item }}
                        </div>
                    </li>

                    @if (! $loop->last)
                        @if ($isAddable && $addBetweenAction(['afterItem' => $itemKey])->isVisible())
                            <li class="flex justify-center -my-2 relative z-10">
                                {{ $addBetweenAction(['afterItem' => $itemKey]) }}
                            </li>
                        @elseif (filled($labelBetweenItems))
                            <li class="flex items-center justify-center gap-4 my-2">
                                <div class="h-px bg-zinc-200 dark:bg-zinc-800 flex-1"></div>
                                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    {{ $labelBetweenItems }}
                                </span>
                                <div class="h-px bg-zinc-200 dark:bg-zinc-800 flex-1"></div>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <div
                @class([
                    'flex',
                    match ($addActionAlignment?->value ?? $addActionAlignment) {
                        'center' => 'justify-center',
                        'end' => 'justify-end',
                        default => 'justify-start',
                    },
                ])
            >
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
