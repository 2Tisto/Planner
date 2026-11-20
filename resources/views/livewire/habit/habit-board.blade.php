<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Habits') }}</h1>

        <form wire:submit="save" class="mt-6 flex flex-col gap-2 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800 sm:flex-row">
            <input wire:model="name" type="text" placeholder="{{ __('Add a habit') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Save') }}</button>
        </form>
        @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror

        <div class="mt-4 space-y-3">
            @foreach ($rows as $habit)
                <article class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h2 class="text-lg font-medium">{{ $habit['name'] }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Streak') }} {{ trans_choice(':count day|:count days', $habit['streak'], ['count' => $habit['streak']]) }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($habit['days'] as $day)
                            <button
                                type="button"
                                wire:click="toggle({{ $habit['id'] }}, '{{ $day['date'] }}')"
                                class="rounded-2xl px-3 py-2 text-sm {{ $day['done'] ? 'bg-slate-900 text-white' : 'border border-slate-200' }}"
                            >{{ $day['label'] }}</button>
                        @endforeach
                    </div>
                    <button type="button" wire:click="remove({{ $habit['id'] }})" class="mt-3 text-sm text-rose-700 underline">{{ __('Remove') }}</button>
                </article>
            @endforeach
        </div>
    </div>
</div>
