<div>
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
                :placeholder="$placeholder ?? __('Add tags...')"
                maxlength="{{ $maxLength }}"
            />
        </x-slot>

        @foreach ($this->options as $option)
            <flux:pillbox.option :value="$option">{{ $option }}</flux:pillbox.option>
        @endforeach

        <flux:pillbox.option.create wire:click="createTag" min-length="1">
            {{ __('Create') }} "<span wire:text="search"></span>"
        </flux:pillbox.option.create>
    </flux:pillbox>

    <flux:error name="search" />
</div>
