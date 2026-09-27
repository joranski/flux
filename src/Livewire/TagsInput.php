<?php

declare(strict_types=1);

namespace Joranski\Flux\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * Free-text tag picker: pick from suggestions or create a new tag from the
 * search text. Bind it with `wire:model` on the parent; the value is a list of
 * tag names, so hosts decide how to persist them (e.g. spatie/laravel-tags).
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

    public function createTag(): void
    {
        $this->search = trim($this->search);

        if ($this->search === '') {
            return;
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
}
