@php
    use Filament\Support\Enums\GridDirection;

    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $isHtmlAllowed = $isHtmlAllowed();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();
    $options = $getOptions();
    $livewireKey = $getLivewireKey();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('checkbox-list', 'filament/forms') }}"
        x-data="checkboxListFormComponent({
                    livewireId: @js($this->getId()),
                })"
        {{ $getExtraAlpineAttributeBag()->class(['fi-fo-checkbox-list space-y-4']) }}
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <flux:input
                    size="sm"
                    placeholder="{{ $getSearchPrompt() }}"
                    type="search"
                    x-model.debounce.{{ $getSearchDebounce() }}="search"
                    icon="magnifying-glass"
                />
            @endif

            @if ($isBulkToggleable && count($options))
                <div
                    x-cloak
                    class="fi-fo-checkbox-list-actions flex gap-4 text-xs font-semibold text-zinc-500 dark:text-zinc-400"
                    wire:key="{{ $livewireKey }}.actions"
                >
                    <button
                        type="button"
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.select-all"
                        class="hover:text-zinc-800 dark:hover:text-white transition"
                    >
                        {{ $getAction('selectAll') }}
                    </button>

                    <button
                        type="button"
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.deselect-all"
                        class="hover:text-zinc-800 dark:hover:text-white transition"
                    >
                        {{ $getAction('deselectAll') }}
                    </button>
                </div>
            @endif
        @endif

        <div
            {{
                $getExtraAttributeBag()
                    ->grid($getColumns(), $gridDirection)
                    ->merge([
                        'x-show' => $isSearchable ? 'visibleCheckboxListOptions.length' : null,
                    ], escape: false)
                    ->class([
                        'fi-fo-checkbox-list-options gap-4 grid',
                    ])
            }}
        >
            @forelse ($options as $value => $label)
                @php
                    $cleanLabel = $isHtmlAllowed ? $label : e($label);
                @endphp
                <div
                    wire:key="{{ $livewireKey }}.options.{{ $value }}"
                    @if ($isSearchable)
                        x-show="
                            $el
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(search.toLowerCase()) ||
                                $el
                                    .querySelector('.fi-fo-checkbox-list-option-description')
                                    ?.innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                        "
                    @endif
                    class="fi-fo-checkbox-list-option-ctn"
                >
                    {{-- Filament's checkboxListFormComponent Alpine helper requires .fi-fo-checkbox-list-option --}}
                    <div class="fi-fo-checkbox-list-option">
                        <flux:checkbox
                            :label="$cleanLabel"
                            :description="$hasDescription($value) ? $getDescription($value) : null"
                            :attributes="
                                $extraInputAttributeBag
                                    ->merge([
                                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $wireModelAttribute => $statePath,
                                        'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                    ], escape: false)
                                    ->class([
                                        'fi-checkbox-input',
                                        'fi-valid' => ! $errors->has($statePath),
                                        'fi-invalid' => $errors->has($statePath),
                                    ])
                            "
                        />
                        {{-- Keep hidden label text for Alpine search when Flux renders the visible label separately --}}
                        <span class="hidden fi-fo-checkbox-list-option-label">{{ $cleanLabel }}</span>
                        @if ($hasDescription($value))
                            <span class="hidden fi-fo-checkbox-list-option-description">{{ $getDescription($value) }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div wire:key="{{ $livewireKey }}.empty"></div>
            @endforelse
        </div>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="search && ! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message text-xs text-zinc-400 italic"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
