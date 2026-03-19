<?php

namespace App\Livewire\Usermanagement;

use App\Services\Usermanagement\PermissionServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionsIndex extends Component
{
    use WithPagination;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'User Management', 'routes' => ''],
        ['title' => 'Permissions', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Permission Management',
        'subtitle' => 'Manage the individual permissions that can be attached to roles and users.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $createForm = [
        'name' => '',
        'guard_name' => 'web',
    ];

    public array $editForm = [
        'name' => '',
        'guard_name' => 'web',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingPermissionId = null;

    public ?int $deletingPermissionId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected PermissionServices $permissionServices;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(PermissionServices $permissionServices): void
    {
        $this->permissionServices = $permissionServices;
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

    public function openCreateModal(): void
    {
        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $this->reset('createForm');
        $this->createForm['guard_name'] = 'web';

        $this->createFeedback = '';
        $this->resetErrorBag([
            'createForm.name',
            'createForm.guard_name',
        ]);

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->createFeedback = '';
        $this->reset('createForm');
        $this->createForm['guard_name'] = 'web';

        $this->resetErrorBag([
            'createForm.name',
            'createForm.guard_name',
        ]);
    }

    public function createPermission(): void
    {
        $this->validate(
            [
                'createForm.name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
                'createForm.guard_name' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'createForm.name' => __('Name'),
                'createForm.guard_name' => __('Guard Name'),
            ],
        );

        $this->permissionServices->createPermission($this->createForm);

        $this->createFeedback = __('Permission created successfully.');
        $this->reset('createForm');
        $this->createForm['guard_name'] = 'web';

        $this->resetErrorBag([
            'createForm.name',
            'createForm.guard_name',
        ]);

        $this->resetPage();
    }

    public function startEditing(int $permissionId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        $permission = Permission::findOrFail($permissionId);

        $this->editingPermissionId = $permissionId;
        $this->editFeedback = '';
        $this->editForm = [
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ];

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
        ]);

        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingPermissionId = null;
        $this->editFeedback = '';
        $this->reset('editForm');
        $this->editForm['guard_name'] = 'web';

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
        ]);
    }

    public function updatePermission(): void
    {
        if (! $this->editingPermissionId) {
            return;
        }

        $permission = Permission::findOrFail($this->editingPermissionId);

        $this->validate(
            [
                'editForm.name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('permissions', 'name')->ignore($permission->id),
                ],
                'editForm.guard_name' => ['required', 'string', 'max:255'],
            ],
            [],
            [
                'editForm.name' => __('Name'),
                'editForm.guard_name' => __('Guard Name'),
            ],
        );

        $this->permissionServices->updatePermission($permission, $this->editForm);

        $this->editFeedback = __('Permission updated successfully.');

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
        ]);
    }

    public function confirmDelete(int $permissionId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $permission = Permission::findOrFail($permissionId);

        $this->deletingPermissionId = $permissionId;
        $this->deleteContextName = $permission->name;
        $this->deleteFeedback = '';
        $this->showDeleteModal = true;
        $this->resetErrorBag(['delete']);
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPermissionId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deletePermission(): void
    {
        if (! $this->deletingPermissionId) {
            return;
        }

        $permission = Permission::findOrFail($this->deletingPermissionId);

        $this->permissionServices->deletePermission($permission);

        $name = $this->deleteContextName ?? $permission->name;
        $this->deleteFeedback = __('Permission ":name" deleted successfully.', ['name' => $name]);

        $this->deletingPermissionId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;

        $this->resetPage();
    }

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        $permissions = Permission::query()
            ->withCount('roles')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return view('livewire.usermanagement.permissions-index', [
            'permissions' => $permissions,
            'perPageOptions' => $this->perPageOptions,
        ])->layout('components.layouts.app', [
            'title' => __('Permission Management'),
        ]);
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
