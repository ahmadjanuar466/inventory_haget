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
            <x-table-navbar-title title="{{ __('List Category Product') }}"
                subtitle="{{ __('Maintain product category codes, names, and parent category structure.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.parent_id" class="sm:w-52">
                    <option value="">{{ __('All Parent Categories') }}</option>
                    @foreach ($parentFilterOptions as $parentOption)
                        <option value="{{ $parentOption->id }}">{{ $parentOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search category products...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Category') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Name', 'Parent Category', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$categories" row-view="livewire.category.partials.category-row" :columns="4"
                item-key="category" empty-message="{{ __('No category products found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $categories->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-category-modal" class="max-w-2xl" title="{{ __('Add Category Product') }}"
        subtitle="{{ __('Create a product category with an optional parent category.') }}" wire:model="showCreateModal"
        wire-target="createCategory" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving category...') }}">
        <form wire:submit.prevent="createCategory" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.code" :label="__('Code')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <flux:select wire:model.defer="createForm.parent_id" :label="__('Parent Category')">
                <option value="">{{ __('No parent category') }}</option>
                @foreach ($parentOptions as $parentOption)
                    <option value="{{ $parentOption->id }}">{{ $parentOption->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createCategory">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createCategory">
                    <span wire:loading.remove wire:target="createCategory">
                        {{ __('Create Category') }}
                    </span>
                    <span wire:loading wire:target="createCategory" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-category-modal" class="max-w-2xl" title="{{ __('Edit Category Product') }}"
        subtitle="{{ __('Update product category identity and hierarchy.') }}" wire:model="showEditModal"
        wire-target="updateCategory" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating category...') }}">
        <form wire:submit.prevent="updateCategory" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <flux:select wire:model.defer="editForm.parent_id" :label="__('Parent Category')">
                <option value="">{{ __('No parent category') }}</option>
                @foreach ($parentOptions as $parentOption)
                    @if ($parentOption->id !== $editingCategoryId)
                        <option value="{{ $parentOption->id }}">{{ $parentOption->name }}</option>
                    @endif
                @endforeach
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateCategory">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateCategory">
                    <span wire:loading.remove wire:target="updateCategory">
                        {{ __('Update Category') }}
                    </span>
                    <span wire:loading wire:target="updateCategory" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-category-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteCategory"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting category...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Category Product') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the category product ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteCategory">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteCategory" wire:loading.attr="disabled"
                    wire:target="deleteCategory">
                    <span wire:loading.remove wire:target="deleteCategory">
                        {{ __('Delete Category') }}
                    </span>
                    <span wire:loading wire:target="deleteCategory" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
