<?php

namespace Joranski\Flux;

use Illuminate\Support\ServiceProvider;

class FluxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No-op
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flux-panel');

        $this->callAfterResolving('view', function ($view) {
            $view->prependNamespace('filament-panels', __DIR__.'/../resources/views/filament-panels');
            $view->prependNamespace('filament-forms', __DIR__.'/../resources/views/filament-forms');
            $view->prependNamespace('filament-tables', __DIR__.'/../resources/views/filament-tables');
            $view->prependNamespace('filament-widgets', __DIR__.'/../resources/views/filament-widgets');
            $view->prependNamespace('filament-google-analytics', __DIR__.'/../resources/views/filament-google-analytics');
            $view->prependNamespace('filament-exceptions', __DIR__.'/../resources/views/filament-exceptions');
            $view->prependNamespace('filament-media', __DIR__.'/../resources/views/filament-media');
            $view->prependNamespace('laravel-addressing', __DIR__.'/../resources/views/laravel-addressing');
            $view->prependNamespace('filament-impersonate', __DIR__.'/../resources/views/filament-impersonate');
            $view->prependNamespace('impersonate', __DIR__.'/../resources/views/filament-impersonate');
            $view->prependNamespace('filament-activity-log', __DIR__.'/../resources/views/filament-activity-log');
            $view->prependNamespace('guava-calendar', __DIR__.'/../resources/views/guava-calendar');
            $view->prependNamespace('filament-select-tree', __DIR__.'/../resources/views/filament-select-tree');
            $view->prependNamespace('filament-barcode-scanner-field', __DIR__.'/../resources/views/filament-barcode-scanner-field');
            $view->prependNamespace('filament', __DIR__.'/../resources/views/filament');
        });
    }
}
