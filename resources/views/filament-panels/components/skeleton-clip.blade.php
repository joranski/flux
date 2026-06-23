@props([
    'type' => 'card', // 'card' or 'row'
    'model' => null,
])
@php
    $baseName = null;
    if ($model) {
        if (class_exists($model)) {
            $baseName = strtolower(class_basename($model));
        } else {
            $baseName = strtolower($model);
        }
    }

    $componentName = $baseName ? "clip.skeleton-{$baseName}" : null;
    $hasCustomSkeleton = $componentName && \Illuminate\Support\Facades\View::exists("components.{$componentName}");
@endphp

@if($hasCustomSkeleton)
    <x-dynamic-component :component="$componentName" :type="$type" {{ $attributes }} />
@else
    <x-clip.wrapper :type="$type" class="animate-pulse" {{ $attributes }}>
        @if($type === 'card')
            <div class="space-y-3 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1 flex items-center gap-3">
                        <flux:skeleton animate="pulse" class="w-10 h-10 rounded shrink-0" />
                        <div class="min-w-0 flex-1 space-y-2">
                            <flux:skeleton animate="pulse" class="h-4 w-24 rounded" />
                            <flux:skeleton animate="pulse" class="h-3 w-32 rounded" />
                        </div>
                    </div>
                </div>
                <div class="space-y-2 border-t border-zinc-100 dark:border-zinc-800/60 pt-2.5">
                    <flux:skeleton animate="pulse" class="h-3.5 w-full rounded" />
                    <flux:skeleton animate="pulse" class="h-3 w-16 rounded" />
                </div>
            </div>
        @else
            <div class="flex items-center justify-between w-full gap-4">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <flux:skeleton animate="pulse" class="w-8 h-8 rounded shrink-0" />
                    <div class="min-w-0 flex-1 space-y-2">
                        <flux:skeleton animate="pulse" class="h-4 w-32 rounded" />
                        <flux:skeleton animate="pulse" class="h-3 w-20 rounded" />
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <flux:skeleton animate="pulse" class="h-2 w-2 rounded-full" />
                </div>
            </div>
        @endif
    </x-clip.wrapper>
@endif
