@php
    use Filament\Support\Facades\FilamentAsset;
    use Guava\Calendar\Enums\Context;

    use function Filament\Support\generate_loading_indicator_html;
@endphp

<div x-ignore
     x-load
     x-load-src="{{ FilamentAsset::getAlpineComponentSrc('calendar-context-menu', 'guava/calendar') }}"
     x-data="calendarContextMenu({
            getContextMenuActionsUsing: async (context, data) => {
                return await $wire.getContextMenuActionsUsing(context, data)
            },
         })"
     calendar-context-menu
     class="absolute top-0 left-0 z-30"
>
    <div x-bind="menu"
         x-transition:enter-start="fi-opacity-0" x-transition:leave-end="fi-opacity-0"
         class="absolute w-screen max-w-xs divide-y divide-zinc-200 dark:divide-zinc-800/50 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-md transition"
    >
        <div wire:loading.flex wire:target="getContextMenuActionsUsing" class="w-full flex items-center justify-center p-2">{{generate_loading_indicator_html()}}</div>
        <x-filament::dropdown.list>
            <template x-for="action in actions">
                <div x-html="action"></div>
            </template>
        </x-filament::dropdown.list>
    </div>
</div>
