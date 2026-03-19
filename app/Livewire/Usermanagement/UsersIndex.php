<?php

namespace App\Livewire\Usermanagement;

use App\Models\User;
use App\Services\Usermanagement\PermissionServices;
use App\Services\Usermanagement\RoleServices;
use App\Services\Usermanagement\UserProfileServices;
use App\Services\Usermanagement\UserServices;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'User Management', 'routes' => ''],
        ['title' => 'Profile', 'routes' => '']
    ];

    public array $pageTitle = [
        'title' => 'Manage Users',
        'subtitle' => 'Search for existing accounts, add new users, update their details, reset passwords, or remove access as needed.'
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $createForm = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
        'roles' => [],
    ];

    public array $profileForm = [
        'nama_lengkap' => '',
        'email' => '',
        'alamat' => '',
        'no_telp' => '',
        'avatars' => '',
        'birth_date' => '',
    ];

    public $profileAvatarUpload = null;

    public int $createStep = 1;

    public bool $profileStepCompleted = false;

    public array $editProfileForm = [
        'nama_lengkap' => '',
        'email' => '',
        'alamat' => '',
        'no_telp' => '',
        'avatars' => '',
        'birth_date' => '',
    ];

    public $editAvatarUpload = null;

    public array $resetForm = [
        'password' => '',
        'password_confirmation' => '',
    ];

    public array $accessForm = [
        'roles' => [],
        'permissions' => [],
    ];

    public ?int $editingUserId = null;

    public ?int $resetUserId = null;

    public ?int $accessUserId = null;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showResetModal = false;

    public bool $showDeleteModal = false;

    public bool $showAccessModal = false;

    public ?int $deletingUserId = null;

    public ?string $createFeedback = "";

    public ?string $editFeedback = "";

    public ?string $resetFeedback = "";

    public ?string $deleteFeedback = "";

    public ?string $deleteContextName = null;

    public ?string $accessFeedback = "";

    protected UserServices $userServices;

    protected RoleServices $roleServices;

    protected PermissionServices $permissionServices;

    protected UserProfileServices $userProfileServices;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(
        UserServices $userServices,
        RoleServices $roleServices,
        PermissionServices $permissionServices,
        UserProfileServices $userProfileServices,
    ): void {
        $this->userServices = $userServices;
        $this->roleServices = $roleServices;
        $this->permissionServices = $permissionServices;
        $this->userProfileServices = $userProfileServices;
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

        if ($this->showResetModal) {
            $this->cancelPasswordReset();
        }

        // if ($this->showCreateModal) {
        //     $this->closeCreateModal();
        // }

        if ($this->showAccessModal) {
            $this->cancelAccessEditing();
        }

        $this->reset('createForm', 'profileForm');
        $this->createForm['roles'] = [];
        $this->createStep = 1;
        $this->profileStepCompleted = false;
        $this->reset('profileAvatarUpload');
        $this->resetErrorBag([
            'createForm.name',
            'createForm.email',
            'createForm.password',
            'createForm.password_confirmation',
            'createForm.roles',
            'createForm.roles.*',
            'profileForm.nama_lengkap',
            'profileForm.email',
            'profileForm.alamat',
            'profileForm.no_telp',
            'profileForm.avatars',
            'profileForm.birth_date',
        ]);

        $this->createFeedback = "";

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->createFeedback = "";
        $this->reset('createForm', 'profileForm');
        $this->createForm['roles'] = [];
        $this->createStep = 1;
        $this->profileStepCompleted = false;
        $this->reset('profileAvatarUpload');
        $this->resetErrorBag([
            'createForm.name',
            'createForm.email',
            'createForm.password',
            'createForm.password_confirmation',
            'createForm.roles',
            'createForm.roles.*',
            'profileForm.nama_lengkap',
            'profileForm.email',
            'profileForm.alamat',
            'profileForm.no_telp',
            'profileForm.avatars',
            'profileForm.birth_date',
        ]);
    }

    public function completeProfileStep(): void
    {
        $this->validateProfileForm();

        $this->profileStepCompleted = true;

        if ($this->createForm['name'] === '' && $this->profileForm['nama_lengkap'] !== '') {
            $this->createForm['name'] = $this->profileForm['nama_lengkap'];
        }

        if ($this->createForm['email'] === '' && $this->profileForm['email'] !== '') {
            $this->createForm['email'] = $this->profileForm['email'];
        }

        $this->createStep = 2;
    }

    public function backToProfileStep(): void
    {
        $this->profileStepCompleted = false;
        $this->createStep = 1;
    }

    public function createUser(): void
    {
        $this->validateProfileForm();
        $this->validateAccountForm();

        if ($path = $this->storeAvatarUpload('profileAvatarUpload')) {
            $this->profileForm['avatars'] = $path;
        }

        DB::transaction(function () {
            $profile = $this->userProfileServices->createProfile($this->profilePayload());
            $user = $this->userServices->createNewUser($this->accountPayload());
            $this->userProfileServices->attachUser($profile, $user);
        });

        $this->createFeedback = __('Profile and user account created successfully.');
        $this->reset('createForm', 'profileForm');
        $this->createForm['roles'] = [];
        $this->createStep = 1;
        $this->profileStepCompleted = false;
        $this->resetErrorBag([
            'createForm.name',
            'createForm.email',
            'createForm.password',
            'createForm.password_confirmation',
            'createForm.roles',
            'createForm.roles.*',
            'profileForm.nama_lengkap',
            'profileForm.email',
            'profileForm.alamat',
            'profileForm.no_telp',
            'profileForm.avatars',
            'profileForm.birth_date',
        ]);
        $this->reset('profileAvatarUpload');
        $this->resetPage();
    }

    protected function profileRules(string $base = 'profileForm'): array
    {
        return [
            "{$base}.nama_lengkap" => ['required', 'string', 'max:150'],
            "{$base}.email" => ['nullable', 'email', 'max:255'],
            "{$base}.alamat" => ['nullable', 'string', 'max:150'],
            "{$base}.no_telp" => ['nullable', 'string', 'max:15'],
            "{$base}.avatars" => ['nullable', 'string', 'max:50'],
            "{$base}.birth_date" => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    protected function profileAttributes(string $base = 'profileForm'): array
    {
        return [
            "{$base}.nama_lengkap" => __('Full name'),
            "{$base}.email" => __('Profile email'),
            "{$base}.alamat" => __('Address'),
            "{$base}.no_telp" => __('Phone number'),
            "{$base}.avatars" => __('Avatar photo'),
            "{$base}.birth_date" => __('Birth date'),
        ];
    }

    protected function validateProfileForm(): void
    {
        $this->validate(
            array_merge($this->profileRules(), [
                'profileAvatarUpload' => ['nullable', 'image', 'max:2048'],
            ]),
            [],
            array_merge($this->profileAttributes(), [
                'profileAvatarUpload' => __('Avatar photo'),
            ]),
        );
    }

    protected function accountRules(): array
    {
        return [
            'createForm.name' => ['required', 'string', 'max:255'],
            'createForm.email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'createForm.password' => ['required', 'string', 'min:8', 'confirmed'],
            'createForm.roles' => ['array'],
            'createForm.roles.*' => ['string', Rule::exists('roles', 'name')],
        ];
    }

    protected function accountAttributes(): array
    {
        return [
            'createForm.name' => __('Name'),
            'createForm.email' => __('Email'),
            'createForm.password' => __('Password'),
            'createForm.roles' => __('Roles'),
        ];
    }

    protected function validateAccountForm(): void
    {
        $this->validate(
            $this->accountRules(),
            [],
            $this->accountAttributes(),
        );
    }

    protected function accessRules(): array
    {
        return [
            'accessForm.roles' => ['array'],
            'accessForm.roles.*' => ['string', Rule::exists('roles', 'name')],
            'accessForm.permissions' => ['array'],
            'accessForm.permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ];
    }

    protected function accessAttributes(): array
    {
        return [
            'accessForm.roles' => __('Roles'),
            'accessForm.permissions' => __('Permissions'),
        ];
    }

    protected function profilePayload(): array
    {
        return $this->normalizeProfilePayload($this->profileForm);
    }

    protected function accountPayload(): array
    {
        return [
            'name' => $this->createForm['name'],
            'email' => $this->createForm['email'],
            'password' => $this->createForm['password'],
            'roles' => $this->createForm['roles'],
        ];
    }

    protected function normalizeProfilePayload(array $source): array
    {
        $payload = Arr::only($source, [
            'nama_lengkap',
            'email',
            'alamat',
            'no_telp',
            'avatars',
            'birth_date',
        ]);

        foreach (['email', 'alamat', 'no_telp', 'avatars', 'birth_date'] as $field) {
            if (($payload[$field] ?? null) === '') {
                $payload[$field] = null;
            }
        }

        return $payload;
    }

    protected function storeAvatarUpload(string $property, ?string $existingPath = null): ?string
    {
        $file = $this->{$property} ?? null;

        if (! $file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension();
        $filename = (string) Str::uuid();
        if (! empty($extension)) {
            $filename .= '.' . strtolower($extension);
        }

        $path = $file->storeAs('avatars', $filename, 'public');

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $this->reset($property);

        return $path;
    }

    public function startEditing(int $userId): void
    {
        // if ($this->showEditModal) {
        //     $this->cancelEditing();
        // }

        if ($this->showResetModal) {
            $this->cancelPasswordReset();
        }

        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showAccessModal) {
            $this->cancelAccessEditing();
        }

        $this->editFeedback = "";
        $this->reset('editAvatarUpload');

        $user = User::with(['profile'])->findOrFail($userId);

        $profile = $user->profile;

        $this->editingUserId = $userId;
        $this->editProfileForm = [
            'nama_lengkap' => $profile->nama_lengkap ?? $user->name,
            'email' => $profile->email ?? $user->email,
            'alamat' => $profile->alamat ?? '',
            'no_telp' => $profile->no_telp ?? '',
            'avatars' => $profile->avatars ?? '',
            'birth_date' => $profile?->birth_date?->format('Y-m-d') ?? '',
        ];

        $this->resetErrorBag([
            'editProfileForm.nama_lengkap',
            'editProfileForm.email',
            'editProfileForm.alamat',
            'editProfileForm.no_telp',
            'editProfileForm.avatars',
            'editProfileForm.birth_date',
        ]);

        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->editingUserId = null;
        $this->showEditModal = false;
        $this->editFeedback = "";
        $this->reset('editProfileForm');
        $this->reset('editAvatarUpload');
        $this->resetErrorBag([
            'editProfileForm.nama_lengkap',
            'editProfileForm.email',
            'editProfileForm.alamat',
            'editProfileForm.no_telp',
            'editProfileForm.avatars',
            'editProfileForm.birth_date',
        ]);
    }

    public function updateUser(): void
    {
        if (! $this->editingUserId) {
            return;
        }

        $this->validate(
            array_merge($this->profileRules('editProfileForm'), [
                'editAvatarUpload' => ['nullable', 'image', 'max:2048'],
            ]),
            [],
            array_merge($this->profileAttributes('editProfileForm'), [
                'editAvatarUpload' => __('Avatar photo'),
            ]),
        );

        $user = User::with('profile')->findOrFail($this->editingUserId);

        if ($path = $this->storeAvatarUpload('editAvatarUpload', $user->profile->avatars ?? null)) {
            $this->editProfileForm['avatars'] = $path;
        }

        $payload = $this->normalizeProfilePayload($this->editProfileForm);

        DB::transaction(function () use ($payload, $user) {
            $user->refresh();
            $user->load('profile');

            if ($user->profile) {
                $this->userProfileServices->updateProfile($user->profile, $payload);
            } else {
                $profile = $this->userProfileServices->createProfile($payload);
                $this->userProfileServices->attachUser($profile, $user);
            }

            if (! empty($payload['nama_lengkap']) && $user->name !== $payload['nama_lengkap']) {
                $user->name = $payload['nama_lengkap'];
                $user->save();
            }
        });

        $this->editFeedback = __('Profile updated successfully.');

        $this->resetErrorBag([
            'editProfileForm.nama_lengkap',
            'editProfileForm.email',
            'editProfileForm.alamat',
            'editProfileForm.no_telp',
            'editProfileForm.avatars',
            'editProfileForm.birth_date',
        ]);

        $this->resetPage();
    }

    public function startAccessEditing(int $userId): void
    {


        $this->accessFeedback = "";

        $user = User::with(['roles', 'permissions'])->findOrFail($userId);

        $this->accessUserId = $userId;
        $this->accessForm = [
            'roles' => $user->roles->pluck('name')->all(),
            'permissions' => $user->permissions->pluck('name')->all(),
        ];

        $this->resetErrorBag([
            'accessForm.roles',
            'accessForm.roles.*',
            'accessForm.permissions',
            'accessForm.permissions.*',
        ]);

        $this->showAccessModal = true;
    }

    public function cancelAccessEditing(): void
    {
        $this->accessUserId = null;
        $this->showAccessModal = false;
        $this->accessFeedback = "";
        $this->reset('accessForm');
        $this->resetErrorBag([
            'accessForm.roles',
            'accessForm.roles.*',
            'accessForm.permissions',
            'accessForm.permissions.*',
        ]);
    }

    public function updateUserAccess(): void
    {
        if (! $this->accessUserId) {
            return;
        }

        $this->validate(
            $this->accessRules(),
            [],
            $this->accessAttributes(),
        );

        $user = User::findOrFail($this->accessUserId);

        $this->userServices->updateUsers($user, [
            'roles' => $this->accessForm['roles'],
            'permissions' => $this->accessForm['permissions'],
        ]);

        $this->accessFeedback = __('Access updated successfully.');

        $this->resetErrorBag([
            'accessForm.roles',
            'accessForm.roles.*',
            'accessForm.permissions',
            'accessForm.permissions.*',
        ]);

        $this->resetPage();
    }

    public function startPasswordReset(int $userId): void
    {


        $this->resetUserId = $userId;
        $this->reset('resetForm');
        $this->resetErrorBag([
            'resetForm.password',
            'resetForm.password_confirmation',
        ]);

        $this->resetFeedback = "";

        $this->showResetModal = true;
    }

    public function cancelPasswordReset(): void
    {
        $this->resetUserId = null;
        $this->reset('resetForm');
        $this->showResetModal = false;
        $this->resetFeedback = "";
        $this->resetErrorBag([
            'resetForm.password',
            'resetForm.password_confirmation',
        ]);
    }

    public function resetUserPassword(): void
    {
        if (! $this->resetUserId) {
            return;
        }

        $user = User::findOrFail($this->resetUserId);

        $this->validate(
            [
                'resetForm.password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [],
            [
                'resetForm.password' => __('Password'),
            ],
        );

        $this->userServices->resetPassword($user, $this->resetForm['password']);

        $this->resetFeedback = __('Password reset successfully.');
        $this->reset('resetForm');
        $this->resetErrorBag([
            'resetForm.password',
            'resetForm.password_confirmation',
        ]);
    }

    public function confirmDelete(int $userId): void
    {
        if ($userId === auth()->id()) {
            $this->addError('delete', __('You cannot delete the account you are currently using.'));

            return;
        }

        $user = User::findOrFail($userId);

        // if ($this->showCreateModal) {
        //     $this->closeCreateModal();
        // }

        // if ($this->showEditModal) {
        //     $this->cancelEditing();
        // }

        // if ($this->showResetModal) {
        //     $this->cancelPasswordReset();
        // }

        // if ($this->showAccessModal) {
        //     $this->cancelAccessEditing();
        // }

        $this->deletingUserId = $userId;
        $this->deleteContextName = $user->name;
        $this->deleteFeedback = "";
        $this->showDeleteModal = true;
        $this->resetErrorBag(['delete']);
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = "";
        $this->resetErrorBag(['delete']);
    }

    public function deleteUser(): void
    {
        if (! $this->deletingUserId) {
            return;
        }

        if ($this->deletingUserId === auth()->id()) {
            $this->addError('delete', __('You cannot delete the account you are currently using.'));
            $this->cancelDelete();

            return;
        }

        if ($this->editingUserId === $this->deletingUserId) {
            $this->cancelEditing();
        }

        if ($this->resetUserId === $this->deletingUserId) {
            $this->cancelPasswordReset();
        }

        if ($this->accessUserId === $this->deletingUserId) {
            $this->cancelAccessEditing();
        }

        $user = User::findOrFail($this->deletingUserId);

        $this->userServices->deleteUser($user);

        $name = $this->deleteContextName ?? $user->name;

        $this->deleteFeedback = __('User ":name" deleted successfully.', ['name' => $name]);
        $this->deletingUserId = null;
        $this->deleteContextName = null;
        $this->resetPage();
    }

    public function getAvailableRolesProperty(): Collection
    {
        return $this->roleServices->allRoles();
    }

    public function getAvailablePermissionsProperty(): Collection
    {
        return $this->permissionServices->allPermissions();
    }

    public function render()
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        $users = User::query()
            ->with(['roles', 'permissions'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return view('livewire.usermanagement.users-index', [
            'users' => $users,
            'perPageOptions' => $this->perPageOptions,
            'availableRoles' => $this->availableRoles,
            'availablePermissions' => $this->availablePermissions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('User Management'),
        ]);
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
