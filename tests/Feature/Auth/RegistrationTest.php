<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::query()->where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->categories)->toHaveCount(12)
        ->and($user->categories->pluck('name')->all())->toContain('Cours', 'Sommeil');

    $this->get(route('dashboard'))->assertRedirect(route('verification.notice', absolute: false));
});
