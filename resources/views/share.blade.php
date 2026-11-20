<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Shared week') }}</title>
        @include('layouts.pwa')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f6f4ef] font-sans text-slate-900 antialiased dark:bg-gray-900 dark:text-white">
        <main class="mx-auto max-w-3xl px-4 py-8">
            <p class="text-sm text-slate-500">{{ $owner }}</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Shared week') }}</h1>

            @if ($days === [])
                <p class="mt-6 text-slate-600">{{ __('Nothing this week.') }}</p>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($days as $date => $items)
                        <section class="rounded-[2rem] bg-white p-5 shadow-sm dark:bg-gray-800">
                            <h2 class="text-sm text-slate-500">{{ $date }}</h2>
                            <ul class="mt-3 space-y-2">
                                @foreach ($items as $item)
                                    <li>{{ $item['time'] }} · {{ $item['title'] }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @endif
        </main>
    </body>
</html>
