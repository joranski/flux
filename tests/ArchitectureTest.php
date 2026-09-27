<?php

declare(strict_types=1);

/*
 * The package must stay Filament-free: no Filament classes, Blade components,
 * view namespaces or Composer requirements. Plain filesystem scan so it runs
 * without the package having its own vendor/ (e.g. from the host app).
 */

/**
 * @return list<string>
 */
function joranskiFluxPackageFiles(): array
{
    $root = dirname(__DIR__);
    $files = [];

    foreach (['src', 'resources', 'config'] as $directory) {
        if (! is_dir($root.'/'.$directory)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }
    }

    return $files;
}

test('src, resources and config never reference Filament', function (): void {
    $files = joranskiFluxPackageFiles();

    expect($files)->not->toBeEmpty();

    foreach ($files as $path) {
        expect(file_get_contents($path))
            ->not->toMatch('/filament/i', "Filament reference found in {$path}");
    }
});

test('composer.json does not require any filament package', function (): void {
    $composer = json_decode(
        json: (string) file_get_contents(dirname(__DIR__).'/composer.json'),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    $packages = array_keys([...($composer['require'] ?? []), ...($composer['require-dev'] ?? [])]);

    foreach ($packages as $package) {
        expect($package)->not->toStartWith('filament/');
    }
});
