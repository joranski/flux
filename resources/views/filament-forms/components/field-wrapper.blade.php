@props([
    'areHtmlErrorMessagesAllowed' => null,
    'errorMessage' => null,
    'errorMessages' => null,
    'field' => null,
    'hasErrors' => true,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => null,
    'id' => null,
    'inlineLabelVerticalAlignment' => \Filament\Support\Enums\VerticalAlignment::Start,
    'isDisabled' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'labelTag' => 'label',
    'required' => null,
    'shouldShowAllValidationMessages' => null,
    'statePath' => null,
])

@php
    use Filament\Support\Enums\VerticalAlignment;
    use Illuminate\Support\Arr;

    if ($field) {
        $hasInlineLabel ??= $field->hasInlineLabel();
        $hasNestedRecursiveValidationRules ??= $field instanceof \Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
        $id ??= $field->getId();
        $isDisabled ??= $field->isDisabled();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $required ??= $field->isMarkedAsRequired();
        $statePath ??= $field->getStatePath();
        $areHtmlErrorMessagesAllowed ??= $field->areHtmlValidationMessagesAllowed();
        $shouldShowAllValidationMessages ??= $field->shouldShowAllValidationMessages();
    }

    $aboveLabelSchema = $field?->getChildSchema($field::ABOVE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $belowLabelSchema = $field?->getChildSchema($field::BELOW_LABEL_SCHEMA_KEY)?->toHtmlString();
    $beforeLabelSchema = $field?->getChildSchema($field::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $afterLabelSchema = $field?->getChildSchema($field::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
    $aboveContentSchema = $field?->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $belowContentSchema = $field?->getChildSchema($field::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $beforeContentSchema = $field?->getChildSchema($field::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $afterContentSchema = $field?->getChildSchema($field::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $aboveErrorMessageSchema = $field?->getChildSchema($field::ABOVE_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();
    $belowErrorMessageSchema = $field?->getChildSchema($field::BELOW_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();

    $hasError = $hasErrors && (filled($errorMessage) || filled($errorMessages) || (filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")))));

    if ($hasError && filled($statePath) && blank($errorMessage) && blank($errorMessages)) {
        if ($shouldShowAllValidationMessages) {
            $errorMessages = $errors->has($statePath) ? $errors->get($statePath) : ($hasNestedRecursiveValidationRules ? $errors->get("{$statePath}.*") : []);

            if (count($errorMessages) === 1) {
                $errorMessage = Arr::first($errorMessages);
                $errorMessages = [];
            }
        } else {
            $errorMessage = $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null);
        }
    }
@endphp

<flux:field
    data-field-wrapper
    {{
        $attributes
            ->merge($field?->getExtraFieldWrapperAttributes() ?? [], escape: false)
            ->class([
                'fi-fo-field',
                'fi-fo-field-has-inline-label' => $hasInlineLabel,
            ])
    }}
>
    {{ $aboveLabelSchema }}

    @if ($labelPrefix || $labelSuffix)
        <div class="fi-fo-field-label-row mb-1.5 flex flex-wrap items-baseline gap-x-2 gap-y-1">
            @if ($labelPrefix)
                {{ $labelPrefix }}
            @endif

            @if (filled($label) && (! $labelSrOnly))
                <flux:label :for="$id" class="cursor-pointer shrink-0">
                    {{ $beforeLabelSchema }}
                    {{ $label }}@if ($required && (! $isDisabled))<span class="text-red-500 ml-0.5">*</span>@endif
                </flux:label>
            @endif

            @if ($labelSuffix)
                {{ $labelSuffix }}
            @endif

            @if (filled($afterLabelSchema))
                <div class="fi-fo-field-label-hint min-w-0">
                    {{ $afterLabelSchema }}
                </div>
            @endif
        </div>
    @elseif (filled($label) && (! $labelSrOnly))
        <div class="fi-fo-field-label-row mb-1.5 flex flex-wrap items-baseline gap-x-2 gap-y-1">
            <flux:label :for="$id" class="shrink-0">
                {{ $beforeLabelSchema }}
                {{ $label }}@if ($required && (! $isDisabled))<span class="text-red-500 ml-0.5">*</span>@endif
            </flux:label>

            @if (filled($afterLabelSchema))
                <div class="fi-fo-field-label-hint min-w-0">
                    {{ $afterLabelSchema }}
                </div>
            @endif
        </div>
    @elseif (filled($afterLabelSchema))
        <div class="fi-fo-field-label-hint mb-1.5">
            {{ $afterLabelSchema }}
        </div>
    @endif

    {{ $belowLabelSchema }}

    {{ $aboveContentSchema }}

    @if ($beforeContentSchema || $afterContentSchema)
        <div class="flex items-center gap-2">
            {{ $beforeContentSchema }}
            <div class="flex-1">
                {{ $slot }}
            </div>
            {{ $afterContentSchema }}
        </div>
    @else
        {{ $slot }}
    @endif

    {{ $belowContentSchema }}

    @if ($hasError)
        {{ $aboveErrorMessageSchema }}

        @if (filled($errorMessages))
            @foreach ($errorMessages as $err)
                <flux:error :message="$err" />
            @endforeach
        @else
            <flux:error :message="$errorMessage" />
        @endif

        {{ $belowErrorMessageSchema }}
    @endif
</flux:field>
