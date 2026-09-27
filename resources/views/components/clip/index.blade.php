@props([
    'record',
    'type' => 'card', // 'card' or 'row'
    'title' => null,
    'subtitle' => null,
    'href' => null,
    'image' => null,
    'actions' => null,
    'pivot' => null,
    'isSelected' => false,
])
@php
    $href = $href ?? \Joranski\Flux\Clip::defaultHref($record);
    $title = $title ?? ($record->name ?? $record->code ?? $record->number ?? $record->domain ?? 'Record #' . $record->id);
    $isActive = $record->active ?? $record->is_active ?? true;
    // Inside an explorer list the row itself is wire:click-able; a left click on the title
    // must not trigger a full page load on top of that. The href stays for middle-click / new tab.
    $preventTitleNavigation = $attributes->has('wire:click');
@endphp
<x-joranski-flux::clip.wrapper :type="$type" :isSelected="$isSelected" {{ $attributes }}>
    @if($type === 'card')
        <div class="space-y-3 w-full">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1 flex items-center gap-3">
                    @if($image)
                        <img src="{{ $image }}" class="w-10 h-10 object-contain rounded border bg-white shrink-0" />
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-zinc-800 dark:text-zinc-200 truncate">
                            @if($href)
                                <a href="{{ $href }}" @if($preventTitleNavigation) x-on:click.prevent="" @endif class="hover:underline text-amber-600 dark:text-amber-400">
                                    {{ $title }}
                                </a>
                            @else
                                {{ $title }}
                            @endif
                        </h4>
                        @if($subtitle)
                            <p class="text-[10px] text-zinc-400 font-medium truncate mt-0.5">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                @if($actions) {{ $actions }} @endif
            </div>
            
            @if($pivot)
                <div class="text-xs text-zinc-500">
                    {{ $pivot }}
                </div>
            @endif

            <div class="text-xs text-zinc-400 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800/60 pt-2.5">
                <span>Status:</span>
                @if($isActive)
                    <span class="text-green-600 font-semibold">Active</span>
                @else
                    <span class="text-zinc-500 font-medium">Inactive</span>
                @endif
            </div>
        </div>
    @else
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                @if($image)
                    <img src="{{ $image }}" class="w-8 h-8 object-contain rounded border bg-white shrink-0" />
                @endif
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                        @if($href)
                            <a href="{{ $href }}" @if($preventTitleNavigation) x-on:click.prevent="" @endif class="hover:underline text-amber-600 dark:text-amber-400 font-bold">
                                {{ $title }}
                            </a>
                        @else
                            {{ $title }}
                        @endif
                    </div>
                    @if($subtitle)
                        <p class="text-[10px] text-zinc-400 font-medium truncate">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            
            @if($pivot)
                <div class="shrink-0">
                    {{ $pivot }}
                </div>
            @endif

            <div class="flex items-center gap-3 shrink-0">
                @if($actions) {{ $actions }} @endif
                <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-zinc-400 dark:bg-zinc-700' }}" title="{{ $isActive ? 'Active' : 'Inactive' }}"></span>
            </div>
        </div>
    @endif
</x-joranski-flux::clip.wrapper>
