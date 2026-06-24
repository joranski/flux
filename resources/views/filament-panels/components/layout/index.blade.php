@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $hasTopbar = filament()->hasTopbar();
    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
    $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
    $hasTopNavigation = filament()->hasTopNavigation();
    $hasNavigation = filament()->hasNavigation();
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getMaxContentWidth() ?? Width::SevenExtraLarge);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    $sanitizeIcon = function ($icon) {
        if (! $icon) {
            return null;
        }
        if ($icon instanceof \BackedEnum) {
            $icon = $icon->value;
        }
        if (is_object($icon) && method_exists($icon, 'toHtml')) {
            $icon = $icon->toHtml();
        }
        if (is_object($icon) && method_exists($icon, '__toString')) {
            $icon = (string) $icon;
        }
        if (is_string($icon)) {
            $icon = str_replace(['heroicon-m-', 'heroicon-o-', 'heroicon-s-', 'heroicon-c-', 'heroicon-', 'fas-', 'far-', 'fal-', 'fad-'], '', $icon);
            if (str_starts_with($icon, 'o-')) {
                $icon = substr($icon, 2);
            } elseif (str_starts_with($icon, 'm-')) {
                $icon = substr($icon, 2);
            }
            if (! \Flux\Flux::componentExists("icon.{$icon}")) {
                return null;
            }
        }
        return $icon;
    };
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
        <!-- Header / Navbar -->
        <flux:header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" />

        <!-- Brand Logo/Name in Header for Desktop -->
        <div class="max-lg:hidden flex items-center gap-2 mr-6">
            @if ($homeUrl = filament()->getHomeUrl())
                <a {{ \Filament\Support\generate_href_html($homeUrl) }} class="flex items-center gap-2">
                    <x-filament-panels::logo />
                </a>
            @else
                <div class="flex items-center gap-2">
                    <x-filament-panels::logo />
                </div>
            @endif
        </div>

        <!-- Top Navigation for Desktop -->
        <flux:navbar class="-mb-px max-lg:hidden">
            @foreach (filament()->getNavigation() as $group)
                @php
                    $groupLabel = $group->getLabel();
                    $groupItems = $group->getItems();
                @endphp

                @if (filled($groupLabel))
                    @foreach ($groupItems as $item)
                        @php
                            $icon = $item->getIcon();
                            $icon = $sanitizeIcon($icon);
                        @endphp
                        <flux:navbar.item 
                            :icon="$icon ?: 'document-text'" 
                            :href="$item->getUrl()"
                            :current="$item->isActive()"
                            :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                        >
                            {{ $item->getLabel() }}
                        </flux:navbar.item>
                    @endforeach
                @else
                    @foreach ($groupItems as $item)
                        @php
                            $icon = $item->getIcon();
                            $icon = $sanitizeIcon($icon);
                        @endphp
                        <flux:navbar.item 
                            :icon="$icon ?: 'document-text'" 
                            :href="$item->getUrl()"
                            :current="$item->isActive()"
                            :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                        >
                            {{ $item->getLabel() }}
                        </flux:navbar.item>
                    @endforeach
                @endif
            @endforeach
        </flux:navbar>

        <flux:spacer />

        <!-- Database Notifications & User Profile Menu -->
        <div class="flex items-center gap-2 shrink-0 isolate overflow-visible pe-2 lg:pe-3">
            @if (filament()->hasDatabaseNotifications())
                @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                    'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                ])
            @endif

            @livewire(\Filament\Livewire\SimpleUserMenu::class)
        </div>
    </flux:header>

    <!-- Sidebar -->
    <flux:sidebar collapsible="mobile" class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-100 lg:hidden">
        <!-- Sidebar Header (For Mobile view context) -->
        <div class="hidden max-lg:flex px-6 py-4 items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
            @if ($homeUrl = filament()->getHomeUrl())
                <a {{ \Filament\Support\generate_href_html($homeUrl) }} class="flex items-center gap-2">
                    <x-filament-panels::logo />
                </a>
            @else
                <div class="flex items-center gap-2">
                    <x-filament-panels::logo />
                </div>
            @endif
        </div>

        <!-- Navigation list using Flux -->
        <flux:navlist class="mt-4">
            @foreach (filament()->getNavigation() as $group)
                @php
                    $groupLabel = $group->getLabel();
                    $groupItems = $group->getItems();
                @endphp

                @if (filled($groupLabel))
                    <flux:navlist.group heading="{{ $groupLabel }}">
                        @foreach ($groupItems as $item)
                            @php
                                $icon = $item->getIcon();
                                $icon = $sanitizeIcon($icon);
                            @endphp
                            <flux:navlist.item 
                                :icon="$icon ?: 'document-text'" 
                                :href="$item->getUrl()"
                                :current="$item->isActive()"
                                :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                            >
                                {{ $item->getLabel() }}
                            </flux:navlist.item>
                        @endforeach
                    </flux:navlist.group>
                @else
                    @foreach ($groupItems as $item)
                        @php
                            $icon = $item->getIcon();
                            $icon = $sanitizeIcon($icon);
                        @endphp
                        <flux:navlist.item 
                            :icon="$icon ?: 'document-text'" 
                            :href="$item->getUrl()"
                            :current="$item->isActive()"
                            :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                        >
                            {{ $item->getLabel() }}
                        </flux:navlist.item>
                    @endforeach
                @endif
            @endforeach
        </flux:navlist>
    </flux:sidebar>

    <!-- Main Content -->
    <flux:main>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_BEFORE, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START, scopes: $renderHookScopes) }}

        {{ $slot }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_AFTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
    </flux:main>
</x-filament-panels::layout.base>
