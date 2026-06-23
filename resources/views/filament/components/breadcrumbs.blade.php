@props([
    'breadcrumbs' => [],
])

<flux:breadcrumbs {{ $attributes }}>
    @foreach ($breadcrumbs as $url => $label)
        @if (is_int($url))
            <flux:breadcrumbs.item>
                {{ $label }}
            </flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item :href="$url">
                {{ $label }}
            </flux:breadcrumbs.item>
        @endif
    @endforeach
</flux:breadcrumbs>
