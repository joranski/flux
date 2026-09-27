<?php

declare(strict_types=1);

return [

    'barcode' => [
        /*
         * Camera scanning library for <x-joranski-flux::barcode-input>, loaded on
         * first use. Point it at a self-hosted copy to avoid the CDN.
         */
        'scanner_script' => env('JORANSKI_FLUX_BARCODE_SCANNER_SCRIPT', 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'),
    ],

];
