<div data-tags-input @unless ($allowCreate) data-tags-locked @endunless>
    <flux:pillbox
        wire:model.live="tags"
        variant="combobox"
        multiple
        :filter="false"
        :label="$label"
    >
        <x-slot name="input">
            <flux:pillbox.input
                wire:model.live.debounce.250ms="search"
                :placeholder="$placeholder ?? ($allowCreate ? __('Add tags...') : __('Choose tags...'))"
                maxlength="{{ $maxLength }}"
            />
        </x-slot>

        @foreach ($this->options as $option)
            <flux:pillbox.option :value="$option">{{ $option }}</flux:pillbox.option>
        @endforeach

        @if ($allowCreate)
            <flux:pillbox.option.create wire:click="createTag" min-length="1">
                {{ __('Create') }} "<span wire:text="search"></span>"
            </flux:pillbox.option.create>
        @else
            <flux:pillbox.option.empty>
                {{ __('No matching tag. Only suggested tags can be used.') }}
            </flux:pillbox.option.empty>
        @endif
    </flux:pillbox>

    <flux:error name="search" />
</div>
