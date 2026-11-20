<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Deadlines') }}</h1>

        <form wire:submit="save" class="mt-6 space-y-3 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <input wire:model="title" type="text" placeholder="{{ __('Title') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            @error('title') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
            <select wire:model="kind" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                <option value="ds">{{ __('Test') }}</option>
                <option value="exam">{{ __('Exam') }}</option>
                <option value="expose">{{ __('Talk') }}</option>
                <option value="projet">{{ __('Project') }}</option>
                <option value="rapport">{{ __('Report') }}</option>
            </select>
            <input wire:model="date" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="time" type="time" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="location" type="text" placeholder="{{ __('Place') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="coefficient" type="text" inputmode="decimal" placeholder="{{ __('Coefficient') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="subjectName" type="text" placeholder="{{ __('Subject') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Add a deadline') }}</button>
        </form>

        <div class="mt-4 space-y-3">
            @foreach ($rows as $row)
                <article class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ $row['kind'] }} · {{ $row['countdown'] }}</p>
                    <h2 class="mt-1 text-lg font-medium">{{ $row['title'] }}</h2>
                    <p class="text-sm text-slate-500">{{ $row['when'] }} @if ($row['location'] !== '') · {{ $row['location'] }} @endif</p>
                    <form wire:submit="saveGrade({{ $row['id'] }})" class="mt-3 flex gap-2">
                        <input wire:model="grades.{{ $row['id'] }}" type="text" inputmode="decimal" placeholder="{{ __('Grade') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
                        <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Save') }}</button>
                    </form>
                    <button type="button" wire:click="remove({{ $row['id'] }})" class="mt-3 text-sm text-rose-700 underline">{{ __('Remove') }}</button>
                </article>
            @endforeach
        </div>
    </div>
</div>
