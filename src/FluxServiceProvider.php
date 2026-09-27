<?php

declare(strict_types=1);

namespace Joranski\Flux;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FluxServiceProvider extends ServiceProvider
{
    public const VIEW_NAMESPACE = 'joranski-flux';

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', self::VIEW_NAMESPACE);

        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', self::VIEW_NAMESPACE);
    }
}
