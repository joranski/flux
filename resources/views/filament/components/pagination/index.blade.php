@props([
    'currentPageOptionProperty' => 'tableRecordsPerPage',
    'extremeLinks' => false,
    'paginator',
    'pageOptions' => [],
])

@php
    use Illuminate\Contracts\Pagination\CursorPaginator;

    $isRtl = __('filament-panels::layout.direction') === 'rtl';
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
@endphp

<div
    {{
        $attributes->class([
            'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 pb-4 px-4 sm:px-6 border-t border-zinc-200 dark:border-zinc-800',
        ])
    }}
>
    {{-- Left: Overview of records --}}
    @if (! $isSimple)
        <div class="text-zinc-500 dark:text-zinc-400 text-xs font-medium">
            {{
                trans_choice(
                    'filament::components/pagination.overview',
                    $paginator->total(),
                    [
                        'first' => \Illuminate\Support\Number::format($paginator->firstItem() ?? 0),
                        'last' => \Illuminate\Support\Number::format($paginator->lastItem() ?? 0),
                        'total' => \Illuminate\Support\Number::format($paginator->total()),
                    ],
                )
            }}
        </div>
    @else
        <div></div>
    @endif

    {{-- Right: Pagination controls & Per Page selector --}}
    <div class="flex items-center gap-4 self-end sm:self-auto">
        @if (count($pageOptions) > 1)
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    {{ __('filament::components/pagination.fields.records_per_page.label') }}
                </label>
                <flux:select
                    size="sm"
                    :wire:model.live="$currentPageOptionProperty"
                    class="w-20"
                >
                    @foreach ($pageOptions as $option)
                        <option value="{{ $option }}">
                            {{ $option === 'all' ? __('filament::components/pagination.fields.records_per_page.options.all') : $option }}
                        </option>
                    @endforeach
                </flux:select>
            </div>
        @endif

        {{-- Page buttons --}}
        @if ($paginator->hasPages())
            <div class="flex items-center bg-white border border-zinc-200 rounded-[8px] p-[1px] dark:bg-white/10 dark:border-white/10 shadow-xs">
                {{-- Previous Page Button --}}
                @if ($paginator->onFirstPage())
                    <div class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-left variant="micro" class="rtl:hidden" />
                        <flux:icon.chevron-right variant="micro" class="hidden rtl:inline" />
                    </div>
                @else
                    @php
                        if ($paginator instanceof CursorPaginator) {
                            $wireClickAction = "setPage('{$paginator->previousCursor()->encode()}', '{$paginator->getCursorName()}')";
                        } else {
                            $wireClickAction = "previousPage('{$paginator->getPageName()}')";
                        }
                    @endphp
                    <button
                        type="button"
                        wire:click="{{ $wireClickAction }}"
                        class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/10 hover:text-zinc-800 dark:hover:text-white"
                    >
                        <flux:icon.chevron-left variant="micro" class="rtl:hidden" />
                        <flux:icon.chevron-right variant="micro" class="hidden rtl:inline" />
                    </button>
                @endif

                {{-- Page numbers --}}
                @if (! $isSimple)
                    @foreach (\Livewire\invade($paginator)->elements() as $element)
                        @if (is_string($element))
                            <div class="cursor-default flex justify-center items-center text-xs size-6 rounded-[6px] font-medium text-zinc-400 dark:text-zinc-500">
                                {{ $element }}
                            </div>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <div
                                        wire:key="{{ $this->getId() }}.pagination.page{{ $page }}"
                                        aria-current="page"
                                        class="cursor-default flex justify-center items-center text-xs h-6 px-2 rounded-[6px] font-semibold dark:text-white text-zinc-900 bg-zinc-100 dark:bg-white/10"
                                    >
                                        {{ \Illuminate\Support\Number::format($page) }}
                                    </div>
                                @else
                                    <button
                                        type="button"
                                        wire:key="{{ $this->getId() }}.pagination.page{{ $page }}"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        class="text-xs h-6 px-2 rounded-[6px] text-zinc-500 font-medium dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/10 hover:text-zinc-800 dark:hover:text-white"
                                    >
                                        {{ \Illuminate\Support\Number::format($page) }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif

                {{-- Next Page Button --}}
                @if ($paginator->hasMorePages())
                    @php
                        if ($paginator instanceof CursorPaginator) {
                            $wireClickAction = "setPage('{$paginator->nextCursor()->encode()}', '{$paginator->getCursorName()}')";
                        } else {
                            $wireClickAction = "nextPage('{$paginator->getPageName()}')";
                        }
                    @endphp
                    <button
                        type="button"
                        wire:click="{{ $wireClickAction }}"
                        class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/10 hover:text-zinc-800 dark:hover:text-white"
                    >
                        <flux:icon.chevron-right variant="micro" class="rtl:hidden" />
                        <flux:icon.chevron-left variant="micro" class="hidden rtl:inline" />
                    </button>
                @else
                    <div class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-right variant="micro" class="rtl:hidden" />
                        <flux:icon.chevron-left variant="micro" class="hidden rtl:inline" />
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
