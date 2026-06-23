@php
    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $canSelectPlaceholder = $canSelectPlaceholder();
    $isAutofocused = $isAutofocused();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $isReorderable = $isReorderable();
    $isSearchable = $isSearchable();
    $hasInitialNoOptionsMessage = $hasInitialNoOptionsMessage();
    $hasDynamicOptions = $hasDynamicOptions();
    $canOptionLabelsWrap = $canOptionLabelsWrap();
    $isRequired = $isRequired();
    $isConcealed = $isConcealed();
    $isHtmlAllowed = $isHtmlAllowed();
    $isNative = (! ($isSearchable || $isMultiple || $isHtmlAllowed) && $isNative());
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $key = $getKey();
    $id = $getId();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
    $state = $getRawState();
    $livewireKey = $getLivewireKey();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    class="fi-fo-select-wrp"
>
    @if ($isNative)
        @php
            $options = $getOptions();
            
            // Map prefix icon
            if ($prefixIcon) {
                if ($prefixIcon instanceof \BackedEnum) {
                    $prefixIcon = $prefixIcon->value;
                }
                if (is_string($prefixIcon)) {
                    $prefixIcon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $prefixIcon);
                    if (! \Flux\Flux::componentExists("icon.{$prefixIcon}")) {
                        $prefixIcon = null;
                    }
                }
            }
        @endphp

        <flux:select
            size="sm"
            :id="$id"
            :disabled="$isDisabled"
            :required="$isRequired && (! $isConcealed)"
            :placeholder="$canSelectPlaceholder ? $getPlaceholder() : null"
            :icon="$prefixIcon"
            :attributes="
                $extraInputAttributeBag
                    ->merge([
                        'autofocus' => $isAutofocused,
                        'wire:key' => $hasDynamicOptions ? ($livewireKey . '.' . substr(md5(serialize($options)), 0, 64)) : null,
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
            "
        >
            @foreach ($options as $value => $label)
                @if (is_array($label))
                    <optgroup label="{{ $value }}">
                        @foreach ($label as $groupedValue => $groupedLabel)
                            <flux:select.option
                                :disabled="$isOptionDisabled($groupedValue, $groupedLabel)"
                                value="{{ $groupedValue }}"
                            >
                                @if ($isHtmlAllowed)
                                    {!! $groupedLabel !!}
                                @else
                                    {{ $groupedLabel }}
                                @endif
                            </flux:select.option>
                        @endforeach
                    </optgroup>
                @else
                    <flux:select.option
                        :disabled="$isOptionDisabled($value, $label)"
                        value="{{ $value }}"
                    >
                        @if ($isHtmlAllowed)
                            {!! $label !!}
                        @else
                            {{ $label }}
                        @endif
                    </flux:select.option>
                @endif
            @endforeach
        </flux:select>
    @else
        <x-filament::input.wrapper
            :disabled="$isDisabled"
            :inline-prefix="$isPrefixInline"
            :inline-suffix="$isSuffixInline"
            :prefix="$prefixLabel"
            :prefix-actions="$prefixActions"
            :prefix-icon="$prefixIcon"
            :prefix-icon-color="$prefixIconColor"
            :suffix="$suffixLabel"
            :suffix-actions="$suffixActions"
            :suffix-icon="$suffixIcon"
            :suffix-icon-color="$suffixIconColor"
            :valid="! $errors->has($statePath)"
            :x-on:focus-input.stop="'$el.querySelector(\'.fi-select-input-btn\')?.focus()'"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class([
                        'fi-fo-select',
                    ])
            "
        >
            <div
                class="fi-hidden"
                x-data="{
                    isDisabled: @js($isDisabled),
                    init() {
                        const container = $el.nextElementSibling
                        container.dispatchEvent(
                            new CustomEvent('set-select-property', {
                                detail: { isDisabled: this.isDisabled },
                            }),
                        )
                    },
                }"
            ></div>
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('select', 'filament/forms') }}"
                x-data="selectFormComponent({
                            canOptionLabelsWrap: @js($canOptionLabelsWrap),
                            canSelectPlaceholder: @js($canSelectPlaceholder),
                            getOptionLabelUsing: async () => {
                                return await Livewire.fireAction(
                                    $wire.__instance,
                                    'callSchemaComponentMethod',
                                    [@js($key), 'getOptionLabel'],
                                    { async: true },
                                )
                            },
                            getOptionLabelsUsing: async () => {
                                return await Livewire.fireAction(
                                    $wire.__instance,
                                    'callSchemaComponentMethod',
                                    [@js($key), 'getOptionLabelsForJs'],
                                    { async: true },
                                )
                            },
                            getOptionsUsing: async () => {
                                return await Livewire.fireAction(
                                    $wire.__instance,
                                    'callSchemaComponentMethod',
                                    [@js($key), 'getOptionsForJs'],
                                    { async: true },
                                )
                            },
                            getSearchResultsUsing: async (search) => {
                                return await Livewire.fireAction(
                                    $wire.__instance,
                                    'callSchemaComponentMethod',
                                    [@js($key), 'getSearchResultsForJs', { search }],
                                    { async: true },
                                )
                            },
                            hasDynamicOptions: @js($hasDynamicOptions),
                            hasDynamicSearchResults: @js($hasDynamicSearchResults()),
                            hasInitialNoOptionsMessage: @js($hasInitialNoOptionsMessage),
                            initialOptionLabel: @js((blank($state) || $isMultiple) ? null : $getOptionLabel()),
                            initialOptionLabels: @js((filled($state) && $isMultiple) ? $getOptionLabelsForJs() : []),
                            initialState: @js($state),
                            isAutofocused: @js($isAutofocused),
                            isDisabled: @js($isDisabled),
                            isHtmlAllowed: @js($isHtmlAllowed),
                            isMultiple: @js($isMultiple),
                            isReorderable: @js($isReorderable),
                            isSearchable: @js($isSearchable),
                            livewireId: @js($this->getId()),
                            loadingMessage: @js($getLoadingMessage()),
                            maxItems: @js($getMaxItems()),
                            maxItemsMessage: @js($getMaxItemsMessage()),
                            noOptionsMessage: @js($getNoOptionsMessage()),
                            noSearchResultsMessage: @js($getNoSearchResultsMessage()),
                            options: @js($getOptionsForJs()),
                            optionsLimit: @js($getOptionsLimit()),
                            placeholder: @js($getPlaceholder()),
                            position: @js($getPosition()),
                            searchDebounce: @js($getSearchDebounce()),
                            searchingMessage: @js($getSearchingMessage()),
                            searchPrompt: @js($getSearchPrompt()),
                            searchableOptionFields: @js($getSearchableOptionFields()),
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                            statePath: @js($statePath),
                        })"
                wire:ignore
                wire:key="{{ $livewireKey }}.{{
                    substr(md5(serialize([
                        $isDisabled,
                        $isReorderable,
                    ])), 0, 64)
                }}"
                x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                x-on:set-select-property="$event.detail.isDisabled ? select.disable() : select.enable()"
                {{
                    $attributes
                        ->merge($getExtraAlpineAttributes(), escape: false)
                        ->class(['fi-select-input'])
                }}
            >
                <div x-ref="select"></div>
            </div>
        </x-filament::input.wrapper>
    @endif
</x-dynamic-component>
