<div class="min-h-screen bg-[#f6f4ef] dark:bg-gray-900">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('Weekly review') }}</h1>

        <section class="mt-6 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('Planned') }}</p>
            @if ($totals === [])
                <p class="mt-2 text-slate-600">{{ __('Nothing this week.') }}</p>
            @else
                <ul class="mt-2 space-y-1">
                    @foreach ($totals as $name => $minutes)
                        <li>{{ $name }} · {{ $minutes }} min</li>
                    @endforeach
                </ul>
            @endif
            <p class="mt-3 text-sm text-slate-600">{{ __('Done for real') }} {{ $actual }} min</p>
        </section>

        <form wire:submit="save" class="mt-4 space-y-3 rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
            <label class="block text-sm text-slate-500">{{ __('Wins') }}</label>
            <textarea wire:model="wins" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900"></textarea>
            <label class="block text-sm text-slate-500">{{ __('What was hard') }}</label>
            <textarea wire:model="hard" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900"></textarea>
            <label class="block text-sm text-slate-500">{{ __('Next week') }}</label>
            <textarea wire:model="nextStep" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900"></textarea>
            <label class="block text-sm text-slate-500">{{ __('Satisfaction') }}</label>
            <input wire:model="satisfaction" type="number" min="1" max="5" class="w-full rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700 dark:bg-gray-900" />
            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-white">{{ __('Save') }}</button>
        </form>
    </div>
</div>
