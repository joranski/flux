@props([
    'alignment' => \Filament\Support\Enums\Alignment::Start,
    'ariaLabelledby' => null,
    'autofocus' => \Filament\Support\View\Components\ModalComponent::$isAutofocused,
    'closeButton' => \Filament\Support\View\Components\ModalComponent::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\ModalComponent::$isClosedByClickingAway,
    'closeByEscaping' => \Filament\Support\View\Components\ModalComponent::$isClosedByEscaping,
    'closeEventName' => 'close-modal',
    'closeQuietlyEventName' => 'close-modal-quietly',
    'description' => null,
    'extraModalWindowAttributeBag' => null,
    'extraModalOverlayAttributeBag' => null,
    'footer' => null,
    'footerActions' => [],
    'footerActionsAlignment' => \Filament\Support\Enums\Alignment::Start,
    'header' => null,
    'heading' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => 'primary',
    'id' => null,
    'openEventName' => 'open-modal',
    'slideOver' => false,
    'slideOverPosition' => \Filament\Support\Enums\SlideOverPosition::End,
    'stickyFooter' => true,
    'stickyHeader' => true,
    'teleport' => null,
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\SlideOverPosition;
    use Filament\Support\Enums\Width;
    use Filament\Support\View\Components\ModalComponent\IconComponent;
    use Illuminate\View\ComponentAttributeBag;

    $hasContent = ! \Filament\Support\is_slot_empty($slot);
    $hasDescription = filled($description);
    $hasFooter = (! \Filament\Support\is_slot_empty($footer)) || (is_array($footerActions) && count($footerActions)) || (! is_array($footerActions) && (! \Filament\Support\is_slot_empty($footerActions)));
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    if (! $footerActionsAlignment instanceof Alignment) {
        $footerActionsAlignment = filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null;
    }

    if (is_string($width)) {
        $width = Width::tryFrom($width) ?? $width;
    }

    $closeEventHandler = filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()';

    $wireSubmitHandler = $attributes->get('wire:submit.prevent');
    $attributes = $attributes->except(['wire:submit.prevent']);

    $headingString = is_string($heading) ? $heading : (is_object($heading) && method_exists($heading, 'toHtml') ? $heading->toHtml() : (is_object($heading) ? (string) $heading : ''));
    $modalKeySuffix = md5($headingString . ($width instanceof Width ? $width->value : $width) . ($slideOver ? 'true' : 'false'));
@endphp

@if ($trigger)
    <div
        @if (! $trigger->attributes->get('disabled'))
            @if ($id)
                x-on:click="$dispatch(@js($openEventName), { id: @js($id) })"
            @else
                x-on:click="$el.nextElementSibling.dispatchEvent(new CustomEvent(@js($openEventName)))"
            @endif
        @endif
        {{ $trigger->attributes->except(['disabled'])->class(['fi-modal-trigger']) }}
    >
        {{ $trigger }}
    </div>
@endif

@if (filled($teleport))
    {!! "<template x-teleport=\"{$teleport}\">" !!}
@endif

<div
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    aria-modal="true"
    id="{{ $id }}"
    role="dialog"
    x-data="filamentModal({
                id: @js($id),
            })"
    @if ($id)
        data-fi-modal-id="{{ $id }}"
        x-on:{{ $closeEventName }}.window="if (($event.detail.id === @js($id)) && isOpen) close()"
        x-on:{{ $closeQuietlyEventName }}.window="if (($event.detail.id === @js($id)) && isOpen) closeQuietly()"
        x-on:{{ $openEventName }}.window="if (($event.detail.id === @js($id)) && (! isOpen)) open()"
    @else
        x-on:{{ $closeEventName }}.stop="if (isOpen) close()"
        x-on:{{ $closeQuietlyEventName }}.stop="if (isOpen) closeQuietly()"
        x-on:{{ $openEventName }}.stop="if (! isOpen) open()"
    @endif
    x-bind:class="{
        'fi-modal-open': isOpen,
    }"
    x-cloak
    x-show="isOpen"
    x-trap.noscroll{{ $autofocus ? '' : '.noautofocus' }}="isOpen"
    {{
        $attributes->class([
            'fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto',
        ])
    }}
>
    <!-- Overlay -->
    <div
        aria-hidden="true"
        x-show="isOpen"
        x-transition.duration.300ms.opacity
        @if ($closeByClickingAway)
            x-on:click="{{ $closeEventHandler }}"
        @endif
        {{ ($extraModalOverlayAttributeBag ?? new \Illuminate\View\ComponentAttributeBag)->class([
            'fixed inset-0 bg-zinc-800/40 dark:bg-zinc-950/60 backdrop-blur-xs transition-opacity',
        ]) }}
    ></div>

    <!-- Modal Window Container -->
    <div
        class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none"
    >
        <{{ filled($wireSubmitHandler) ? 'form' : 'div' }}
            @if ($closeByEscaping)
                x-on:keydown.window.escape="if (isTopmost()) {{ $closeEventHandler }}"
            @endif
            x-show="isWindowVisible"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @if (filled($wireSubmitHandler))
                wire:submit.prevent="{!! $wireSubmitHandler !!}"
            @endif
            @if (filled($id))
                wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.{{ $modalKeySuffix }}.window"
            @endif
            {{ ($extraModalWindowAttributeBag ?? new \Illuminate\View\ComponentAttributeBag)->class([
                'pointer-events-auto relative w-full bg-white dark:bg-zinc-800 border dark:border-zinc-700 shadow-xl rounded-xl transition-all flex flex-col',
                'fixed inset-y-0 right-0 h-screen max-h-screen rounded-none border-l' => $slideOver,
                'max-h-[calc(100vh-4rem)] rounded-xl' => ! $slideOver,
                match ($width?->value ?? $width) {
                    'xs' => 'max-w-xs',
                    'sm' => 'max-w-sm',
                    'md' => 'max-w-md',
                    'lg' => 'max-w-lg',
                    'xl' => 'max-w-xl',
                    '2xl' => 'max-w-2xl',
                    '3xl' => 'max-w-3xl',
                    '4xl' => 'max-w-4xl',
                    '5xl' => 'max-w-5xl',
                    '6xl' => 'max-w-6xl',
                    '7xl' => 'max-w-7xl',
                    'screen' => 'max-w-screen-2xl',
                    default => 'max-w-xl',
                },
            ]) }}
        >
            @if ($closeButton)
                <div class="absolute top-4 right-4 z-20">
                    <flux:button
                        variant="ghost"
                        icon="x-mark"
                        size="sm"
                        aria-label="Close"
                        x-on:click="{{ $closeEventHandler }}"
                        class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white"
                    />
                </div>
            @endif

            @if ($heading || $header)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.{{ $modalKeySuffix }}.header"
                    @endif
                    class="px-6 pt-6 pb-4 shrink-0 {{ $stickyHeader ? 'sticky top-0 bg-white dark:bg-zinc-800 z-10 border-b border-zinc-100 dark:border-zinc-700/50' : '' }}"
                >
                    @if ($header)
                        {{ $header }}
                    @else
                        <div class="flex items-start gap-3">
                            @if ($hasIcon)
                                <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 shrink-0">
                                    {{ \Filament\Support\generate_icon_html($icon, size: \Filament\Support\Enums\IconSize::Large) }}
                                </div>
                            @endif
                            <div class="pr-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $heading }}
                                </h3>
                                @if ($hasDescription)
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                        {{ $description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($hasContent)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.{{ $modalKeySuffix }}.content"
                    @endif
                    class="text-zinc-600 dark:text-zinc-300 text-sm px-6 py-6 flex-1 overflow-y-auto min-h-0"
                >
                    {{ $slot }}
                </div>
            @endif

            @if ($hasFooter)
                <div
                    @if (filled($id))
                        wire:key="{{ isset($this) ? "{$this->getId()}." : '' }}modal.{{ $id }}.{{ $modalKeySuffix }}.footer"
                    @endif
                    class="px-6 py-4 shrink-0 {{ $stickyFooter ? 'sticky bottom-0 bg-white dark:bg-zinc-800 z-10 border-t border-zinc-100 dark:border-zinc-700/50' : 'border-t border-zinc-100 dark:border-zinc-700/50' }} flex items-center justify-end gap-3"
                >
                    @if (! \Filament\Support\is_slot_empty($footer))
                        {{ $footer }}
                    @else
                        @if (is_array($footerActions))
                            @foreach ($footerActions as $action)
                                {{ $action }}
                            @endforeach
                        @else
                            {{ $footerActions }}
                        @endif
                    @endif
                </div>
            @endif
        </{{ filled($wireSubmitHandler) ? 'form' : 'div' }}>
    </div>
</div>

@if (filled($teleport))
    {!! '</template>' !!}
@endif
