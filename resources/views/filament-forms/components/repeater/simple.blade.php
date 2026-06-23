@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $cloneAction = $getAction($getCloneActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $key = $getKey();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['space-y-3'])
        }}
    >
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
                        ->class(['space-y-2'])
                }}
            >
                @foreach ($items as $itemKey => $item)
                    @php
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
                    @endphp

                    <li
                        wire:key="{{ $item->getLivewireKey() }}.item"
                        x-sortable-item="{{ $itemKey }}"
                        class="flex items-center gap-3 border border-zinc-200 dark:border-zinc-800/80 rounded-xl bg-white dark:bg-zinc-900/50 p-3 shadow-xs"
                    >
                        <div class="flex-1">
                            {{ $item }}
                        </div>

                        @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $visibleExtraItemActions)
                            <div class="flex items-center gap-1.5" x-on:click.stop>
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

                                @foreach ($visibleExtraItemActions as $extraItemAction)
                                    {{ $extraItemAction(['item' => $itemKey]) }}
                                @endforeach

                                @if ($cloneActionIsVisible)
                                    {{ $cloneAction }}
                                @endif

                                @if ($deleteActionIsVisible)
                                    {{ $deleteAction }}
                                @endif
                            </div>
                        @endif
                    </li>
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
