<?php

namespace App\Livewire\Usermanagement;

use App\Services\Usermanagement\PermissionServices;
use App\Services\Usermanagement\RoleServices;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RolesIndex extends Component
{
    use WithPagination;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'User Management', 'routes' => ''],
        ['title' => 'Roles', 'routes' => '']
    ];
    public array $pageTitle = ['title' => 'Role Management', 'subtitle' => 'Create, update, and remove roles, as well as manage the permissions attached to each role. '];
    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $createForm = [
        'name' => '',
        'guard_name' => 'web',
        'permissions' => [],
    ];

    public array $editForm = [
        'name' => '',
        'guard_name' => 'web',
        'permissions' => [],
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingRoleId = null;

    public ?int $deletingRoleId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected RoleServices $roleServices;

    protected PermissionServices $permissionServices;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(RoleServices $roleServices, PermissionServices $permissionServices): void
    {
        $this->roleServices = $roleServices;
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
            'createForm.permissions',
            'createForm.permissions.*',
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
            'createForm.permissions',
            'createForm.permissions.*',
        ]);
    }

    public function createRole(): void
    {
        $this->validate(
            [
                'createForm.name' => ['required', 'string', 'max:255', 'unique:roles,name'],
                'createForm.guard_name' => ['required', 'string', 'max:255'],
                'createForm.permissions' => ['array'],
                'createForm.permissions.*' => ['string', Rule::exists('permissions', 'name')],
            ],
            [],
            [
                'createForm.name' => __('Name'),
                'createForm.guard_name' => __('Guard Name'),
                'createForm.permissions' => __('Permissions'),
            ],
        );

        $this->roleServices->createRole($this->createForm);

        $this->createFeedback = __('Role created successfully.');
        $this->reset('createForm');
        $this->createForm['guard_name'] = 'web';

        $this->resetErrorBag([
            'createForm.name',
            'createForm.guard_name',
            'createForm.permissions',
            'createForm.permissions.*',
        ]);

        $this->resetPage();
    }

    public function startEditing(int $roleId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        $role = Role::with('permissions')->findOrFail($roleId);

        $this->editingRoleId = $roleId;
        $this->editFeedback = '';
        $this->editForm = [
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->all(),
        ];

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
            'editForm.permissions',
            'editForm.permissions.*',
        ]);

        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingRoleId = null;
        $this->editFeedback = '';
        $this->reset('editForm');
        $this->editForm['guard_name'] = 'web';

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
            'editForm.permissions',
            'editForm.permissions.*',
        ]);
    }

    public function updateRole(): void
    {
        if (! $this->editingRoleId) {
            return;
        }

        $role = Role::findOrFail($this->editingRoleId);

        $this->validate(
            [
                'editForm.name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'name')->ignore($role->id),
                ],
                'editForm.guard_name' => ['required', 'string', 'max:255'],
                'editForm.permissions' => ['array'],
                'editForm.permissions.*' => ['string', Rule::exists('permissions', 'name')],
            ],
            [],
            [
                'editForm.name' => __('Name'),
                'editForm.guard_name' => __('Guard Name'),
                'editForm.permissions' => __('Permissions'),
            ],
        );

        $updatedRole = $this->roleServices->updateRole($role, $this->editForm);

        $this->editFeedback = __('Role updated successfully.');
        $this->editForm['permissions'] = $updatedRole->permissions()->pluck('name')->all();

        $this->resetErrorBag([
            'editForm.name',
            'editForm.guard_name',
            'editForm.permissions',
            'editForm.permissions.*',
        ]);
    }

    public function confirmDelete(int $roleId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $role = Role::findOrFail($roleId);

        $this->deletingRoleId = $roleId;
        $this->deleteContextName = $role->name;
        $this->deleteFeedback = '';
        $this->showDeleteModal = true;
        $this->resetErrorBag(['delete']);
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingRoleId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteRole(): void
    {
        if (! $this->deletingRoleId) {
            return;
        }

        $role = Role::findOrFail($this->deletingRoleId);

        $this->roleServices->deleteRole($role);

        $name = $this->deleteContextName ?? $role->name;

        $this->deleteFeedback = __('Role ":name" deleted successfully.', ['name' => $name]);
        $this->deletingRoleId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;

        $this->resetPage();
    }

    public function getAvailablePermissionsProperty(): Collection
    {
        return $this->permissionServices->allPermissions();
    }

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        $roles = Role::query()
            ->with('permissions')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return view('livewire.usermanagement.roles-index', [
            'roles' => $roles,
            'perPageOptions' => $this->perPageOptions,
            'availablePermissions' => $this->availablePermissions,
        ])->layout('components.layouts.app', [
            'title' => __('Role Management'),
        ]);
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
