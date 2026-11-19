<div class="min-h-[calc(100vh-4rem)] bg-[#f6f4ef] dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $board['label'] }}</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-800 dark:text-slate-100 capitalize">{{ $board['title'] }}</h1>
            </div>

            <button type="button" wire:click="openQuickAdd" class="shrink-0 rounded-full bg-slate-800 px-5 py-3 text-sm font-medium text-white shadow-sm hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                {{ __('Add a moment') }}
            </button>
        </div>

        <div class="mt-6 flex items-center gap-2">
            <button type="button" wire:click="shiftDay(-1)" class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-lg text-slate-500 shadow-sm hover:text-slate-800 dark:bg-gray-800 dark:text-slate-300" aria-label="{{ __('Previous day') }}">
                ‹
            </button>
            <button type="button" wire:click="goToToday" class="rounded-full px-4 py-2 text-sm text-slate-600 hover:bg-white dark:text-slate-300 dark:hover:bg-gray-800">
                {{ __('Today') }}
            </button>
            <button type="button" wire:click="shiftDay(1)" class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-lg text-slate-500 shadow-sm hover:text-slate-800 dark:bg-gray-800 dark:text-slate-300" aria-label="{{ __('Next day') }}">
                ›
            </button>
        </div>

        <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
            @foreach ($board['days'] as $day)
                <button
                    type="button"
                    wire:click="selectDate('{{ $day['date'] }}')"
                    wire:key="day-{{ $day['date'] }}"
                    @class([
                        'shrink-0 rounded-2xl px-3 py-2 text-left transition',
                        'bg-white shadow-sm dark:bg-gray-800' => $day['selected'],
                        'hover:bg-white/70 dark:hover:bg-gray-800/70' => ! $day['selected'],
                    ])
                >
                    <span @class(['block text-xs', 'text-slate-800 dark:text-slate-100' => $day['today'], 'text-slate-400' => ! $day['today']])>{{ $day['weekday'] }}</span>
                    <span class="block text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $day['number'] }}</span>
                </button>
            @endforeach
        </div>

        <p class="mt-5 text-sm text-slate-500 dark:text-slate-400">{{ $board['hint'] }}</p>

        <div class="mt-3 flex">
            <div class="w-12 shrink-0">
                @foreach ($board['slots'] as $slot)
                    <div class="flex h-12 items-start">
                        <span class="text-[11px] leading-none text-slate-400">{{ $slot['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex min-w-0 flex-1 gap-1 sm:gap-2">
                @foreach ($board['days'] as $day)
                    <div data-day="{{ $day['date'] }}" @class(['relative min-w-0 flex-1', 'hidden md:block' => ! $day['selected']]) wire:key="column-{{ $day['date'] }}">
                        @foreach ($board['slots'] as $slot)
                            <button
                                type="button"
                                wire:click="openCreate('{{ $day['date'] }}', '{{ $slot['time'] }}')"
                                data-slot
                                class="block h-12 w-full border-t border-slate-200/80 text-left hover:bg-white/80 dark:border-slate-700 dark:hover:bg-gray-800/80 {{ $slot['label'] === null ? 'border-slate-100 dark:border-slate-800' : '' }}"
                                aria-label="{{ __('Add a moment') }} {{ $slot['time'] }}"
                            ></button>
                        @endforeach

                        @if ($day['now'] !== null)
                            <div class="pointer-events-none absolute inset-x-0 z-20" style="top: {{ $day['now'] }}rem">
                                <div class="h-px bg-rose-300/90"></div>
                            </div>
                        @endif

                        @foreach ($day['blocks'] as $block)
                            <div
                                wire:key="block-{{ $day['date'] }}-{{ $block['id'] }}"
                                role="button"
                                tabindex="0"
                                aria-label="{{ $block['title'] }} {{ $block['time'] }}"
                                class="absolute inset-x-1 z-10 cursor-grab touch-none select-none overflow-hidden rounded-2xl px-2 py-1.5 text-left text-slate-800 shadow-sm active:cursor-grabbing"
                                style="background: {{ $block['color'] }}"
                                x-data="{
                                    top: {{ $block['top'] }},
                                    height: {{ $block['height'] }},
                                    originTop: {{ $block['top'] }},
                                    originHeight: {{ $block['height'] }},
                                    dragging: false,
                                    resizing: false,
                                    moved: false,
                                    startY: 0,
                                    startX: 0,
                                    pointerId: null,
                                    stepPx() {
                                        const slot = this.$el.parentElement?.querySelector('[data-slot]');
                                        return slot ? slot.getBoundingClientRect().height / 2 : 24;
                                    },
                                    startMove(event) {
                                        if (event.button !== undefined && event.button !== 0) return;
                                        this.dragging = true;
                                        this.moved = false;
                                        this.startY = event.clientY;
                                        this.startX = event.clientX;
                                        this.pointerId = event.pointerId;
                                        this.$el.setPointerCapture(event.pointerId);
                                    },
                                    startResize(event) {
                                        this.resizing = true;
                                        this.moved = false;
                                        this.startY = event.clientY;
                                        this.pointerId = event.pointerId;
                                        this.$el.setPointerCapture(event.pointerId);
                                    },
                                    onPointerMove(event) {
                                        if (! this.dragging && ! this.resizing) return;
                                        const delta = Math.round((event.clientY - this.startY) / this.stepPx()) * 1.5;
                                        if (Math.abs(event.clientY - this.startY) > 4 || Math.abs(event.clientX - this.startX) > 4) this.moved = true;
                                        if (this.dragging) {
                                            const limit = Math.max(0, 102 - this.originHeight);
                                            this.top = Math.min(Math.max(0, this.originTop + delta), limit);
                                            return;
                                        }
                                        this.height = Math.max(1.5, Math.min(this.originHeight + delta, 102 - this.top));
                                    },
                                    clock(total) {
                                        const minutes = Math.max(0, Math.min(total, 24 * 60));
                                        const hour = Math.floor(minutes / 60);
                                        const minute = minutes % 60;
                                        return String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                                    },
                                    async onPointerUp(event) {
                                        if (! this.dragging && ! this.resizing) return;
                                        const wasResize = this.resizing;
                                        const didMove = this.moved;
                                        this.dragging = false;
                                        this.resizing = false;
                                        if (! didMove) {
                                            this.$wire.openEdit({{ $block['id'] }});
                                            return;
                                        }
                                        const startMinutes = Math.round(this.top / 1.5) * 15 + 7 * 60;
                                        if (wasResize) {
                                            const length = Math.max(15, Math.round(this.height / 1.5) * 15);
                                            await this.$wire.resizeBlock({{ $block['id'] }}, this.clock(startMinutes + length));
                                            return;
                                        }
                                        const column = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-day]');
                                        const date = column?.dataset.day ?? '{{ $day['date'] }}';
                                        await this.$wire.moveBlock({{ $block['id'] }}, date, this.clock(startMinutes));
                                    },
                                }"
                                x-bind:style="`top:${top}rem;height:${height}rem;background:{{ $block['color'] }}`"
                                x-on:pointerdown="startMove($event)"
                                x-on:pointermove="onPointerMove($event)"
                                x-on:pointerup="onPointerUp($event)"
                                x-on:pointercancel="onPointerUp($event)"
                            >
                                <span class="block truncate text-sm font-medium leading-tight">{{ $block['title'] }}</span>
                                <span class="block truncate text-xs text-slate-700/80">{{ $block['time'] }}</span>
                                @if ($block['meta'] !== '')
                                    <span class="block truncate text-xs text-slate-700/70">{{ $block['meta'] }}</span>
                                @endif
                                <span
                                    data-resize
                                    class="absolute inset-x-2 bottom-1 h-1.5 rounded-full bg-slate-800/20"
                                    x-on:pointerdown.stop="startResize($event)"
                                    aria-hidden="true"
                                ></span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($composerOpen)
        <div class="fixed inset-0 z-40 bg-slate-900/10 backdrop-blur-[2px]" wire:click="closeComposer"></div>

        <section class="fixed inset-x-0 bottom-0 z-50 max-h-[90vh] overflow-y-auto rounded-t-[2rem] bg-[#fffdf8] px-6 py-6 shadow-2xl dark:bg-gray-800 sm:px-8 md:inset-y-0 md:left-auto md:right-0 md:w-[26rem] md:max-h-none md:rounded-none">
            <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-slate-200 md:hidden"></div>

            <div class="flex items-start justify-between gap-4">
                <h2 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">
                    {{ $editingId ? __('Adjust this block') : __('What are you planning?') }}
                </h2>
                <button type="button" wire:click="closeComposer" class="text-sm text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
                    {{ __('Close') }}
                </button>
            </div>

            <form wire:submit="save" class="mt-6 space-y-5">
                <div>
                    <x-input-label for="moment-title" :value="__('Title')" />
                    <x-text-input wire:model="title" id="moment-title" type="text" class="mt-1 block w-full text-base" placeholder="{{ __('Networks, sport, revision…') }}" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label :value="__('Color')" />
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <button
                                type="button"
                                wire:click="$set('categoryId', {{ $category->id }})"
                                @class([
                                    'rounded-full px-3 py-1 text-sm text-slate-800',
                                    'ring-2 ring-slate-700 ring-offset-2 dark:ring-offset-gray-800' => $categoryId === $category->id,
                                ])
                                style="background: {{ $category->color }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('categoryId')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="moment-starts" :value="__('From')" />
                        <x-text-input wire:model="starts" id="moment-starts" type="time" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('starts')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="moment-ends" :value="__('To')" />
                        <x-text-input wire:model="ends" id="moment-ends" type="time" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('ends')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="moment-subject" :value="__('Subject')" />
                    <x-text-input wire:model="subjectName" id="moment-subject" type="text" class="mt-1 block w-full" placeholder="{{ __('Optional') }}" />
                    <x-input-error :messages="$errors->get('subjectName')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="moment-room" :value="__('Room')" />
                    <x-text-input wire:model="location" id="moment-room" type="text" class="mt-1 block w-full" placeholder="{{ __('Optional') }}" />
                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between gap-3 pt-2">
                    @if ($editingId)
                        <div class="flex items-center gap-4">
                            <button type="button" wire:click="duplicate" class="text-sm text-slate-600 hover:underline dark:text-slate-300">
                                {{ __('Duplicate') }}
                            </button>
                            <button type="button" wire:click="remove" wire:confirm="{{ __('Remove this block?') }}" class="text-sm text-rose-700 hover:underline dark:text-rose-300">
                                {{ __('Remove') }}
                            </button>
                        </div>
                    @else
                        <span></span>
                    @endif

                    <button type="submit" class="rounded-full bg-slate-800 px-5 py-3 text-sm font-medium text-white hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900" wire:loading.attr="disabled">
                        {{ $editingId ? __('Update') : __('Place it') }}
                    </button>
                </div>
            </form>
        </section>
    @endif
</div>
