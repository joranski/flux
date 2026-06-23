@props([
    'user' => filament()->auth()->user(),
])

@php
    $src = filament()->getUserAvatarUrl($user);
    $alt = __('filament-panels::layout.avatar.alt', ['name' => filament()->getUserName($user)]);
@endphp

<x-filament::avatar
    :src="$src"
    :alt="$alt"
    circular
    size="sm"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-user-avatar'])
    "
/>
