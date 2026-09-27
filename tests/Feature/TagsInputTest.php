<?php

declare(strict_types=1);

use Joranski\Flux\Livewire\TagsInput;
use Livewire\Component;
use Livewire\Livewire;

it('lists suggestions and selected tags as options', function (): void {
    Livewire::test(TagsInput::class, ['tags' => ['Legacy'], 'suggestions' => ['VIP', 'Rush', 'VIP']])
        ->assertSeeHtml('value="Legacy"')
        ->assertSeeHtml('value="Rush"')
        ->assertSeeHtml('value="VIP"')
        ->assertSeeHtml('data-flux-option-create');
});

it('filters options by the search text', function (): void {
    Livewire::test(TagsInput::class, ['suggestions' => ['Rush', 'VIP']])
        ->set('search', 'ru')
        ->assertSeeHtml('value="Rush"')
        ->assertDontSeeHtml('value="VIP"');
});

it('creates a trimmed tag from the search text and clears the search', function (): void {
    Livewire::test(TagsInput::class, ['tags' => ['VIP']])
        ->set('search', '  Wholesale  ')
        ->call('createTag')
        ->assertSet('tags', ['VIP', 'Wholesale'])
        ->assertSet('search', '');
});

it('does not duplicate a tag that is already selected, ignoring case', function (): void {
    Livewire::test(TagsInput::class, ['tags' => ['VIP']])
        ->set('search', 'vip')
        ->call('createTag')
        ->assertSet('tags', ['VIP']);
});

it('rejects tags longer than the max length', function (): void {
    Livewire::test(TagsInput::class, ['maxLength' => 5])
        ->set('search', 'TooLong')
        ->call('createTag')
        ->assertHasErrors('search')
        ->assertSet('tags', []);
});

it('ignores blank search text', function (): void {
    Livewire::test(TagsInput::class)
        ->set('search', '   ')
        ->call('createTag')
        ->assertSet('tags', []);
});

it('binds to the parent through wire:model', function (): void {
    $parent = new class extends Component
    {
        /** @var list<string> */
        public array $selected = ['VIP'];

        public function render(): string
        {
            return <<<'BLADE'
                <div><livewire:joranski-flux.tags-input wire:model="selected" :suggestions="['Rush']" label="Tags" /></div>
                BLADE;
        }
    };

    Livewire::test($parent::class)
        ->assertSeeHtml('value="VIP"')
        ->assertSeeHtml('value="Rush"')
        ->assertSee('Tags');
});
