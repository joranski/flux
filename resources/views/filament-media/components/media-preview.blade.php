@php
    $state = $getState();

    if (is_string($state)) {
        $url = $state;
        $mimeType = null;
        $fileName = null;
    } else {
        $url = $state['url'] ?? $state['preview_url'] ?? null;
        $mimeType = $state['mime_type'] ?? null;
        $fileName = $state['file_name'] ?? null;
    }

    $icon = app(\Joranski\FilamentMedia\Support\MimeIconResolver::class)->resolve(
        mimeType: $mimeType,
        fileName: $fileName,
    );

    $isImage = filled($mimeType)
        ? str_starts_with($mimeType, 'image/')
        : filled($fileName) && in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'], true);
@endphp

<div class="flex items-center gap-3 rounded-xl p-2 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-200/50 dark:border-zinc-800/50 shadow-xs">
    @if ($url && $isImage)
        <a href="{{ $url }}" target="media-preview" rel="noopener noreferrer" class="shrink-0">
            <img
                src="{{ $url }}"
                alt=""
                class="h-16 w-16 rounded-lg object-cover object-center border border-zinc-250/20 dark:border-zinc-700/20"
            />
        </a>
    @elseif ($url)
        <a
            href="{{ $url }}"
            target="media-preview"
            rel="noopener noreferrer"
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-500 hover:bg-zinc-200/80 dark:hover:bg-zinc-700/80 transition"
        >
            <x-filament::icon :icon="$icon" class="h-8 w-8" />
        </a>
    @else
        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-zinc-200 dark:bg-zinc-700 text-zinc-400">
            <x-filament::icon :icon="$icon" class="h-8 w-8" />
        </div>
    @endif
</div>
