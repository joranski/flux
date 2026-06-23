@props([
    'title' => '',
    'denseExplorer' => true,
    'searchQuery' => '',
    'selectedRecord' => null,
    
    // Slots
    'filtersDropdown' => null,
    'guideModal' => null,
    'sortDropdown' => null,
    'createAction' => null,
    'filterTags' => null,
    'list' => null,
    'pagination' => null,
    
    'detailHeader' => null,
    'detailTabs' => null,
    'detailContent' => null,
    'emptyState' => null,
    'modals' => null,
    'scrollableDetail' => false,
])
<x-filament-panels::page class="fi-explorer-workspace {{ $denseExplorer ? 'fi-dense-explorer' : '' }}{{ $scrollableDetail ? ' fi-explorer-workspace--scrollable-detail' : '' }}">
    <div class="fi-explorer-grid grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-14rem+200px)] min-h-[800px]">
        <!-- Left Panel: Master List -->
        <div class="lg:col-span-4 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden h-full">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $title }}</span>
                        @if($filtersDropdown) {{ $filtersDropdown }} @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <flux:button size="xs" variant="ghost" wire:click="toggleDenseListing" :icon="$denseExplorer ? 'bars-3-bottom-left' : 'bars-4'" title="Toggle Compact View" />
                        @if($guideModal) {{ $guideModal }} @endif
                        @if($sortDropdown) {{ $sortDropdown }} @endif
                        @if($createAction) {{ $createAction }} @endif
                    </div>
                </div>
                <div class="w-full">
                    <flux:input 
                        wire:model.live.debounce.250ms="searchQuery" 
                        placeholder="Search..." 
                        size="sm" 
                        icon="magnifying-glass"
                        class="w-full"
                        clearable
                    />
                </div>
                @if($filterTags) {{ $filterTags }} @endif
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                {{ $list }}
            </div>

            @if($pagination) {{ $pagination }} @endif
        </div>
        
        <!-- Right Panel: Detail Workspace -->
        <div class="lg:col-span-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-y-auto h-full p-6 space-y-6">
            @if($selectedRecord)
                @if($detailHeader) {{ $detailHeader }} @endif
                @if($detailTabs) {{ $detailTabs }} @endif
                <div class="mt-4">
                    @if($detailContent) {{ $detailContent }} @endif
                </div>
            @else
                @if($emptyState)
                    {{ $emptyState }}
                @else
                    <div class="flex flex-col items-center justify-center h-full text-center py-20 space-y-4 text-zinc-400">
                        <flux:icon name="magnifying-glass" class="w-16 h-16 opacity-30" />
                        <p class="text-sm italic">Select an item from the list to view its details.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @if($modals) {{ $modals }} @endif
</x-filament-panels::page>
