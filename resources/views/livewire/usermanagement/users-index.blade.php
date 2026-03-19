<x-page-body>

    <x-breadcumbs :datas="$breadcumb"></x-breadcumbs>

    @error('delete')
        <x-delete-message>
            {{ $message }}
        </x-delete-message>
    @enderror

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <x-table-navbar>
            <!--Gunakan Bila Perlu-->
            <x-table-navbar-title title="Users"
                subtitle="Keep track of everyone registered in the system."></x-table-navbar-title>
            <!--Komponen Pencarian-->
            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions"></x-table-navbar-per-page-option>
                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search name or email...')"
                    icon="magnifying-glass" class="sm:w-64" />
                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add User') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>
        <x-table-custom>
            @php
                $dtHead = ['Name', 'Email', 'Roles', 'Permissions', 'Created', 'Actions'];
            @endphp

            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$users" row-view="livewire.usermanagement.partials.user-row" :columns="6"
                item-key="user" :row-data="['currentUserId' => auth()->id()]" empty-message="{{ __('No users found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $users->links() }}
        </div>
    </x-table-section>


    <x-modal name="create-user-modal" class="max-w-3xl" title="Add New User"
        subtitle="Create a new account for the platform." wire:model="showCreateModal"
        wire-target="createUser,completeProfileStep" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving user...') }}">
        <form wire:submit.prevent="createUser" class="space-y-6">
            @php
                $profileActive = $createStep === 1;
                $profileDone = $profileStepCompleted && $createStep === 2;
                $accountActive = $createStep === 2;
            @endphp

            <div
                class="flex flex-col gap-4 rounded-xl border border-[#0f2234]/70 bg-[#0b1624]/40 px-4 py-3 text-sm text-[#8fb3d9] sm:flex-row sm:items-center">
                <div class="flex flex-1 items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full border text-base font-semibold transition
                        @if ($profileDone) border-green-400 bg-green-500/10 text-green-300
                        @elseif ($profileActive) border-[#ffc600] bg-[#ffc600]/10 text-[#ffc600]
                        @else border-[#1b2d42] text-[#8fb3d9] @endif">
                        @if ($profileDone)
                            <flux:icon.check class="h-5 w-5" />
                        @else
                            <span>1</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-[#e6f1ff]">{{ __('Profile Data') }}</p>
                        <p class="text-xs">
                            {{ __('Capture the user profile first.') }}
                        </p>
                    </div>
                </div>
                <div class="hidden flex-1 items-center sm:flex">
                    <div class="h-px w-full bg-[#1b2d42]/80"></div>
                </div>
                <div class="flex flex-1 items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full border text-base font-semibold transition
                        @if ($accountActive) border-[#ffc600] bg-[#ffc600]/10 text-[#ffc600]
                        @else border-[#1b2d42] text-[#8fb3d9] @endif">
                        <span>2</span>
                    </div>
                    <div>
                        <p class="font-semibold text-[#e6f1ff]">{{ __('Account Access') }}</p>
                        <p class="text-xs">
                            {{ __('Create the login credential after the profile is saved.') }}
                        </p>
                    </div>
                </div>
            </div>

            @if ($createStep === 1)
                <div class="space-y-4" wire:key="profile-step-form">
                    <flux:input wire:model.defer="profileForm.nama_lengkap" :label="__('Full Name')" type="text"
                        required autofocus autocomplete="name" />

                    <flux:input wire:model.defer="profileForm.email" :label="__('Profile Email')" type="email"
                        autocomplete="email" />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model.defer="profileForm.no_telp" :label="__('Phone Number')" type="tel"
                            autocomplete="tel" />

                        <flux:input wire:model.defer="profileForm.birth_date" :label="__('Birth Date')" type="date"
                            max="{{ now()->toDateString() }}" autocomplete="bday" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                            {{ __('Address') }}
                        </label>
                        <textarea wire:model.defer="profileForm.alamat" rows="3"
                            class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0"
                            placeholder="{{ __('Street, city, province') }}"></textarea>
                        @error('profileForm.alamat')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#e6f1ff]">
                            {{ __('Avatar Photo') }}
                        </label>
                        <input type="file" wire:model="profileAvatarUpload" accept="image/*"
                            class="w-full cursor-pointer rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#193549] file:px-3 file:py-2 file:text-sm file:text-[#e6f1ff] file:font-medium hover:border-[#ffc600] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                        <p class="text-xs text-[#8fb3d9]">
                            {{ __('PNG or JPG up to 2MB.') }}
                        </p>
                        @error('profileAvatarUpload')
                            <p class="text-xs text-red-300">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="profileAvatarUpload" class="text-xs font-medium text-[#ffc600]">
                            {{ __('Uploading avatar...') }}
                        </div>
                        @if ($profileAvatarUpload || $profileForm['avatars'])
                            <div class="mt-2 flex items-center gap-3">
                                <div class="text-xs text-[#8fb3d9]">{{ __('Preview') }}</div>
                                <img src="{{ $profileAvatarUpload?->temporaryUrl() ?? asset('storage/' . ltrim($profileForm['avatars'], '/')) }}"
                                    alt="{{ __('Avatar preview') }}" class="h-14 w-14 rounded-full object-cover">
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-end">
                        <flux:button type="button" variant="ghost" wire:click="closeCreateModal">
                            {{ __('Cancel') }}
                        </flux:button>

                        <flux:button type="button" variant="primary" wire:click="completeProfileStep"
                            wire:loading.attr="disabled" wire:target="completeProfileStep">
                            <span wire:loading.remove wire:target="completeProfileStep">
                                {{ __('Continue to Account') }}
                            </span>
                            <span wire:loading wire:target="completeProfileStep" class="inline-flex items-center gap-2">
                                <flux:icon.loading class="h-4 w-4" />
                                {{ __('Saving profile...') }}
                            </span>
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="space-y-4" wire:key="account-step-form">
                    <div
                        class="flex flex-col gap-3 rounded-lg border border-[#0f2234]/60 bg-[#0b1624]/60 px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-[#8fb3d9]">{{ __('Profile Ready') }}</p>
                            <p class="text-base font-semibold text-[#e6f1ff]">
                                {{ $profileForm['nama_lengkap'] !== '' ? $profileForm['nama_lengkap'] : __('Unnamed profile') }}
                            </p>
                            <p class="text-xs text-[#8fb3d9]">
                                {{ $profileForm['email'] !== '' ? $profileForm['email'] : __('No email recorded') }}
                            </p>
                        </div>
                        <flux:button type="button" size="sm" variant="ghost"
                            class="text-[#ffc600] hover:text-[#ffd23f]" wire:click="backToProfileStep">
                            {{ __('Edit Profile') }}
                        </flux:button>
                    </div>

                    <flux:input wire:model.defer="createForm.name" :label="__('Display Name')" type="text" required
                        autocomplete="name" />

                    <flux:input wire:model.defer="createForm.email" :label="__('Login Email')" type="email" required
                        autocomplete="email" />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model.defer="createForm.password" :label="__('Password')" type="password"
                            required autocomplete="new-password" />

                        <flux:input wire:model.defer="createForm.password_confirmation" :label="__('Confirm Password')"
                            type="password" required autocomplete="new-password" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                            {{ __('Roles') }}
                        </label>

                        <select wire:model.defer="createForm.roles" multiple size="5"
                            class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                            @foreach ($availableRoles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>

                        @if ($availableRoles->isEmpty())
                            <p class="mt-2 text-xs text-[#8fb3d9]">
                                {{ __('No roles available yet. Create one first in the Roles section.') }}
                            </p>
                        @else
                            <p class="mt-2 text-xs text-[#8fb3d9]">
                                {{ __('Hold Ctrl (Windows) or Command (Mac) to select multiple roles.') }}
                            </p>
                        @endif

                        @error('createForm.roles')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                        @error('createForm.roles.*')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <flux:text variant="subtle" class="text-xs text-[#8fb3d9]">
                        {{ __('Permissions are inherited from the roles you assign here.') }}
                    </flux:text>

                    <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                        <flux:button type="button" variant="ghost" wire:click="backToProfileStep">
                            {{ __('Back to Profile') }}
                        </flux:button>

                        <div class="flex items-center gap-3">
                            <flux:button type="button" variant="ghost" wire:click="closeCreateModal"
                                wire:loading.attr="disabled" wire:target="createUser">
                                {{ __('Cancel') }}
                            </flux:button>

                            <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                                wire:target="createUser">
                                <span wire:loading.remove wire:target="createUser">
                                    {{ __('Create User') }}
                                </span>
                                <span wire:loading wire:target="createUser" class="inline-flex items-center gap-2">
                                    <flux:icon.loading class="h-4 w-4" />
                                    {{ __('Saving...') }}
                                </span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </x-modal>
    <x-modal name="edit-user-modal" class="max-w-xl" title="Edit Profile"
        subtitle="Update the user's profile information." wire:model="showEditModal" wire-target="updateUser"
        :createFeedback="$editFeedback" close-action="cancelEditing" loading-message="{{ __('Saving profile...') }}">
        <form wire:submit.prevent="updateUser" class="space-y-4">
            <div
                class="rounded-lg border border-[#ffc600]/40 bg-[#ffc600]/5 px-4 py-3 text-xs text-[#f7e79c] shadow-inner shadow-[#0b1424]/40">
                {{ __('Login credentials cannot be edited here. Use the Reset Password action to change passwords.') }}
            </div>
            <flux:text variant="subtle" class="text-xs uppercase tracking-wide text-[#8fb3d9]">
                {{ __('User ID: :id', ['id' => $editingUserId]) }}
            </flux:text>

            <flux:input wire:model.defer="editProfileForm.nama_lengkap" :label="__('Full Name')" type="text"
                required autocomplete="name" />

            <flux:input wire:model.defer="editProfileForm.email" :label="__('Profile Email')" type="email"
                autocomplete="email" />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model.defer="editProfileForm.no_telp" :label="__('Phone Number')" type="tel"
                    autocomplete="tel" />

                <flux:input wire:model.defer="editProfileForm.birth_date" :label="__('Birth Date')" type="date"
                    max="{{ now()->toDateString() }}" autocomplete="bday" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Address') }}
                </label>
                <textarea wire:model.defer="editProfileForm.alamat" rows="3"
                    class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0"
                    placeholder="{{ __('Street, city, province') }}"></textarea>
                @error('editProfileForm.alamat')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Avatar Photo') }}
                </label>
                <input type="file" wire:model="editAvatarUpload" accept="image/*"
                    class="w-full cursor-pointer rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#193549] file:px-3 file:py-2 file:text-sm file:text-[#e6f1ff] file:font-medium hover:border-[#ffc600] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                <p class="text-xs text-[#8fb3d9]">
                    {{ __('PNG or JPG up to 2MB.') }}
                </p>
                @error('editAvatarUpload')
                    <p class="text-xs text-red-300">{{ $message }}</p>
                @enderror
                <div wire:loading wire:target="editAvatarUpload" class="text-xs font-medium text-[#ffc600]">
                    {{ __('Uploading avatar...') }}
                </div>
                @if ($editAvatarUpload || $editProfileForm['avatars'])
                    <div class="mt-2 flex items-center gap-3">
                        <div class="text-xs text-[#8fb3d9]">{{ __('Preview') }}</div>
                        <img src="{{ $editAvatarUpload?->temporaryUrl() ?? asset('storage/' . ltrim($editProfileForm['avatars'], '/')) }}"
                            alt="{{ __('Avatar preview') }}" class="h-14 w-14 rounded-full object-cover">
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateUser">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateUser">
                    <span wire:loading.remove wire:target="updateUser">
                        {{ __('Update Profile') }}
                    </span>
                    <span wire:loading wire:target="updateUser" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>
    <x-modal name="access-management-modal" class="max-w-3xl" title="Roles & Permissions"
        subtitle="Adjust the roles and direct permissions assigned to this user." wire:model="showAccessModal"
        wire-target="updateUserAccess" :createFeedback="$accessFeedback" close-action="cancelAccessEditing"
        loading-message="{{ __('Updating access...') }}">
        <flux:text variant="subtle" class="text-xs uppercase tracking-wide text-[#8fb3d9]">
            {{ __('User ID: :id', ['id' => $accessUserId]) }}
        </flux:text>

        <form wire:submit.prevent="updateUserAccess" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Roles') }}
                </label>

                <select wire:model.defer="accessForm.roles" multiple size="6"
                    class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                    @foreach ($availableRoles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                @if ($availableRoles->isEmpty())
                    <p class="mt-2 text-xs text-[#8fb3d9]">
                        {{ __('No roles available yet. Create one first in the Roles section.') }}
                    </p>
                @else
                    <p class="mt-2 text-xs text-[#8fb3d9]">
                        {{ __('Hold Ctrl (Windows) or Command (Mac) to select multiple roles.') }}
                    </p>
                @endif

                @error('accessForm.roles')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
                @error('accessForm.roles.*')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Direct Permissions') }}
                </label>

                <select wire:model.defer="accessForm.permissions" multiple size="6"
                    class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                    @foreach ($availablePermissions as $permission)
                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                    @endforeach
                </select>

                @if ($availablePermissions->isEmpty())
                    <p class="mt-2 text-xs text-[#8fb3d9]">
                        {{ __('No permissions defined yet. Add permissions from the Permissions page.') }}
                    </p>
                @else
                    <p class="mt-2 text-xs text-[#8fb3d9]">
                        {{ __('Grant specific capabilities even if the assigned roles do not include them.') }}
                    </p>
                @endif

                @error('accessForm.permissions')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
                @error('accessForm.permissions.*')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <flux:text variant="subtle" class="text-xs text-[#8fb3d9]">
                {{ __('Permissions granted through roles continue to apply automatically.') }}
            </flux:text>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelAccessEditing"
                    wire:loading.attr="disabled" wire:target="updateUserAccess">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="updateUserAccess">
                    <span wire:loading.remove wire:target="updateUserAccess">
                        {{ __('Update Access') }}
                    </span>
                    <span wire:loading wire:target="updateUserAccess" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>
    <x-modal name="reset-password-modal" class="max-w-lg" title="Reset Password"
        subtitle="Set a new password for this user." wire:model="showResetModal" wire-target="resetUserPassword"
        :createFeedback="$resetFeedback" close-action="cancelPasswordReset" loading-message="{{ __('Resetting password...') }}">
        <flux:text variant="subtle" class="text-xs uppercase tracking-wide text-[#8fb3d9]">
            {{ __('User ID: :id', ['id' => $resetUserId]) }}
        </flux:text>

        <form wire:submit.prevent="resetUserPassword" class="space-y-4">
            <flux:input wire:model.defer="resetForm.password" :label="__('New Password')" type="password" required
                autocomplete="new-password" />

            <flux:input wire:model.defer="resetForm.password_confirmation" :label="__('Confirm New Password')"
                type="password" required autocomplete="new-password" />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelPasswordReset"
                    wire:loading.attr="disabled" wire:target="resetUserPassword">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="resetUserPassword">
                    <span wire:loading.remove wire:target="resetUserPassword">
                        {{ __('Reset Password') }}
                    </span>
                    <span wire:loading wire:target="resetUserPassword" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Resetting...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>
    <flux:modal name="delete-user-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteUser"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0b1424]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#ffc600]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting user...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            @if ($deletingUserId && $deleteContextName)
                <div
                    class="rounded-lg border border-[#f8717155] bg-[#f8717133] px-4 py-3 text-sm text-[#fee2e2] shadow-inner shadow-[#7f1d1d]/40">
                    {{ __('Are you sure you want to remove ":name" from the platform?', ['name' => $deleteContextName]) }}
                </div>
            @endif

            @if ($deleteFeedback)
                <div
                    class="rounded-lg border border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-100 shadow-inner shadow-green-900/30">
                    {{ $deleteFeedback }}
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteUser">
                    {{ $deletingUserId ? __('Cancel') : __('Close') }}
                </flux:button>

                @if ($deletingUserId)
                    <flux:button type="button" variant="danger" wire:click="deleteUser"
                        wire:loading.attr="disabled" wire:target="deleteUser">
                        <span wire:loading.remove wire:target="deleteUser">
                            {{ __('Delete User') }}
                        </span>
                        <span wire:loading wire:target="deleteUser" class="inline-flex items-center gap-2">
                            <flux:icon.loading class="h-4 w-4" />
                            {{ __('Deleting...') }}
                        </span>
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
</x-page-body>
