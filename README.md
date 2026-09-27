# joranski/flux

Filament-free [Flux UI](https://fluxui.dev) Blade building blocks for Livewire apps.

The package ships anonymous Blade components under the `joranski-flux` namespace. It depends only on
Laravel, Livewire and Flux — no Filament classes, Blade components, view namespaces or Composer
requirements (enforced by `tests/ArchitectureTest.php`).

> **Upgrading from ≤ 0.1.x:** earlier releases were a Filament panel theme that prepended Flux view
> overrides onto `filament`, `filament-panels`, `filament-forms` and plugin namespaces. That theme is no
> longer part of this package; host apps that still run a Filament panel keep those overrides in their own
> codebase and register them from an app service provider.

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- Livewire 4
- Flux 2 (free + Pro)

## Installation

```bash
composer require joranski/flux
```

`Joranski\Flux\FluxServiceProvider` is auto-discovered. It registers:

- the view namespace `joranski-flux::` → `resources/views`
- the anonymous component namespace `<x-joranski-flux::…>` → `resources/views/components`

Include the package views in your Tailwind sources so the component classes are compiled:

```css
@source '../../vendor/joranski/flux/resources/views/**/*';
```

## Components

### `<x-joranski-flux::clip>`

A compact list item for master/detail explorer lists, rendered as a **card** or a dense **row**.

| Prop | Type | Default | Notes |
| --- | --- | --- | --- |
| `record` | `object` | required | Any object/model. Used for the title fallback, the active dot, and the default link. |
| `type` | `'card'\|'row'` | `'card'` | Card shows a status footer; row shows a status dot. |
| `title` | `?string` | `null` | Falls back to `name`, `code`, `number`, `domain`, then `Record #{id}`. |
| `subtitle` | `?string` | `null` | Small muted line under the title. |
| `href` | `?string` | `null` | Title link. When `null`, the host's resolver is asked (see below). |
| `image` | `?string` | `null` | Thumbnail URL. |
| `isSelected` | `bool` | `false` | Stamps `data-selected` for the amber highlight. |
| `pivot` | slot | `null` | Extra content (price, badges) — below the title on cards, right-aligned on rows. |
| `actions` | slot | `null` | Trailing actions. |

All other attributes (`wire:click`, `wire:key`, `data-*`, `class`, …) pass through to the root element.
When the clip has a `wire:click`, a left click on the title link is prevented so selecting the row does
not also navigate; middle-click / open-in-new-tab still use the `href`.

The active state reads `$record->active ?? $record->is_active ?? true`.

```blade
<x-joranski-flux::clip
    :record="$sku"
    :type="$dense ? 'row' : 'card'"
    :isSelected="$selectedSkuId === $sku->id"
    :subtitle="$sku->partnum"
    :href="route('manage.skus', ['selectedSkuId' => $sku->id])"
    wire:key="sku-{{ $sku->id }}"
    wire:click="selectSku({{ $sku->id }})"
    data-record-id="{{ $sku->id }}"
    class="cursor-pointer"
>
    <x-slot name="pivot">
        <span class="font-mono">{{ $sku->price }}</span>
    </x-slot>
</x-joranski-flux::clip>
```

#### Default links

The package knows nothing about your models or routes. Register a resolver in a service provider to supply
a link when `href` is omitted:

```php
use Joranski\Flux\Clip;

Clip::resolveHrefUsing(fn (object $record): ?string => match ($record::class) {
    \App\Models\Product::class => route('products.show', $record),
    default => null,
});
```

Without a resolver, clips without an `href` render an unlinked title.

#### Client-side selection

The root element carries `data-explorer-row` and, when selected, `data-selected`. Styling uses Tailwind's
`data-selected:` variant, so an explorer can move the highlight without re-rendering the list:

```js
$wire.$watch('selectedSkuId', (id) => {
    $el.querySelectorAll('[data-explorer-row][data-record-id]').forEach((row) => {
        row.toggleAttribute('data-selected', String(row.dataset.recordId) === String(id))
    })
})
```

### `<x-joranski-flux::clip.wrapper>`

The bare card/row shell used by `clip`, for building custom list items with the same chrome.
Props: `type` (`'row'` default, or `'card'`) and `isSelected`.

## Testing

```bash
composer test
```

The architecture test is a plain filesystem scan, so it can also run from a host app:

```bash
vendor/bin/pest vendor/joranski/flux/tests/ArchitectureTest.php
```

## License

MIT
