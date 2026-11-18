<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response
        ->assertOk()
        ->assertSeeVolt('profile.update-profile-information-form')
        ->assertSeeVolt('profile.update-password-form')
        ->assertSeeVolt('profile.delete-user-form');
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $component
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('academic profile can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('filiere', 'Génie électrique et informatique')
        ->set('niveau', 'L3')
        ->set('semestre', 'S1')
        ->set('timezone', 'Africa/Porto-Novo')
        ->set('locale', 'fr')
        ->set('weekStartsOn', 1)
        ->call('updateProfileInformation');

    $component->assertHasNoErrors();

    $user->refresh();

    expect($user->filiere)->toBe('Génie électrique et informatique')
        ->and($user->niveau)->toBe('L3')
        ->and($user->semestre)->toBe('S1')
        ->and($user->timezone)->toBe('Africa/Porto-Novo')
        ->and($user->locale)->toBe('fr')
        ->and($user->week_starts_on)->toBe(1);
});

test('an invalid timezone is rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('timezone', 'Not/AZone')
        ->call('updateProfileInformation')
        ->assertHasErrors('timezone');

    expect($user->refresh()->timezone)->toBe('Africa/Porto-Novo');
});

test('updating a profile leaves another account unchanged', function () {
    $user = User::factory()->create();
    $other = User::factory()->create([
        'filiere' => 'Génie mécanique',
        'timezone' => 'UTC',
    ]);

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('filiere', 'GEI')
        ->set('timezone', 'Africa/Porto-Novo')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $other->refresh();

    expect($other->filiere)->toBe('Génie mécanique')
        ->and($other->timezone)->toBe('UTC');
});

test('the saved locale is used on the next request', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profile Information');

    Volt::test('profile.update-profile-information-form')
        ->set('locale', 'fr')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $this->actingAs($user->refresh())
        ->get('/profile')
        ->assertOk()
        ->assertSee('Informations du profil');
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $component
        ->assertHasErrors('password')
        ->assertNoRedirect();

    $this->assertNotNull($user->fresh());
});
