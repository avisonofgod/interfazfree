<x-filament-panels::page>
    <div class="space-y-4">
        @foreach ($this->getConfigurations() as $config)
            <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="flex-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $config['label'] }}
                    </dt>
                    <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                        {{ $config['value'] }}
                    </dd>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
