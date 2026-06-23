@props(['style', 'display', 'fixed', 'position'])

@php
use STS\FilamentImpersonate\Facades\Impersonation;

$impersonatorGuard = Impersonation::getImpersonatorGuardUsingName();
$currentPanelGuard = Filament\Facades\Filament::getAuthGuard();
$shouldShowBanner = Impersonation::isImpersonating()
    && $currentPanelGuard
    && $impersonatorGuard === $currentPanelGuard;
@endphp

@if($shouldShowBanner)

@php
$user = Filament\Facades\Filament::auth()->user();
if (blank($user)) {
    $display = "(No user found)";
}

$display = $display ?? Filament\Facades\Filament::getUserName($user);
$fixed = $fixed ?? config('filament-impersonate.banner.fixed');
$position = $position ?? config('filament-impersonate.banner.position');
$borderPosition = $position === 'top' ? 'bottom' : 'top';

$style = $style ?? config('filament-impersonate.banner.style');
$styles = config('filament-impersonate.banner.styles');
$default = $style === 'auto' ? 'light' : $style;
@endphp

<style>
    :root {
        --impersonate-banner-height: 40px;

        --impersonate-light-bg-color: #18181b;
        --impersonate-light-text-color: #f4f4f5;
        --impersonate-light-border-color: #27272a;

        --impersonate-dark-bg-color: #09090b;
        --impersonate-dark-text-color: #f4f4f5;
        --impersonate-dark-border-color: #27272a;
    }
    @if($fixed)
        body {
            margin-{{ $position }}: var(--impersonate-banner-height);
        }

        body.fi-body {
            min-height: calc(100dvh - var(--impersonate-banner-height));
        }

        body.fi-body .fi-main-ctn {
            min-height: calc(100dvh - var(--impersonate-banner-height));
        }

        body.fi-body-has-topbar .fi-main-ctn {
            min-height: calc(100dvh - 4rem - var(--impersonate-banner-height));
        }
    @else
        html {
            margin-{{ $position }}: var(--impersonate-banner-height);
        }
    @endif

    #impersonate-banner {
        position: {{ $fixed ? 'fixed' : 'absolute' }};
        height: var(--impersonate-banner-height);
        {{ $position }}: 0;
        width: 100%;
        display: flex;
        column-gap: 20px;
        justify-content: center;
        align-items: center;
        background-color: var(--impersonate-{{ $default }}-bg-color);
        color: var(--impersonate-{{ $default }}-text-color);
        border-{{ $borderPosition }}: 1px solid var(--impersonate-{{ $default }}-border-color);
        z-index: 45;
    }

    @if($style === 'auto')
        .dark #impersonate-banner {
            background-color: var(--impersonate-dark-bg-color);
            color: var(--impersonate-dark-text-color);
            border-{{ $borderPosition }}: 1px solid var(--impersonate-dark-border-color);
        }
    @endif

    @if($fixed)
        @if($position === 'top')
            body.fi-body .fi-sidebar {
                top: calc(4rem + var(--impersonate-banner-height));
                height: calc(100dvh - 4rem - var(--impersonate-banner-height));
            }
            .fi-topbar-ctn {
                top: var(--impersonate-banner-height);
            }
            .fi-modal.fi-modal-slide-over > .fi-modal-window-ctn > .fi-modal-window {
                padding-top: var(--impersonate-banner-height);
            }
        @else
            body.fi-body .fi-sidebar {
                height: calc(100dvh - 4rem - var(--impersonate-banner-height));
            }
            .fi-page-main {
                padding-bottom: var(--impersonate-banner-height);
            }
            .fi-modal.fi-modal-slide-over > .fi-modal-window-ctn > .fi-modal-window {
                padding-bottom: var(--impersonate-banner-height);
            }
        @endif

    @endif

    @media print {
        html, body {
            margin-top: 0;
            margin-bottom: 0;
        }

        body.fi-body {
            min-height: auto;
        }

        #impersonate-banner {
            display: none;
        }
    }
</style>

<div id="impersonate-banner">
    <div class="flex items-center gap-2 text-sm font-medium">
        <flux:icon.user class="size-4 text-zinc-400" />
        <span>{{ __('filament-impersonate::banner.impersonating') }} <strong class="font-semibold text-white">{{ $display }}</strong></span>
    </div>

    <flux:button
        href="{{ route('filament-impersonate.leave') }}"
        tag="a"
        size="xs"
        variant="primary"
        color="danger"
    >
        {{ __('filament-impersonate::banner.leave') }}
    </flux:button>
</div>
@endif
