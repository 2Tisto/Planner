<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Revisions') }}</h1>

        @if ($subjects->isEmpty())
            <p class="mt-6 text-slate-600">{{ __('Add a subject first.') }}</p>
            <a href="{{ route('subjects.index') }}" wire:navigate class="mt-2 inline-block text-sm underline">{{ __('Subjects') }}</a>
        @else
            <form wire:submit="save" class="mt-6 space-y-3 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                <select wire:model="subjectId" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">{{ __('Subject') }}</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                <input wire:model="title" type="text" placeholder="{{ __('Add a chapter') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
                @error('title') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
                @error('subjectId') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Save') }}</button>
            </form>
        @endif

        <div class="mt-4 space-y-3">
            @foreach ($rows as $item)
                <article class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ $item['subject'] }}</p>
                    <h2 class="mt-1 text-lg font-medium">{{ $item['title'] }}</h2>
                    <button type="button" wire:click="advance({{ $item['id'] }})" class="mt-2 text-sm underline">{{ $item['stage'] }}</button>
                    <div class="mt-3 flex gap-2">
                        @foreach ([1, 2, 3, 4, 5] as $level)
                            <button type="button" wire:click="confidence({{ $item['id'] }}, {{ $level }})" class="rounded-2xl px-3 py-2 text-sm {{ $item['confidence'] === $level ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">{{ $level }}</button>
                        @endforeach
                    </div>
                    <button type="button" wire:click="remove({{ $item['id'] }})" class="mt-3 text-sm text-rose-700 underline">{{ __('Remove') }}</button>
                </article>
            @endforeach
        </div>
    </div>
</div>
