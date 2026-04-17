<x-page-body>
    <x-breadcumbs :datas="$breadcumbs"></x-breadcumbs>

    @if ($deleteFeedback !== '')
        <x-delete-message>
            {{ $deleteFeedback }}
        </x-delete-message>
    @endif

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <x-table-navbar>
            <x-table-navbar-title title="{{ __('Branches') }}"
                subtitle="{{ __('Maintain branch identities, contact details, and operational status.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.branch_type_id" class="sm:w-48">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($branchTypes as $branchType)
                        <option value="{{ $branchType->id }}">{{ $branchType->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.status" class="sm:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search branches...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Branch') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Name', 'Type', 'Phone', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$branches" row-view="livewire.branch.partials.branch-row" :columns="6"
                item-key="branch" empty-message="{{ __('No branches found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $branches->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-branch-modal" class="max-w-2xl" title="{{ __('Add Branch') }}"
        subtitle="{{ __('Create a branch with its code, type, location, and status.') }}" wire:model="showCreateModal"
        wire-target="createBranch" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving branch...') }}">
        <form wire:submit.prevent="createBranch" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.code" :label="__('Code')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="createForm.branch_type_id" :label="__('Branch Type')" required>
                    <option value="">{{ __('Select branch type') }}</option>
                    @foreach ($branchTypes as $branchType)
                        <option value="{{ $branchType->id }}">{{ $branchType->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.defer="createForm.status" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <flux:input wire:model.defer="createForm.phone" :label="__('Phone')" type="text" />

            <flux:textarea wire:model.defer="createForm.address" :label="__('Address')" rows="3" />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createBranch">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createBranch">
                    <span wire:loading.remove wire:target="createBranch">
                        {{ __('Create Branch') }}
                    </span>
                    <span wire:loading wire:target="createBranch" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-branch-modal" class="max-w-2xl" title="{{ __('Edit Branch') }}"
        subtitle="{{ __('Update branch details and operational status.') }}" wire:model="showEditModal"
        wire-target="updateBranch" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating branch...') }}">
        <form wire:submit.prevent="updateBranch" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="editForm.branch_type_id" :label="__('Branch Type')" required>
                    <option value="">{{ __('Select branch type') }}</option>
                    @foreach ($branchTypes as $branchType)
                        <option value="{{ $branchType->id }}">{{ $branchType->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.defer="editForm.status" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <flux:input wire:model.defer="editForm.phone" :label="__('Phone')" type="text" />

            <flux:textarea wire:model.defer="editForm.address" :label="__('Address')" rows="3" />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateBranch">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateBranch">
                    <span wire:loading.remove wire:target="updateBranch">
                        {{ __('Update Branch') }}
                    </span>
                    <span wire:loading wire:target="updateBranch" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-branch-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteBranch"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0b1424]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#ffc600]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting branch...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Branch') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#8fb3d9]">
                {{ __('Are you sure you want to delete the branch ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteBranch">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteBranch" wire:loading.attr="disabled"
                    wire:target="deleteBranch">
                    <span wire:loading.remove wire:target="deleteBranch">
                        {{ __('Delete Branch') }}
                    </span>
                    <span wire:loading wire:target="deleteBranch" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
