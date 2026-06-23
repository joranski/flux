@props([
    'navigation',
])

@php
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
            // Map FontAwesome and other non-standard icons to standard Flux/Heroicons
            $iconMap = [
                'fas-boxes-packing' => 'archive-box',
                'fas-folder-tree' => 'folder',
                'fas-boxes-stacked' => 'cube',
                'fas-people-carry-box' => 'truck',
                'fas-users-line' => 'users',
                'fab-mix' => 'puzzle-piece',
                'fas-copyright' => 'tag',
                'fas-shop' => 'building-storefront',
                'fas-gears' => 'cog',
                'heroicon-o-wrench-screwdriver' => 'wrench',
                'heroicon-s-chat-bubble-bottom-center' => 'chat-bubble-left-right',
            ];

            if (isset($iconMap[$icon])) {
                $icon = $iconMap[$icon];
            }

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

@php
    $isCollapsed = request()->cookie('subnav_collapsed') === 'true';
@endphp

<div 
    x-data="{
        collapsed: {{ $isCollapsed ? 'true' : 'false' }},
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('subnav_collapsed', this.collapsed);
            document.cookie = 'subnav_collapsed=' + this.collapsed + '; path=/; max-age=31536000';
            window.dispatchEvent(new CustomEvent('subnav-toggle', { detail: this.collapsed }));
        }
    }"
    :data-collapsed="collapsed ? 'true' : 'false'"
    data-collapsed="{{ $isCollapsed ? 'true' : 'false' }}"
    {{ $attributes->class([
        'fi-page-sub-navigation-sidebar-ctn bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800 shrink-0 transition-all duration-200 relative',
    ]) }}
>
    <style>
        .fi-page-sub-navigation-sidebar-ctn {
            width: 15rem; /* w-60 */
            padding: 1rem; /* p-4 */
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] {
            width: 25px !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] h3,
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] [role="heading"],
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .uppercase,
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .px-3.py-2,
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] [class*="py-2"][class*="px-3"] {
            display: none !important;
        }
        /* Hide item labels instantly when collapsed to avoid page load flashing */
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .fi-subnav-item-label,
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] [data-flux-navlist-item] [data-content] {
            display: none !important;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] [data-flux-navlist-item] {
            justify-content: center !important;
            align-items: center !important;
            padding: 0.25rem 0 !important;
            margin-inline: 0 !important;
            width: 100% !important;
            gap: 0 !important;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] [data-flux-navlist-item] svg {
            width: 1rem !important;
            height: 1rem !important;
            margin-inline: auto !important;
            flex-shrink: 0 !important;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .border-b {
            border-bottom: none !important;
            margin-bottom: 0.5rem !important;
            padding-bottom: 0 !important;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .flex.items-center.justify-between {
            flex-direction: column !important;
            gap: 0.5rem !important;
        }
        /* Group Separator styles */
        .fi-subnav-group-separator {
            display: none;
        }
        .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .fi-subnav-group-separator {
            display: block;
            height: 1px;
            background-color: var(--color-zinc-200);
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            width: 100%;
        }
        .dark .fi-page-sub-navigation-sidebar-ctn[data-collapsed="true"] .fi-subnav-group-separator {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    <div class="flex items-center justify-between mb-4 border-b border-zinc-200 dark:border-zinc-800/80 pb-2" :class="collapsed ? 'flex-col gap-2' : ''">
        <span x-show="!collapsed" class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Navigation</span>
        <button 
            type="button" 
            @click="toggle()" 
            class="p-1 rounded-md text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors focus:outline-hidden"
            :title="collapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
            <flux:icon name="chevron-left" class="size-4 transition-transform duration-200" ::class="collapsed ? 'rotate-180' : ''" />
        </button>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <flux:navlist>
        @foreach ($navigation as $group)
            @php
                $groupLabel = $group->getLabel();
                $groupItems = $group->getItems();
            @endphp

            @if (!$loop->first)
                <div class="fi-subnav-group-separator"></div>
            @endif

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
                            title="{{ $item->getLabel() }}"
                        >
                            <span class="fi-subnav-item-label" x-show="!collapsed">{{ $item->getLabel() }}</span>
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
                        title="{{ $item->getLabel() }}"
                    >
                        <span class="fi-subnav-item-label" x-show="!collapsed">{{ $item->getLabel() }}</span>
                    </flux:navlist.item>
                @endforeach
            @endif
        @endforeach
    </flux:navlist>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_AFTER, scopes: $this->getRenderHookScopes()) }}
</div>
