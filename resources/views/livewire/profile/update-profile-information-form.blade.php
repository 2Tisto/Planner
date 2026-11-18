<?php

use App\Actions\Profile\UpdateProfileInformation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $filiere = '';

    public string $niveau = '';

    public string $semestre = '';

    public string $timezone = 'Africa/Porto-Novo';

    public string $locale = 'fr';

    public int $weekStartsOn = 1;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->filiere = $user->filiere ?? '';
        $this->niveau = $user->niveau ?? '';
        $this->semestre = $user->semestre ?? '';
        $this->timezone = $user->timezone;
        $this->locale = $user->locale;
        $this->weekStartsOn = $user->week_starts_on;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(UpdateProfileInformation $updateProfileInformation): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'filiere' => ['nullable', 'string', 'max:120'],
            'niveau' => ['nullable', 'string', 'max:20'],
            'semestre' => ['nullable', 'string', 'max:32'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'locale' => ['required', 'string', Rule::in(['fr', 'en'])],
            'weekStartsOn' => ['required', 'integer', Rule::in([0, 1])],
        ]);

        $updateProfileInformation($user, [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'filiere' => $validated['filiere'],
            'niveau' => $validated['niveau'],
            'semestre' => $validated['semestre'],
            'timezone' => $validated['timezone'],
            'locale' => $validated['locale'],
            'week_starts_on' => $validated['weekStartsOn'],
        ]);

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * @return list<string>
     */
    public function timezones(): array
    {
        return timezone_identifiers_list();
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Update your name, email, track and timezone.') }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="filiere" :value="__('Track')" />
            <x-text-input wire:model="filiere" id="filiere" name="filiere" type="text" class="mt-1 block w-full" autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('filiere')" />
        </div>

        <div>
            <x-input-label for="niveau" :value="__('Level')" />
            <x-text-input wire:model="niveau" id="niveau" name="niveau" type="text" class="mt-1 block w-full" list="niveaux" />
            <datalist id="niveaux">
                <option value="L1"></option>
                <option value="L2"></option>
                <option value="L3"></option>
                <option value="M1"></option>
                <option value="M2"></option>
            </datalist>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('For example L2 or M1.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('niveau')" />
        </div>

        <div>
            <x-input-label for="semestre" :value="__('Semester')" />
            <x-text-input wire:model="semestre" id="semestre" name="semestre" type="text" class="mt-1 block w-full" />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('For example S1.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('semestre')" />
        </div>

        <div>
            <x-input-label for="timezone" :value="__('Timezone')" />
            <select wire:model="timezone" id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                @foreach ($this->timezones() as $zone)
                    <option value="{{ $zone }}">{{ $zone }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
        </div>

        <div>
            <x-input-label for="locale" :value="__('Language')" />
            <select wire:model="locale" id="locale" name="locale" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="fr">{{ __('French') }}</option>
                <option value="en">{{ __('English') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('locale')" />
        </div>

        <div>
            <x-input-label for="weekStartsOn" :value="__('Week starts on')" />
            <select wire:model="weekStartsOn" id="weekStartsOn" name="weekStartsOn" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="1">{{ __('Monday') }}</option>
                <option value="0">{{ __('Sunday') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('weekStartsOn')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
