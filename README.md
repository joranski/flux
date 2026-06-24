# Joranski Flux Theme & Layout Overrides

This package provides visual customizations and layout overrides for Filament Panels in the application using **Livewire Flux**. It enables a clean, modern, and compact top-navigation structure on desktop, a thin collapsible sidebar for clusters, optimized whitespace heights, and custom unified button layouts.

Most Filament Blade components resolve through this package first; where Flux cannot safely replace Filament markup (notably **badged icon buttons**), the override **delegates back to stock Filament** so behavior and positioning stay correct.

---

## How It Works

The package integrates with Laravel and Filament by prepending view namespaces:

- The [`FluxServiceProvider`](src/FluxServiceProvider.php) hooks into the view factory's resolver.
- It prepends namespaces for Filament panels, forms, tables, widgets, and third-party plugins so templates under `resources/views/` win over vendor defaults.
- If a custom template exists, Laravel loads it; otherwise, it falls back to default Filament templates.

### Prepended namespaces

| Namespace | Override directory |
|-----------|-------------------|
| `filament` | `resources/views/filament/` |
| `filament-panels` | `resources/views/filament-panels/` |
| `filament-forms` | `resources/views/filament-forms/` |
| `filament-tables` | `resources/views/filament-tables/` |
| `filament-widgets` | `resources/views/filament-widgets/` |
| Plus plugin namespaces | `filament-activity-log`, `filament-impersonate`, `guava-calendar`, etc. |

Application-level styling that complements these overrides lives in `resources/css/filament/admin/theme.css` (built via Vite into the admin panel theme).

---

## How to Switch Between Layouts

### Enable Flux UI layout (default)

Ensure `Joranski\Flux\FluxServiceProvider::class` is registered in `bootstrap/providers.php`:

```php
return [
    AppServiceProvider::class,
    FilamentServiceProvider::class,
    AdminPanelProvider::class,
    VoltServiceProvider::class,
    Joranski\Flux\FluxServiceProvider::class, // <-- Active
];
```

### Revert to default Filament UI

1. Comment out the provider in `bootstrap/providers.php`.
2. Clear compiled views: `php artisan view:clear`
3. Hard-refresh the browser.

---

## Implemented Design Overrides

### 1. Top navigation hierarchy

- Root clusters and pages (Dashboard, Shop, Settings) render in the top navbar.
- Child pages and resources render in the cluster **sub-navigation sidebar**.

### 2. Collapsible cluster sub-navigation sidebar

**File:** `resources/views/filament-panels/components/page/sub-navigation/sidebar.blade.php`

Behavior:

- **Expanded:** `15rem` (`w-60`) width with labels, group headings, and Flux `navlist` groups.
- **Collapsed:** `25px` icon rail — icons only, no extra horizontal padding.
- Collapse state persists via `localStorage` and a `subnav_collapsed` cookie (read on first paint to avoid flash).
- Group separators (`.fi-subnav-group-separator`) show when collapsed to distinguish nav groups.
- Font Awesome and legacy Heroicon names are mapped to Flux icon names (e.g. `fas-boxes-stacked` → `cube`).

**Collapsed-mode CSS (important details):**

| Rule | Purpose |
|------|---------|
| `.fi-subnav-item-label { display: none }` | Hides item text labels only — **not** all `<span>` elements |
| `[data-content] { display: none }` | Hides Flux navlist label slot when collapsed |
| `[data-flux-navlist-item] { justify-content: center; padding-inline: 0 }` | Centers icons in the active highlight |
| `[data-flux-navlist-item] svg { margin-inline: auto }` | Keeps SVG icons visually centered in the narrow rail |

**Do not** restore a blanket rule like `[data-collapsed="true"] span { display: none }`. That breaks Flux navlist layout and causes icons to sit off-center inside the active-state background.

Labels use class `fi-subnav-item-label` plus Alpine `x-show="!collapsed"` for instant hide on toggle.

### 3. Icon buttons (`<x-filament::icon-button>`)

**File:** `resources/views/filament/components/icon-button.blade.php`

This override uses a **dual rendering strategy**:

| Condition | Renderer | Why |
|-----------|----------|-----|
| **`badge` prop is set** (e.g. database notification bell + unread count) | **Native Filament** vendor view via `view()->file(...)` | Filament positions the count in `.fi-icon-btn-badge-ctn` as a superscript at the top-right of the icon. Wrapping `flux:button` breaks that layout (flex row, wrong containing block, or uncompiled nested Flux tags → HTTP 500). |
| **No badge** | **Flux** `<flux:button square …>` | Keeps Flux styling for toolbar/modal icon actions. |

**Native path (badged):**

```blade
@if (filled($badge))
    {!! view()->file(
        base_path('vendor/filament/support/resources/views/components/icon-button.blade.php'),
        get_defined_vars(),
    )->render() !!}
@else
    {{-- flux:button path --}}
@endif
```

**Production caller:** Filament database notifications trigger (`vendor/filament/filament/resources/views/components/topbar/database-notifications-trigger.blade.php`) passes `:badge="$unreadNotificationsCount ?: null"` — always uses the native path when unread count &gt; 0.

**Lessons learned (2026-06-22):**

- Do **not** put `flux:badge` inside `flux:button` for notification counts.
- Do **not** wrap `flux:button` in an outer `<span>` for badge overlay — Blade may fail to compile nested self-closing Flux tags, causing **500 errors** after view recompilation.
- Do **not** add custom `.fi-icon-btn--flux` badge CSS in `theme.css`; Filament's built-in `.fi-icon-btn > .fi-icon-btn-badge-ctn` rules are correct when native markup is used.

### 4. Shield role form checkbox lists (Pages / Widgets tabs)

**File:** `resources/views/filament-forms/components/checkbox-list.blade.php`

Filament Shield's Pages and Widgets permission tabs use **searchable** checkbox lists. Filament's Alpine helper (`checkboxListFormComponent`) toggles visibility on elements with class **`.fi-fo-checkbox-list-option`**.

The Flux override wraps each option in:

```html
<div class="fi-fo-checkbox-list-option">
    <flux:checkbox … />
    <span class="hidden fi-fo-checkbox-list-option-label">…</span>
</div>
```

Without `.fi-fo-checkbox-list-option`, searchable lists report zero visible options and **all checkboxes disappear** (Resources tab often still worked because Shield sets `searchable: false` there).

**Test:** `tests/Feature/Authorization/ShieldRoleFormCheckboxListTest.php`

### 5. Optimized vertical whitespace

- Reduced top layout padding (see `theme.css`) so content sits closer to the top navbar.
- Compact breadcrumbs (`text-xs`) and page headers (`text-xl`).

### 6. Unified compact buttons

**File:** `resources/views/filament/components/button/index.blade.php`

- Maps default Filament action button size (`Size::Medium`) to Flux `sm` height application-wide.
- Aligns list actions with dropdown menus and avoids double-wrapped label text.

---

## Development Workflow

### Package layout (this repo)

This is a **standalone Composer package** ([joranski/flux on GitHub](https://github.com/joranski/flux)). Do **not** edit `vendor/joranski/flux` in the consuming app — changes belong here, then release via git tag.

| What | Where |
|------|--------|
| Filament Blade overrides | `resources/views/{namespace}/…` (prepended by `FluxServiceProvider`) |
| Provider / registration | `src/FluxServiceProvider.php` |
| App-only layout hooks | Consuming app (`resources/views/filament/app/`, `theme.css`) |
| Domain packages (emails, comments, …) | Their own repos / `packages/joranski/*` — **not** folded into flux unless adding a new prepended namespace |

**Workflow:**

1. Clone or work in this repo (e.g. `~/packages/flux`).
2. Copy the **stock Filament** view from `vendor/filament/…` when overriding; preserve Alpine hook classes (`fi-modal-*`, `fi-fo-*`, `filamentModal`, `filamentDropdown`).
3. Prefer Flux components for chrome that does not drive Filament Alpine (navbar, buttons, forms). Revert to stock Filament markup when Flux breaks Alpine (modals, teleported dropdowns) — see 2026-06-23 notes below.
4. Bump `"version"` in `composer.json`, commit, tag (`v0.1.1`), push to GitHub.
5. In the consuming app: `"joranski/flux": "^0.1.1"` + VCS repository (same pattern as `joranski/filament-media`).
6. `composer update joranski/flux && php artisan view:clear`

**Local path install (optional, like `filament-emails`):** only if you vendor the clone inside the app at `packages/joranski/flux` with a Composer `path` repository — avoid machine-specific absolute paths.

### After changing Blade overrides in this package

```bash
php artisan view:clear
```

If you also changed `resources/css/filament/admin/theme.css`:

```bash
npm run build
# or: npm run dev
```

Then hard-refresh the browser. CSS changes **do not** appear until Vite rebuilds the admin theme.

### Compiled view permissions (production / PHP-FPM)

Blade recompilation writes to `storage/framework/views`. On RHEL/CentOS, PHP-FPM runs as `apache` and must **own** those files (group write alone is not enough for Laravel's `touch()` on compiled views).

From the application root:

```bash
./bin/fix-web-permissions.sh          # before web traffic / after deploy
./bin/fix-web-permissions.sh --dev    # before local artisan / Pest (CLI user owns views)
bash bin/with-cli-storage-permissions.sh php artisan test …
```

Symptom of wrong ownership: generic **500 Server Error**, log may show `tempnam(): file created in the system's temporary directory` during view compile.

---

## Tests

| Test file | What it covers |
|-----------|----------------|
| `tests/Feature/Filament/FluxOverridesTest.php` | Modal/dropdown/user-menu layout invariants (run from consuming app) |
| `tests/Feature/Theme/FluxIconButtonBadgeTest.php` | Badged icon buttons use native Filament markup; non-badged use Flux |
| `tests/Feature/Authorization/ShieldRoleFormCheckboxListTest.php` | Shield Pages/Widgets checkbox lists render `.fi-fo-checkbox-list-option` |

Run:

```bash
php artisan test --compact tests/Feature/Theme/FluxIconButtonBadgeTest.php
php artisan test --compact tests/Feature/Authorization/ShieldRoleFormCheckboxListTest.php
```

---

## Future-Proofing: Customizing Future Filament Plugins

To add visual overrides for a new third-party Filament package:

1. **Identify the view namespace** the plugin registers (e.g. `filament-comments`).
2. **Prepend the namespace** in `FluxServiceProvider::boot()`:
   ```php
   $view->prependNamespace('plugin-namespace', __DIR__.'/../resources/views/plugin-namespace');
   ```
3. **Copy or author Blade templates** under `resources/views/plugin-namespace/` using Flux components where appropriate.
4. **Clear views and rebuild assets** if CSS changed: `php artisan view:clear` and `npm run build`.

When overriding form components that Filament drives with Alpine (checkbox lists, repeaters, etc.), **preserve Filament's documented CSS hook classes** from the vendor template — search the vendor view for `fi-fo-*` or `x-show` selectors before replacing markup with Flux.

---

## Quick Reference: Key Files Changed (2026-06-22)

| File | Change |
|------|--------|
| `resources/views/filament/components/icon-button.blade.php` | Dual native/Flux rendering for badged vs plain icon buttons |
| `resources/views/filament-panels/components/page/sub-navigation/sidebar.blade.php` | Collapsed rail centering; `.fi-subnav-item-label` instead of hiding all spans |
| `resources/views/filament-forms/components/checkbox-list.blade.php` | `.fi-fo-checkbox-list-option` wrapper for Shield searchable lists |
| `resources/css/filament/admin/theme.css` | Subnav label truncation selector; removed experimental badged icon-button CSS |

### Livewire / Filament modal stability (2026-06-23)

Custom Flux-styled **Filament modals and dropdowns** (`fixed inset-0 flex …` markup) broke Alpine `x-show` / `filamentModal` initialization and blocked header clicks (notification bell, user menu). They are reverted to **stock Filament** structure for:

| File | Change |
|------|--------|
| `resources/views/filament/components/modal/index.blade.php` | Stock `fi-modal-window-ctn` / `fi-modal-close-overlay` markup (Alpine-safe) |
| `resources/views/filament/components/dropdown/index.blade.php` | Stock Filament dropdown; teleported panels use `x-float…shift.teleport.hide` |
| `resources/views/filament-panels/components/user-menu.blade.php` | Topbar user menu uses `<flux:dropdown>` instead of teleported Filament dropdown |
| `resources/views/filament-panels/components/layout/index.blade.php` | Header actions: `shrink-0 isolate overflow-visible`; database notifications eager-loaded |
| `resources/views/filament-panels/components/topbar/database-notifications-trigger.blade.php` | Thin override delegating to badged native icon button |

**Related app fixes (not in this package):** sanitize email `body_html` before preview (`joranski/filament-emails`); load `@fluxScripts` once; Flux modal chrome + transition fixes in `resources/css/filament/admin/theme.css`.

**v0.1.4:** Removed stray `wire:loading.attr=""` from database notifications trigger override.

**v0.1.3:** Restored Filament's lazy-loaded database notifications in the panel layout (`lazy => false` was forcing immediate mount + extra Livewire round-trips on every page, contributing to error toasts).

**v0.1.2:** Restored Flux zinc slide-out *appearance* via theme CSS while keeping stock Filament Alpine markup; removed `display:none !important` on closed modals (was killing slide-out animations); stable `wire:key` suffixes on modal sections.

**Tests:** `tests/Feature/Filament/FluxOverridesTest.php`
