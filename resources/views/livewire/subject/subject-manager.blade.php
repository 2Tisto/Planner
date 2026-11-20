<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Subjects') }}</h1>

        <form wire:submit="save" class="mt-6 space-y-3 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <input wire:model="name" type="text" placeholder="{{ __('Name') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            @error('name') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
            <input wire:model="code" type="text" placeholder="{{ __('Code') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="teacher" type="text" placeholder="{{ __('Teacher') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="room" type="text" placeholder="{{ __('Room') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="coefficient" type="text" inputmode="decimal" placeholder="{{ __('Coefficient') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="color" type="color" class="h-12 w-full rounded-2xl border border-slate-200" />
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ $editingId ? __('Update') : __('Save') }}</button>
            @if ($editingId)
                <button type="button" wire:click="cancel" class="w-full text-sm text-slate-500">{{ __('Cancel') }}</button>
            @endif
        </form>

        <div class="mt-4 space-y-3">
            @foreach ($subjects as $subject)
                <article class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                    <span class="block h-2 w-10 rounded-full" style="background: {{ $subject->color }}"></span>
                    <h2 class="mt-2 text-lg font-medium">{{ $subject->name }}</h2>
                    <p class="text-sm text-slate-500">
                        {{ $subject->teacher }}
                        @if ($subject->default_room) · {{ $subject->default_room }} @endif
                        @if ($subject->coefficient) · {{ $subject->coefficient }} @endif
                    </p>
                    <div class="mt-3 flex gap-4 text-sm">
                        <button type="button" wire:click="edit({{ $subject->id }})" class="underline">{{ __('Update') }}</button>
                        <button type="button" wire:click="remove({{ $subject->id }})" class="text-rose-700 underline">{{ __('Remove') }}</button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</div>
