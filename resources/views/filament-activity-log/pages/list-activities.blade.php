@php
    use \Illuminate\Support\Js;
@endphp
<x-filament-panels::page>
    <div class="space-y-6">
        @foreach($this->getActivities() as $activityItem)

            @php
                /* @var \Spatie\Activitylog\Models\Activity $activityItem */
                $changes = $activityItem->getChangesAttribute();
            @endphp

            <div class="p-4 space-y-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-xl">
                <div class="p-1">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            @if ($activityItem->causer)
                                <x-filament-panels::avatar.user :user="$activityItem->causer" class="!w-8 !h-8"/>
                            @endif
                            <div class="flex flex-col text-start">
                                <span class="font-semibold text-zinc-950 dark:text-white">{{ $activityItem->causer?->name }}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('filament-activity-log::activities.events.' . $activityItem->event) }} &bull; {{ $activityItem->created_at->format(__('filament-activity-log::activities.default_datetime_format')) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col text-xs text-zinc-500 justify-end">
                            @if ($this->canRestoreActivity() && $changes->isNotEmpty())
                                <flux:button
                                    size="sm"
                                    icon="arrow-path"
                                    wire:click="restoreActivity({{ Js::from($activityItem->getKey()) }})"
                                >
                                    @lang('filament-activity-log::activities.table.restore')
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($changes->isNotEmpty())
                    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    <th class="px-4 py-2.5">
                                        {{ __('filament-activity-log::activities.table.field') }}
                                    </th>
                                    <th class="px-4 py-2.5">
                                        {{ __('filament-activity-log::activities.table.old') }}
                                    </th>
                                    <th class="px-4 py-2.5">
                                        {{ __('filament-activity-log::activities.table.new') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800/80">
                                @foreach (data_get($changes, 'attributes', []) as $field => $change)
                                    @php
                                        $oldValue = isset($changes['old'][$field]) ? $changes['old'][$field] : '';
                                        $newValue = isset($changes['attributes'][$field]) ? $changes['attributes'][$field] : '';
                                    @endphp
                                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/20 text-zinc-700 dark:text-zinc-300">
                                        <td class="px-4 py-2.5 font-medium text-zinc-900 dark:text-zinc-200 align-top" width="20%">
                                            {{ $this->getFieldLabel($field) }}
                                        </td>
                                        <td width="40%" class="px-4 py-2.5 align-top break-all whitespace-normal">
                                            @if(is_array($oldValue))
                                                <pre class="text-xs font-mono bg-zinc-50 dark:bg-zinc-950/50 p-2 rounded text-zinc-500 dark:text-zinc-500 border border-zinc-200/50 dark:border-zinc-800/50 overflow-x-auto">{{ json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ $oldValue }}
                                            @endif
                                        </td>
                                        <td width="40%" class="px-4 py-2.5 align-top break-all whitespace-normal">
                                            @if (is_bool($newValue))
                                                <span class="text-xs font-medium px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">{{ $newValue ? 'true' : 'false' }}</span>
                                            @elseif(is_array($newValue))
                                                <pre class="text-xs font-mono bg-zinc-50 dark:bg-zinc-950/50 p-2 rounded text-zinc-500 dark:text-zinc-500 border border-zinc-200/50 dark:border-zinc-800/50 overflow-x-auto">{{ json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ $newValue }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

        <x-filament::pagination
            currentPageOptionProperty="recordsPerPage"
            :page-options="$this->getRecordsPerPageSelectOptions()"
            :paginator="$this->getActivities()"
        />
    </div>
</x-filament-panels::page>
