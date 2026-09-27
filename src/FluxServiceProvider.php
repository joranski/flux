<?php

declare(strict_types=1);

namespace Joranski\Flux;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Joranski\Flux\Livewire\TagsInput;
use Livewire\Livewire;

class FluxServiceProvider extends ServiceProvider
{
    public const VIEW_NAMESPACE = 'joranski-flux';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/joranski-flux.php', 'joranski-flux');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/joranski-flux.php' => config_path('joranski-flux.php'),
        ], 'joranski-flux-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', self::VIEW_NAMESPACE);

        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', self::VIEW_NAMESPACE);

        Livewire::component('joranski-flux.tags-input', TagsInput::class);
    }
}
