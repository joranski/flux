@props([
    'label' => null,
    'scanLabel' => __('Scan barcode'),
])
@php
    $boundTo = $attributes->wire('model')->value();
    $modalName = 'barcode-scanner-'.($boundTo ? substr(md5($boundTo), 0, 10) : \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(10)));
    $scannerScript = (string) config('joranski-flux.barcode.scanner_script');
@endphp
<div
    x-data="{
        scanner: null,
        error: null,
        async loadScript() {
            if (window.Html5Qrcode) {
                return;
            }

            await new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = this.$root.dataset.scannerScript;
                script.onload = resolve;
                script.onerror = () => reject(new Error('Could not load the barcode scanner.'));
                document.head.appendChild(script);
            });
        },
        async start() {
            this.error = null;

            try {
                await this.loadScript();
                this.scanner = new window.Html5Qrcode(this.$refs.viewport.id, {
                    experimentalFeatures: { useBarCodeDetectorIfSupported: true },
                });
                await this.scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 260, height: 160 } },
                    (code) => this.found(code),
                );
            } catch (exception) {
                this.error = exception?.message ?? String(exception);
            }
        },
        async stop() {
            if (! this.scanner) {
                return;
            }

            try {
                await this.scanner.stop();
            } catch (exception) {}

            this.scanner.clear();
            this.scanner = null;
        },
        found(code) {
            const input = this.$root.querySelector('input[data-barcode-value]');
            input.value = code;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            this.stop();
            $flux.modal(@js($modalName)).close();
        },
    }"
    data-modal-name="{{ $modalName }}"
    data-scanner-script="{{ $scannerScript }}"
>
    <flux:input :label="$label" data-barcode-value {{ $attributes }}>
        <x-slot name="iconTrailing">
            <flux:button
                size="sm"
                variant="subtle"
                icon="qr-code"
                class="-me-1"
                :tooltip="$scanLabel"
                :aria-label="$scanLabel"
                data-barcode-scan-button
                x-on:click="$flux.modal(@js($modalName)).show(); start()"
            />
        </x-slot>
    </flux:input>

    <flux:modal :name="$modalName" class="md:w-[28rem]" x-on:close="stop()">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $scanLabel }}</flux:heading>

            <div
                id="{{ $modalName }}-viewport"
                x-ref="viewport"
                data-barcode-scanner-viewport
                class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-900"
            ></div>

            <flux:text x-show="error" x-text="error" class="text-red-600 dark:text-red-400" x-cloak />
            <flux:text x-show="! error" class="text-sm">{{ __('Point the camera at the barcode. You can also type it in the field.') }}</flux:text>
        </div>
    </flux:modal>
</div>
