<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <p class="text-sm text-slate-500">{{ $label }}</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Today') }}</h1>

        <section aria-label="{{ __('Right now') }}" class="mt-6 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('Right now') }}</p>
            @if ($focus['current'])
                <a href="{{ route('planning.board') }}" wire:navigate class="mt-2 block">
                    <span class="block h-2 w-10 rounded-full" style="background: {{ $focus['current']['color'] }}"></span>
                    <span class="mt-2 block text-lg font-medium">{{ $focus['current']['title'] }}</span>
                    <span class="block text-sm text-slate-500">{{ $focus['current']['time'] }}</span>
                </a>
            @else
                <p class="mt-2 text-lg">
                    @if ($focus['quiet'] === 'waiting')
                        {{ __('Nothing right now.') }}
                    @elseif ($focus['quiet'] === 'clear')
                        {{ __('Nothing left today.') }}
                    @else
                        {{ __('The day is open.') }}
                    @endif
                </p>
            @endif

            @if ($focus['next'])
                <a href="{{ route('planning.board') }}" wire:navigate class="mt-4 block border-t border-slate-100 pt-4 dark:border-gray-700">
                    <span class="text-xs uppercase tracking-wide text-slate-500">{{ __('Next up') }}</span>
                    <span class="mt-1 block font-medium">{{ $focus['next']['title'] }}</span>
                    <span class="block text-sm text-slate-500">{{ $focus['next']['time'] }}</span>
                </a>
            @endif

            <a href="{{ route('planning.board') }}" wire:navigate class="mt-4 inline-block text-sm text-slate-600 underline">{{ __('See the day') }}</a>
        </section>

        <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('Hours') }}</p>
            <p class="mt-2">{{ __('Planned') }} {{ $planned }} · {{ __('Done for real') }} {{ $actual }}</p>
            <button type="button" wire:click="note" class="mt-3 rounded-2xl bg-slate-900 px-4 py-3 text-sm text-white">{{ __('Note 25 minutes') }}</button>
            @if ($free)
                <p class="mt-3 text-sm text-slate-600">{{ __('A free hour') }} {{ $free }}</p>
            @endif
        </section>

        @if ($doing->isNotEmpty())
            <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('In progress') }}</p>
                <ul class="mt-3 space-y-2">
                    @foreach ($doing as $task)
                        <li>
                            <a href="{{ route('tasks.index') }}" wire:navigate class="text-lg font-medium">{{ $task->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('Three priorities') }}</p>
                <a href="{{ route('tasks.index') }}" wire:navigate class="text-sm text-slate-600 underline">{{ __('Tasks') }}</a>
            </div>
            @if ($priorities->isEmpty())
                <p class="mt-3 text-slate-600">{{ __('No open task.') }}</p>
            @else
                <ul class="mt-3 space-y-2">
                    @foreach ($priorities as $task)
                        <li>
                            <a href="{{ route('tasks.index') }}" wire:navigate>{{ $task->priority->value }} · {{ $task->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($overdue->isNotEmpty())
                <p class="mt-4 text-xs uppercase tracking-wide text-rose-700">{{ __('Overdue') }}</p>
                <ul class="mt-2 space-y-2">
                    @foreach ($overdue as $task)
                        <li>
                            <a href="{{ route('tasks.index') }}" wire:navigate>{{ $task->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('Coming up') }}</p>
                <a href="{{ route('deadlines.index') }}" wire:navigate class="text-sm text-slate-600 underline">{{ __('Deadlines') }}</a>
            </div>
            @if ($coming === [])
                <p class="mt-3 text-slate-600">{{ __('No deadline.') }}</p>
            @else
                <ul class="mt-3 space-y-2">
                    @foreach ($coming as $item)
                        <li>{{ $item['title'] }} · {{ $item['countdown'] }} · {{ $item['when'] }}</li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($focus['today'] !== [])
            <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('My day') }}</p>
                <ul class="mt-3 space-y-2">
                    @foreach ($focus['today'] as $item)
                        <li>{{ $item['time'] }} · {{ $item['title'] }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="mt-4 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <a href="{{ route('planning.calendar') }}" class="text-sm text-slate-700 underline">{{ __('Download the week') }}</a>
            <button type="button" wire:click="share" class="mt-3 block text-sm text-slate-700 underline">{{ __('Share the week') }}</button>
            @if ($shareUrl !== '')
                <p class="mt-2 break-all text-sm text-slate-500">{{ $shareUrl }}</p>
            @endif
        </section>
    </div>
</div>
