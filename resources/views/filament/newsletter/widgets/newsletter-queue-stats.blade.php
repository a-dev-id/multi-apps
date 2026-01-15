<x-filament-widgets::widget>
    <x-filament::section wire:poll.10s>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-base font-semibold">Email Queue Status</div>
                <div class="text-sm text-gray-500">Sent / Pending / Failed per newsletter</div>
            </div>

            <div class="w-full sm:max-w-md">
                {{ $this->form }}
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <x-filament::card>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-paper-airplane" class="h-5 w-5" />
                    <div>
                        <div class="text-sm text-gray-500">Sent</div>
                        <div class="text-3xl font-bold">{{ number_format($this->sentCount) }}</div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5" />
                    <div>
                        <div class="text-sm text-gray-500">Pending</div>
                        <div class="text-3xl font-bold">{{ number_format($this->pendingCount) }}</div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5" />
                    <div>
                        <div class="text-sm text-gray-500">Failed</div>
                        <div class="text-3xl font-bold">{{ number_format($this->failedCount) }}</div>
                    </div>
                </div>
            </x-filament::card>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>