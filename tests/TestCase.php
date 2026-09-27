<?php

declare(strict_types=1);

namespace Joranski\Flux\Tests;

use Flux\FluxServiceProvider as FluxUiServiceProvider;
use FluxPro\FluxProServiceProvider;
use Illuminate\Support\ViewErrorBag;
use Joranski\Flux\FluxServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FluxUiServiceProvider::class,
            FluxProServiceProvider::class,
            FluxServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }

    protected function setUp(): void
    {
        parent::setUp();

        view()->share('errors', new ViewErrorBag);
    }
}
