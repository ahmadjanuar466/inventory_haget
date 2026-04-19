<?php

namespace App\Livewire\Category;

use App\Livewire\Category\Traits\Delete;
use App\Livewire\Category\Traits\Insert;
use App\Livewire\Category\Traits\Update;
use App\Models\Categories;
use App\Services\Categories\CategoryServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesIndex extends Component
{
    use Delete;
    use Insert;
    use Update;
    use WithPagination;

    protected CategoryServices $categoryServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Product', 'routes' => ''],
        ['title' => 'List Category Product', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Category Product Management',
        'subtitle' => 'Manage product category codes, names, and category hierarchy.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'parent_id' => '',
    ];

    public array $createForm = [
        'parent_id' => '',
        'code' => '',
        'name' => '',
    ];

    public array $editForm = [
        'parent_id' => '',
        'code' => '',
        'name' => '',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingCategoryId = null;

    public ?int $deletingCategoryId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.parent_id' => ['except' => ''],
    ];

    public function boot(CategoryServices $categoryServices): void
    {
        $this->categoryServices = $categoryServices;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = $this->resolvePerPage((int) $value);
        $this->resetPage();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        return view('livewire.category.categories-index', [
            'categories' => $this->categoryServices->paginateCategories($this->search, $perPage, $this->filters),
            'parentFilterOptions' => Categories::query()->whereNull('parent_id')->orderBy('name')->get(),
            'parentOptions' => Categories::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Category Product Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $categoryId): array
    {
        return $this->rulesFor('editForm', $categoryId);
    }

    protected function rulesFor(string $form, ?int $categoryId = null): array
    {
        $codeRule = Rule::unique('categories', 'code');
        $parentRules = ['nullable', Rule::exists('categories', 'id')];

        if ($categoryId) {
            $codeRule->ignore($categoryId);
            $parentRules[] = Rule::notIn([$categoryId]);
        }

        return [
            "{$form}.parent_id" => $parentRules,
            "{$form}.code" => ['required', 'string', 'max:15', $codeRule],
            "{$form}.name" => ['required', 'string', 'max:150'],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.parent_id" => __('Parent Category'),
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
        ];
    }

    protected function formErrorKeys(string $form): array
    {
        return array_keys($this->formAttributes($form));
    }

    protected function defaultForm(): array
    {
        return [
            'parent_id' => '',
            'code' => '',
            'name' => '',
        ];
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
