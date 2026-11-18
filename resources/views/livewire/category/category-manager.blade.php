<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Add a category') }}
            </h3>

            <form wire:submit="create" class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input-label for="category-name" :value="__('Name')" />
                    <x-text-input wire:model="name" id="category-name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="category-color" :value="__('Color')" />
                    <input wire:model="color" id="category-color" type="color" class="mt-1 block h-10 w-16 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" />
                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                </div>

                <x-primary-button>
                    {{ __('Save') }}
                </x-primary-button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($categories as $category)
                <li wire:key="category-{{ $category->id }}" class="p-4 sm:p-6">
                    <form wire:submit="save({{ $category->id }})" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <x-input-label :for="'category-name-'.$category->id" :value="__('Name')" />
                            <x-text-input wire:model="forms.{{ $category->id }}.name" :id="'category-name-'.$category->id" class="block mt-1 w-full" type="text" required />
                            <x-input-error :messages="$errors->get('forms.'.$category->id.'.name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :for="'category-color-'.$category->id" :value="__('Color')" />
                            <input wire:model="forms.{{ $category->id }}.color" :id="'category-color-'.$category->id" type="color" class="mt-1 block h-10 w-16 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" />
                            <x-input-error :messages="$errors->get('forms.'.$category->id.'.color')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($category->is_system)
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Default') }}</span>
                            @else
                                <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="{{ __('Delete this category?') }}" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                    {{ __('Delete') }}
                                </button>
                            @endif

                            <x-primary-button>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
