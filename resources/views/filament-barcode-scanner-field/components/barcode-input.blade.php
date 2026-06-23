@php
    use Filament\Support\Facades\FilamentAsset;
    use function Filament\Support\prepare_inherited_attributes;
    $fieldWrapperView = $getFieldWrapperView();
    $datalistOptions = $getDatalistOptions();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $extraAttributeBag = $getExtraAttributeBag();
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();

    $inputAttributes = $getExtraInputAttributeBag()
            ->merge($extraAlpineAttributes, escape: false)
            ->merge([
                'autofocus' => $isAutofocused(),
                'disabled' => $isDisabled,
                'id' => $id,
                'inputmode' => $getInputMode(),
                'list' => $datalistOptions ? $id . '-list' : null,
                'max' => (! $isConcealed) ? $getMaxValue() : null,
                'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
                'min' => (! $isConcealed) ? $getMinValue() : null,
                'minlength' => (! $isConcealed) ? $getMinLength() : null,
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'readonly' => $isReadOnly(),
                'required' => $isRequired() && (! $isConcealed),
                'type' => "text",
                $applyStateBindingModifiers('wire:model') => $statePath,
            ], escape: false);
@endphp
<x-dynamic-component
        :component="$fieldWrapperView"
        :field="$field"
        :has-inline-label="$hasInlineLabel"
        class="fi-fo-text-input-wrp"
>
    <div xmlns:x-filament="http://www.w3.org/1999/html"
         x-load-js="['https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js']"
         x-load-css="[@js(FilamentAsset::getStyleHref('barcode-scanner-field', 'marcelorodrigo/filament-barcode-scanner-field'))]"
         x-on:close-modal.window="stopScanning()"
         x-data="{
        html5QrcodeScanner: null,
        stopScanning() {
           if(!this.html5QrcodeScanner) {
               return;
           }
           this.html5QrcodeScanner.pause();
           this.html5QrcodeScanner.clear();
           this.html5QrcodeScanner = null;
        },
        openScannerModal() {
            $dispatch('open-modal', { id: 'qrcode-scanner-modal-{{ $getName() }}' });
            this.startCamera();
        },
        closeScannerModal() {
            $dispatch('close-modal', { id: 'qrcode-scanner-modal-{{ $getName() }}' });
        },
        onScanSuccess(decodedText, decodedResult) {
            $wire.set('{{ $getStatePath() }}', decodedText);
            $dispatch('close-modal', { id: 'qrcode-scanner-modal-{{ $getName() }}' });
        },
        startCamera() {
            this.html5QrcodeScanner = new Html5QrcodeScanner('reader-{{ $getName() }}', { fps: 10, qrbox: {width: 250, height: 250} }, false);
            this.html5QrcodeScanner.render(this.onScanSuccess.bind(this));
        }
     }"
    >
        <div class="grid gap-y-2">
            <flux:input.group>
                <flux:input size="sm" :attributes="$inputAttributes" />
                <flux:input.group.suffix class="p-0">
                    <button type="button" x-on:click="openScannerModal()"
                            class="flex items-center justify-center w-9 h-9 text-zinc-400 dark:text-zinc-300 hover:text-zinc-500 rounded-lg transition-colors"
                            aria-label="{{ __('filament-barcode-scanner-field::barcode-scanner-field.actions.scan_qrcode') }}">
                    @php
                        $iconName = $getIcon();
                        if ($iconName instanceof \BackedEnum) {
                            $iconName = $iconName->value;
                        }
                        if (is_string($iconName)) {
                            $iconName = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $iconName);
                            if (str_starts_with($iconName, 'o-')) {
                                $iconName = substr($iconName, 2);
                            } elseif (str_starts_with($iconName, 'm-')) {
                                $iconName = substr($iconName, 2);
                            }
                        }
                        $useFluxIcon = is_string($iconName) && \Flux\Flux::componentExists("icon.{$iconName}");
                    @endphp
                    @if ($useFluxIcon)
                        <flux:icon :name="$iconName" class="size-5 shrink-0" />
                    @else
                        <!-- Fallback SVG for QR Code / Barcode -->
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h.008v.008H15V15Zm0 2.25h.008v.008H15v-.008Zm0 2.25h.008v.008H15v-.008Zm2.25-4.5h.008v.008H17.25V15Zm0 2.25h.008v.008H17.25v-.008Zm0 2.25h.008v.008H17.25v-.008Zm2.25-4.5h.008v.008H19.5V15Zm0 2.25h.008v.008H19.5v-.008Zm0 2.25h.008v.008H19.5v-.008Z" />
                        </svg>
                    @endif
                    </button>
                </flux:input.group.suffix>
            </flux:input.group>
        </div>

        <!-- Filament Modal for QrCode Scanner -->
        <x-filament::modal id="qrcode-scanner-modal-{{ $getName() }}" width="lg" :close-by-clicking-away="false">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">
                    {{ __('filament-barcode-scanner-field::barcode-scanner-field.modal.title', ['label' => $getLabel() ?? __('filament-barcode-scanner-field::barcode-scanner-field.modal.default_label')]) }}
                </h2>
            </x-slot>

            <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-950/40">
                <div id="scanner-container">
                    <div id="reader-{{ $getName() }}" class="w-full h-full min-h-[300px]"></div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end w-full">
                    <x-filament::button @click="closeScannerModal()" color="danger">
                        {{ __('filament-barcode-scanner-field::barcode-scanner-field.modal.close_button') }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</x-dynamic-component>
