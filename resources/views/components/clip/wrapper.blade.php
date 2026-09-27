@props([
    'type' => 'row',
    'isSelected' => false,
])
{{--
    Selection is expressed as a `data-selected` attribute instead of swapping
    class lists so an explorer list can move the highlight client-side
    (`$wire.$watch` on the selection property) without re-rendering the list.
    The server still stamps the attribute on first render.
--}}
<div {{ $attributes->class([
    'transition-all duration-150 shadow-xs border',
    'flex flex-col justify-between p-5 rounded-xl' => $type === 'card',
    'flex items-center justify-between p-3.5 rounded-xl gap-4' => $type === 'row',
    'bg-zinc-55/20 dark:bg-zinc-800/30 border-zinc-200 dark:border-zinc-800' => $type === 'card',
    'bg-zinc-50/20 dark:bg-zinc-800/5 hover:bg-zinc-50 dark:hover:bg-zinc-800/30 border-zinc-250/60 dark:border-zinc-800/40' => $type === 'row',
    'data-selected:border-amber-500 data-selected:bg-amber-500/10 data-selected:hover:bg-amber-500/10 dark:data-selected:bg-amber-500/5 dark:data-selected:hover:bg-amber-500/5 data-selected:ring-1 data-selected:ring-amber-500/20',
]) }} data-explorer-row @if($isSelected) data-selected @endif>
    {{ $slot }}
</div>
