@props([
    'livewire' => null,
])

@php
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi',
        'dark' => filament()->hasDarkMode() && filament()->hasDarkModeForced(),
    ])
>
    <head>
        <script>
            (function () {
                const theme =
                    localStorage.getItem('theme')
                    ?? localStorage.getItem('flux.appearance')
                    ?? @js(filament()->getDefaultThemeMode()->value);

                const isDark =
                    theme === 'dark'
                    || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

                document.documentElement.classList.toggle('dark', isDark);

                if (isDark) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            })();
        </script>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $renderHookScopes) }}

        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        @php
            $title = trim(strip_tags($livewire?->getTitle() ?? ''));
            $brandName = trim(strip_tags(filament()->getBrandName()));
        @endphp

        <title>
            {{ filled($title) ? $title : null }}
            {{ filled($brandName) && filled($title) ? ' - ' : null }}
            {{ filled($brandName) ? $brandName : null }}
        </title>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_BEFORE, scopes: $renderHookScopes) }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            [x-cloak='inline-flex'] {
                display: inline-flex !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        @fluxAppearance

        <script>
            (function () {
                const resolveTheme = () =>
                    localStorage.getItem('theme')
                    ?? localStorage.getItem('flux.appearance')
                    ?? @js(filament()->getDefaultThemeMode()->value);

                const applyPanelTheme = (appearance) => {
                    if (window.Flux?.applyAppearance) {
                        window.Flux.applyAppearance(appearance);

                        return;
                    }

                    const isDark =
                        appearance === 'dark'
                        || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

                    document.documentElement.classList.toggle('dark', isDark);

                    if (isDark) {
                        document.documentElement.setAttribute('data-theme', 'dark');
                    } else {
                        document.documentElement.removeAttribute('data-theme');
                    }
                };

                window.applyFilamentPanelTheme = applyPanelTheme;

                applyPanelTheme(resolveTheme());

                window.addEventListener('theme-changed', (event) => {
                    const theme = event.detail;

                    if (theme === 'system') {
                        localStorage.removeItem('flux.appearance');
                    } else if (theme === 'light' || theme === 'dark') {
                        localStorage.setItem('flux.appearance', theme);
                    }

                    applyPanelTheme(theme);
                });

                document.addEventListener('livewire:navigated', () => {
                    applyPanelTheme(resolveTheme());
                });
            })();
        </script>

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontPreloadHtml() }}
        {{ filament()->getMonoFontPreloadHtml() }}
        {{ filament()->getSerifFontPreloadHtml() }}
        {{ filament()->getFontHtml() }}
        {{ filament()->getMonoFontHtml() }}
        {{ filament()->getSerifFontHtml() }}

        <style>
            :root {
                --font-family: '{!! filament()->getFontFamily() !!}';
                --mono-font-family: '{!! filament()->getMonoFontFamily() !!}';
                --serif-font-family: '{!! filament()->getSerifFontFamily() !!}';
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
                --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
            }

            html.fi {
                --livewire-progress-bar-color: var(--primary-500);
            }
        </style>

        @stack('styles')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $renderHookScopes) }}
    </head>

    <body
        {{
            $attributes
                ->merge($livewire?->getExtraBodyAttributes() ?? [], escape: false)
                ->class([
                    'fi-body',
                    'fi-panel-' . filament()->getId(),
                ])
        }}
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $renderHookScopes) }}

        {{ $slot }}

        @livewire(Filament\Livewire\Notifications::class)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_BEFORE, scopes: $renderHookScopes) }}

        @filamentScripts(withCore: true)

        @fluxScripts

        @if (filament()->hasBroadcasting() && config('filament.broadcasting.echo'))
            <script data-navigate-once>
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            </script>
        @endif

        @stack('scripts')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_AFTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $renderHookScopes) }}
    </body>
</html>
