<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Tasks') }}</h1>

        <form wire:submit="save" class="mt-6 space-y-3 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <input wire:model="title" type="text" placeholder="{{ __('What are you planning?') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            @error('title') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                @foreach (['A', 'B', 'C'] as $letter)
                    <button type="button" wire:click="$set('priority', '{{ $letter }}')" class="rounded-2xl px-4 py-3 {{ $priority === $letter ? 'bg-slate-900 text-white' : 'border border-slate-200' }}">{{ $letter }}</button>
                @endforeach
            </div>
            <input wire:model="due" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="estimate" type="number" min="5" max="600" placeholder="{{ __('Estimate') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <input wire:model="subjectName" type="text" placeholder="{{ __('Subject') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Add a task') }}</button>
        </form>

        <div class="mt-4 space-y-3">
            @foreach ($tasks as $task)
                <article class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                    <button type="button" wire:click="cycle({{ $task->id }})" class="text-xs uppercase tracking-wide text-slate-500">
                        @if ($task->status === \App\Enums\TaskStatus::Doing)
                            {{ __('In progress') }}
                        @elseif ($task->status === \App\Enums\TaskStatus::Done)
                            {{ __('Done') }}
                        @else
                            {{ __('To do') }}
                        @endif
                        · {{ $task->priority->value }}
                    </button>
                    <button type="button" wire:click="open({{ $task->id }})" class="mt-1 block text-left text-lg font-medium">{{ $task->title }}</button>
                    @if ($task->due_on)
                        <p class="text-sm text-slate-500">{{ __('Due') }} {{ $task->due_on->format('d/m') }}</p>
                    @endif

                    @if ($openId === $task->id)
                        <ul class="mt-3 space-y-2">
                            @foreach ($task->subtasks as $step)
                                <li>
                                    <button type="button" wire:click="toggleStep({{ $step->id }})" class="text-left {{ $step->done ? 'text-slate-400 line-through' : '' }}">{{ $step->title }}</button>
                                </li>
                            @endforeach
                        </ul>
                        <form wire:submit="addStep({{ $task->id }})" class="mt-3 flex gap-2">
                            <input wire:model="step" type="text" placeholder="{{ __('Add a step') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
                            <button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Save') }}</button>
                        </form>
                        <button type="button" wire:click="note({{ $task->id }})" class="mt-3 text-sm underline">{{ __('Note 25 minutes') }}</button>
                    @endif

                    <button type="button" wire:click="remove({{ $task->id }})" class="mt-3 block text-sm text-rose-700 underline">{{ __('Remove') }}</button>
                </article>
            @endforeach
        </div>
    </div>
</div>
