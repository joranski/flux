<?php

declare(strict_types=1);

namespace Joranski\Flux\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * Tag picker: pick from suggestions or create a new tag from the search text.
 * Bind it with `wire:model` on the parent; the value is a list of tag names,
 * so hosts decide how to persist them (e.g. spatie/laravel-tags).
 *
 * With `:allow-create="false"` only suggestions can be added (typed text is
 * matched to a suggestion, ignoring case). Tags the record already had stay
 * selectable so locking a field never drops existing data. Hosts should still
 * validate on save, since the bound value comes from the browser.
 */
class TagsInput extends Component
{
    /** @var list<string> */
    #[Modelable]
    public array $tags = [];

    /** @var list<string> */
    public array $suggestions = [];

    public string $search = '';

    public ?string $label = null;

    public ?string $placeholder = null;

    public int $maxLength = 255;

    #[Locked]
    public bool $allowCreate = true;

    /** @var list<string> */
    #[Locked]
    public array $initialTags = [];

    public function mount(): void
    {
        $this->initialTags = $this->normalized($this->tags);
    }

    public function createTag(): void
    {
        $this->search = trim($this->search);

        if ($this->search === '') {
            return;
        }

        if (! $this->allowCreate) {
            $match = $this->allowedTag($this->search);

            if ($match === null) {
                $this->addError('search', __('Choose one of the suggested tags.'));

                return;
            }

            $this->search = $match;
        }

        $this->validate(['search' => ['string', 'max:'.$this->maxLength]]);

        if (! $this->hasTag($this->search)) {
            $this->tags = [...$this->tags, $this->search];
        }

        $this->search = '';
    }

    public function updatedSearch(): void
    {
        $this->resetErrorBag('search');
    }

    /**
     * @param  mixed  $value
     */
    public function updatedTags($value): void
    {
        if ($this->allowCreate) {
            return;
        }

        $this->tags = collect($this->normalized($this->tags))
            ->map(fn (string $tag): ?string => $this->allowedTag($tag))
            ->filter()
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    #[Computed]
    public function options(): array
    {
        $needle = Str::lower(trim($this->search));

        return collect([...$this->tags, ...$this->suggestions])
            ->map(fn (mixed $tag): string => (string) $tag)
            ->filter(fn (string $tag): bool => $tag !== '')
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->filter(fn (string $tag): bool => $needle === '' || Str::contains(Str::lower($tag), $needle))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    public function render(): View
    {
        return view('joranski-flux::livewire.tags-input');
    }

    private function hasTag(string $tag): bool
    {
        return collect($this->tags)->contains(fn (mixed $existing): bool => Str::lower((string) $existing) === Str::lower($tag));
    }

    /**
     * The suggestion (or previously held tag) matching $tag, ignoring case.
     */
    private function allowedTag(string $tag): ?string
    {
        $needle = Str::lower(trim($tag));

        return collect([...$this->normalized($this->suggestions), ...$this->initialTags])
            ->first(fn (string $allowed): bool => Str::lower($allowed) === $needle);
    }

    /**
     * @param  array<int, mixed>  $tags
     * @return list<string>
     */
    private function normalized(array $tags): array
    {
        return collect($tags)
            ->map(fn (mixed $tag): string => trim((string) $tag))
            ->filter(fn (string $tag): bool => $tag !== '')
            ->values()
            ->all();
    }
}
