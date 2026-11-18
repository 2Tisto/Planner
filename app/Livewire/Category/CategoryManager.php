<?php

declare(strict_types=1);

namespace App\Livewire\Category;

use App\Actions\Category\CreateCategory;
use App\Actions\Category\DeleteCategory;
use App\Actions\Category\UpdateCategory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryManager extends Component
{
    public string $name = '';

    public string $color = '#BFDBFE';

    /** @var array<string, array{name: string, color: string}> */
    public array $forms = [];

    public function mount(): void
    {
        $this->loadForms();
    }

    public function create(CreateCategory $create): void
    {
        $user = $this->user();
        $this->authorize('create', Category::class);

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('categories', 'name')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $create($user, $validated['name'], $validated['color']);

        $this->reset('name');
        $this->color = '#BFDBFE';
        $this->loadForms();
    }

    public function save(int $categoryId, UpdateCategory $update): void
    {
        $user = $this->user();
        $category = $this->owned($categoryId);
        $this->authorize('update', $category);

        $this->validate([
            "forms.{$categoryId}.name" => [
                'required',
                'string',
                'max:80',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where('user_id', $user->id))
                    ->ignore($category->id),
            ],
            "forms.{$categoryId}.color" => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $payload = $this->form($category);

        $update($category, $payload['name'], $payload['color']);

        $this->loadForms();
    }

    public function delete(int $categoryId, DeleteCategory $delete): void
    {
        $category = $this->owned($categoryId);
        $this->authorize('delete', $category);

        $delete($category);
        $this->loadForms();
    }

    public function render(): View
    {
        return view('livewire.category.category-manager', [
            'categories' => $this->orderedCategories(),
        ]);
    }

    /**
     * @return Collection<int, Category>
     */
    private function orderedCategories(): Collection
    {
        return Category::query()
            ->forUser($this->user())
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    private function loadForms(): void
    {
        $this->forms = [];

        foreach ($this->orderedCategories() as $category) {
            $this->forms[$this->formKey($category)] = [
                'name' => $category->name,
                'color' => $category->color,
            ];
        }
    }

    /**
     * @return array{name: string, color: string}
     */
    private function form(Category $category): array
    {
        $key = $this->formKey($category);

        foreach ($this->forms as $formKey => $payload) {
            if ((string) $formKey === $key) {
                return $payload;
            }
        }

        abort(404);
    }

    private function formKey(Category $category): string
    {
        return (string) $category->getKey();
    }

    private function owned(int $categoryId): Category
    {
        $category = Category::query()->forUser($this->user())->find($categoryId);

        if (! $category instanceof Category) {
            abort(404);
        }

        return $category;
    }

    private function user(): User
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }
}
