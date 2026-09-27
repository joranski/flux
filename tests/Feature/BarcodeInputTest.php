<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders a bound input with a scan button and a camera modal', function (): void {
    $html = Blade::render('<x-joranski-flux::barcode-input wire:model="barcode" label="Barcode" placeholder="UPC" maxlength="50" />');

    expect($html)
        ->toContain('wire:model="barcode"')
        ->toContain('placeholder="UPC"')
        ->toContain('maxlength="50"')
        ->toContain('data-barcode-scan-button')
        ->toContain('data-barcode-scanner-viewport')
        ->toContain('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');
});

it('uses the configured scanner script', function (): void {
    config()->set('joranski-flux.barcode.scanner_script', '/vendor/scanner.js');

    expect(Blade::render('<x-joranski-flux::barcode-input wire:model="code" />'))
        ->toContain('/vendor/scanner.js')
        ->not->toContain('unpkg.com');
});

it('gives each instance its own modal name', function (): void {
    $first = Blade::render('<x-joranski-flux::barcode-input wire:model="a" />');
    $second = Blade::render('<x-joranski-flux::barcode-input wire:model="b" />');

    preg_match('/data-modal-name="([^"]+)"/', $first, $a);
    preg_match('/data-modal-name="([^"]+)"/', $second, $b);

    expect($a[1] ?? null)->not->toBeNull()->not->toBe($b[1] ?? null);
});
