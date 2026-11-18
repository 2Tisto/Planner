<?php

use App\Actions\Category\SeedDefaultCategories;
use App\Livewire\Category\CategoryManager;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

test('registration seeds twelve default categories once', function () {
    $user = User::factory()->create();
    $seed = app(SeedDefaultCategories::class);

    $seed($user);
    $seed($user);

    expect($user->categories()->count())->toBe(12)
        ->and($user->categories()->where('is_system', true)->count())->toBe(12)
        ->and($user->categories()->where('name', 'Cours')->value('color'))->toBe('#bfdbfe');
});

test('a user can create, rename and delete a category', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(CategoryManager::class)
        ->set('name', 'Stage')
        ->set('color', '#fde68a')
        ->call('create')
        ->assertHasNoErrors();

    $category = Category::query()->forUser($user)->where('name', 'Stage')->first();

    expect($category)->not->toBeNull()
        ->and($category->color)->toBe('#fde68a')
        ->and($category->is_system)->toBeFalse();

    Livewire::test(CategoryManager::class)
        ->set('forms.'.$category->id.'.name', 'Stage GEI')
        ->set('forms.'.$category->id.'.color', '#BFDBFE')
        ->call('save', $category->id)
        ->assertHasNoErrors();

    expect($category->refresh()->name)->toBe('Stage GEI');

    Livewire::test(CategoryManager::class)
        ->call('delete', $category->id)
        ->assertHasNoErrors();

    expect($category->fresh())->toBeNull();
});

test('a system category cannot be deleted', function () {
    $user = User::factory()->create();
    app(SeedDefaultCategories::class)($user);
    $category = Category::query()->forUser($user)->where('name', 'Cours')->firstOrFail();

    $this->actingAs($user);

    Livewire::test(CategoryManager::class)
        ->call('delete', $category->id)
        ->assertForbidden();

    expect($category->fresh())->not->toBeNull();
});

test('a user cannot update another account category', function () {
    $owner = User::factory()->create();
    $category = Category::factory()->for($owner)->create(['name' => 'Secret']);
    $intruder = User::factory()->create();

    $this->actingAs($intruder);

    Livewire::test(CategoryManager::class)
        ->call('save', $category->id)
        ->assertNotFound();

    expect($category->refresh()->name)->toBe('Secret');
});

test('two accounts can use the same category name', function () {
    $first = User::factory()->create();
    $second = User::factory()->create();

    Category::factory()->for($first)->create(['name' => 'Stage']);

    $this->actingAs($second);

    Livewire::test(CategoryManager::class)
        ->set('name', 'Stage')
        ->set('color', '#BFDBFE')
        ->call('create')
        ->assertHasNoErrors();

    expect(Category::query()->where('name', 'Stage')->count())->toBe(2);
});

test('categories page is limited to the signed-in account', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    app(SeedDefaultCategories::class)($user);
    Category::factory()->for($other)->create(['name' => 'Secret']);

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Cours')
        ->assertDontSee('Secret');
});

test('guests and unverified users cannot open categories', function () {
    $this->get(route('categories.index'))->assertRedirect(route('login'));

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('categories.index'))
        ->assertRedirect(route('verification.notice'));
});
