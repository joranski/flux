@php
    $fieldWrapperView = $getFieldWrapperView();
    $datalistOptions = $getDatalistOptions();
    $disabledDates = $getDisabledDates();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $extraAttributeBag = $getExtraAttributeBag();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $hasDate = $hasDate();
    $hasTime = $hasTime();
    $hasSeconds = $hasSeconds();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isAutofocused = $isAutofocused();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $maxDate = $getMaxDate();
    $minDate = $getMinDate();
    $defaultFocusedDate = $getDefaultFocusedDate();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
    $isReadOnly = $isReadOnly();
    $isRequired = $isRequired();
    $isConcealed = $isConcealed();
    $step = $getStep();
    $type = $getType();
    $livewireKey = $getLivewireKey();
@endphp

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    @if ($isNative())
        @php
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
            if ($suffixIcon) {
                if ($suffixIcon instanceof \BackedEnum) {
                    $suffixIcon = $suffixIcon->value;
                }
                if (is_string($suffixIcon)) {
                    $suffixIcon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $suffixIcon);
                    if (! \Flux\Flux::componentExists("icon.{$suffixIcon}")) {
                        $suffixIcon = null;
                    }
                }
            }
        @endphp

        <flux:input
            size="sm"
            :type="$type"
            :id="$id"
            :disabled="$isDisabled"
            :readonly="$isReadOnly"
            :required="$isRequired && (! $isConcealed)"
            :placeholder="filled($placeholder) ? $placeholder : null"
            :icon="$prefixIcon"
            :icon-trailing="$suffixIcon"
            :attributes="
                $extraInputAttributeBag
                    ->merge($extraAlpineAttributes, escape: false)
                    ->merge([
                        'max' => $hasTime ? $maxDate : ($maxDate ? \Carbon\Carbon::parse($maxDate)->toDateString() : null),
                        'min' => $hasTime ? $minDate : ($minDate ? \Carbon\Carbon::parse($minDate)->toDateString() : null),
                        'step' => $step,
                        $applyStateBindingModifiers('wire:model') => $statePath,
                    ], escape: false)
            "
        />
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
            x-on:focus-input.stop="$el.querySelector('input:not([type=hidden])')?.focus()"
            :attributes="\Filament\Support\prepare_inherited_attributes($extraAttributeBag)->class(['fi-fo-date-time-picker'])"
        >
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('date-time-picker', 'filament/forms') }}"
                x-data="dateTimePickerFormComponent({
                            defaultFocusedDate: @js($defaultFocusedDate),
                            displayFormat:
                                '{{ convert_date_format($getDisplayFormat())->to('day.js') }}',
                            firstDayOfWeek: {{ $getFirstDayOfWeek() }},
                            isAutofocused: @js($isAutofocused),
                            locale: @js($getLocale()),
                            shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                        })"
                wire:ignore
                wire:key="{{ $livewireKey }}.{{
                    substr(md5(serialize([
                        $disabledDates,
                        $isDisabled,
                        $isReadOnly,
                        $maxDate,
                        $minDate,
                        $hasDate,
                        $hasTime,
                        $hasSeconds,
                    ])), 0, 64)
                }}"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{ $getExtraAlpineAttributeBag() }}
            >
                <input x-ref="maxDate" type="hidden" value="{{ $maxDate }}" />

                <input x-ref="minDate" type="hidden" value="{{ $minDate }}" />

                <input
                    x-ref="disabledDates"
                    type="hidden"
                    value="{{ json_encode($disabledDates) }}"
                />

                <button
                    x-ref="button"
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown.enter.prevent.stop="
                        if (! $el.disabled) {
                            isOpen() ? selectDate() : togglePanelVisibility()
                        }
                    "
                    x-on:keydown.arrow-left.prevent.stop="if (! $el.disabled) focusPreviousDay()"
                    x-on:keydown.arrow-right.prevent.stop="if (! $el.disabled) focusNextDay()"
                    x-on:keydown.arrow-up.prevent.stop="if (! $el.disabled) focusPreviousWeek()"
                    x-on:keydown.arrow-down.prevent.stop="if (! $el.disabled) focusNextWeek()"
                    x-on:keydown.backspace.prevent.stop="if (! $el.disabled) clearState()"
                    x-on:keydown.clear.prevent.stop="if (! $el.disabled) clearState()"
                    x-on:keydown.delete.prevent.stop="if (! $el.disabled) clearState()"
                    aria-label="{{ $placeholder }}"
                    type="button"
                    tabindex="-1"
                    @disabled($isDisabled || $isReadOnly)
                    {{
                        $getExtraTriggerAttributeBag()->class([
                            'fi-fo-date-time-picker-trigger flex items-center w-full px-3 text-start bg-transparent focus:outline-hidden',
                        ])
                    }}
                >
                    <input
                        @disabled($isDisabled)
                        readonly
                        placeholder="{{ $placeholder }}"
                        wire:key="{{ $livewireKey }}.display-text"
                        x-model="displayText"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        class="w-full bg-transparent border-0 p-0 text-sm text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:ring-0 focus:outline-hidden cursor-pointer"
                    />
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $livewireKey }}.panel"
                    class="fi-fo-date-time-picker-panel z-50 absolute p-4 rounded-xl border bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 shadow-lg min-w-[18rem]"
                >
                    @if ($hasDate)
                        <div class="fi-fo-date-time-picker-panel-header flex items-center justify-between gap-2 mb-3">
                            <select
                                x-model="focusedMonth"
                                class="fi-fo-date-time-picker-month-select text-xs font-semibold py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-hidden"
                            >
                                <template x-for="(month, index) in months">
                                    <option
                                        x-bind:value="index"
                                        x-text="month"
                                    ></option>
                                </template>
                            </select>

                            <input
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="focusedYear"
                                class="fi-fo-date-time-picker-year-input text-xs font-semibold w-16 py-1 text-center rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-hidden"
                            />
                        </div>

                        <div class="fi-fo-date-time-picker-calendar-header grid grid-cols-7 gap-1 text-center mb-1">
                            <template
                                x-for="(day, index) in dayLabels"
                                x-bind:key="index"
                            >
                                <div
                                    x-text="day"
                                    class="fi-fo-date-time-picker-calendar-header-day text-[10px] font-semibold text-zinc-400 uppercase tracking-wider"
                                ></div>
                            </template>
                        </div>

                        <div
                            role="grid"
                            class="fi-fo-date-time-picker-calendar grid grid-cols-7 gap-1 text-center"
                        >
                            <template
                                x-for="day in emptyDaysInFocusedMonth"
                                x-bind:key="day"
                            >
                                <div></div>
                            </template>

                            <template
                                x-for="day in daysInFocusedMonth"
                                x-bind:key="day"
                            >
                                <div
                                    x-text="day"
                                    x-on:click="dayIsDisabled(day) || selectDate(day)"
                                    x-on:mouseenter="setFocusedDay(day)"
                                    role="option"
                                    x-bind:aria-selected="focusedDate.date() === day"
                                    x-bind:class="{
                                        'bg-zinc-100 dark:bg-zinc-700': dayIsToday(day),
                                        'ring-2 ring-primary-500': focusedDate.date() === day,
                                        'bg-primary-500 text-white! rounded-md': dayIsSelected(day),
                                        'opacity-30 cursor-not-allowed': dayIsDisabled(day),
                                    }"
                                    class="fi-fo-date-time-picker-calendar-day p-2 text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 rounded-md cursor-pointer transition-all duration-150"
                                ></div>
                            </template>
                        </div>
                    @endif

                    @if ($hasTime)
                        <div class="fi-fo-date-time-picker-time-inputs flex items-center justify-center gap-2 mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                            <input
                                max="23"
                                min="0"
                                step="{{ $getHoursStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-on:blur="checkTimeInputValidity"
                                x-on:invalid="timeInputInvalid"
                                x-model.debounce="hour"
                                class="w-12 text-center text-sm py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-hidden"
                            />

                            <span
                                class="fi-fo-date-time-picker-time-input-separator text-zinc-400 font-bold"
                            >
                                :
                            </span>

                            <input
                                max="59"
                                min="0"
                                step="{{ $getMinutesStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-on:blur="checkTimeInputValidity"
                                x-on:invalid="timeInputInvalid"
                                x-model.debounce="minute"
                                class="w-12 text-center text-sm py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-hidden"
                            />

                            @if ($hasSeconds)
                                <span
                                    class="fi-fo-date-time-picker-time-input-separator text-zinc-400 font-bold"
                                >
                                    :
                                </span>

                                <input
                                    max="59"
                                    min="0"
                                    step="{{ $getSecondsStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-on:blur="checkTimeInputValidity"
                                    x-on:invalid="timeInputInvalid"
                                    x-model.debounce="second"
                                    class="w-12 text-center text-sm py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-hidden"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::input.wrapper>
    @endif
</x-dynamic-component>
