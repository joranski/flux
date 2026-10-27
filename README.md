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

### `<livewire:joranski-flux.tags-input>`

A Flux Pro pillbox for free-form tags: pick existing values or create new ones as you type. Model it as a
plain `array` of strings. Duplicates are ignored case-insensitively.

| Prop | Type | Default | Notes |
| --- | --- | --- | --- |
| `wire:model` | `array<string>` | required | Selected tag names (modelable). |
| `suggestions` | `array<string>` | `[]` | Extra options shown alongside the selected tags. |
| `label` | `?string` | `null` | Field label. |
| `placeholder` | `?string` | `null` | Search input placeholder. |
| `maxLength` | `int` | `255` | Longest tag accepted. |
| `allowCreate` | `bool` | `true` | `false` restricts the field to `suggestions` (locked against browser changes). |

```blade
<livewire:joranski-flux.tags-input wire:model="form.tags" :suggestions="$existingTags" label="Tags" />

{{-- Approved tags only: no "Create" option, typed text must match a suggestion --}}
<livewire:joranski-flux.tags-input wire:model="form.tags" :suggestions="$approvedTags" :allow-create="false" />
```

With `allowCreate` off, typed text is matched to a suggestion ignoring case (so `free shipping` becomes
`Free Shipping`), values that are not suggestions are dropped, and tags the bound value already had when the
component mounted stay selectable, so locking a field never discards existing data. The bound value still
comes from the browser: validate it against the same list when you save.

It is a Livewire component (not a Blade component) because creating a tag needs server state. The package
does not know about `spatie/laravel-tags`; sync the resulting array in your own form object.

### `<x-joranski-flux::barcode-input>`

A `flux:input` with a trailing scan button. The button opens a Flux modal that uses the device camera
([html5-qrcode](https://github.com/mebjas/html5-qrcode), loaded on first use) and writes the decoded
value back into the input, firing `input`/`change` so `wire:model` picks it up. Typing still works.

```blade
<x-joranski-flux::barcode-input wire:model="form.barcode" label="Barcode" maxlength="50" />
```

Props: `label`, `scanLabel`. Other attributes pass through to `flux:input`. Override the scanner script
URL (e.g. to self-host it) with `JORANSKI_FLUX_BARCODE_SCANNER_SCRIPT`, or publish the config:

```bash
php artisan vendor:publish --tag=joranski-flux-config
```

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
