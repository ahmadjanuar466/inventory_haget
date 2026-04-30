<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-[#1c3432] text-[#f4f1ec]">
    <flux:sidebar sticky stashable
        class="border-e border-[#142a28]/80 bg-[#243f3c]/95 text-[#f4f1ec] shadow-lg shadow-[#0d1a18]/40 backdrop-blur">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 text-[#f4f1ec] rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline" class="text-[#f4f1ec]">
            <flux:navlist.group :heading="__('Platform')">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Master')">
                <flux:navlist.group :heading="__('Branches')" expandable
                    :expanded="request()->routeIs('master.branches') || request()->routeIs('master.warehouses')">
                    <flux:navlist.item icon="building-storefront" :href="route('master.branches')"
                        :current="request()->routeIs('master.branches')" wire:navigate>
                        {{ __('List Branches') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="archive-box" :href="route('master.warehouses')"
                        :current="request()->routeIs('master.warehouses')" wire:navigate>
                        {{ __('Warehouses') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Product')" expandable
                    :expanded="request()->routeIs('master.products') || request()->routeIs('master.product-prices') || request()->routeIs('master.product-categories') || request()->routeIs('master.product-units')">
                    <flux:navlist.item icon="cube" :href="route('master.products')"
                        :current="request()->routeIs('master.products')" wire:navigate>
                        {{ __('List Product') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="banknotes" :href="route('master.product-prices')"
                        :current="request()->routeIs('master.product-prices')" wire:navigate>
                        {{ __('Product Price') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="tag" :href="route('master.product-categories')"
                        :current="request()->routeIs('master.product-categories')" wire:navigate>
                        {{ __('List Category Product') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="scale" :href="route('master.product-units')"
                        :current="request()->routeIs('master.product-units')" wire:navigate>
                        {{ __('Units') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.item icon="truck" :href="route('master.suppliers')"
                    :current="request()->routeIs('master.suppliers')" wire:navigate>
                    {{ __('Supplier') }}
                </flux:navlist.item>

                <flux:navlist.group :heading="__('Customer')" expandable
                    :expanded="request()->routeIs('master.customers') || request()->routeIs('master.customer-types')">
                    <flux:navlist.item icon="users" :href="route('master.customers')"
                        :current="request()->routeIs('master.customers')" wire:navigate>
                        {{ __('List Customer') }}
                    </flux:navlist.item>

                    <flux:navlist.item icon="identification" :href="route('master.customer-types')"
                        :current="request()->routeIs('master.customer-types')" wire:navigate>
                        {{ __('Customer Type') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Inventory')">
                <flux:navlist.item icon="clipboard-document-list" :href="route('inventory.stocks')"
                    :current="request()->routeIs('inventory.stocks')" wire:navigate>
                    {{ __('Stock') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('User Management')">
                <flux:navlist.item icon="users" :href="route('user-management.users')"
                    :current="request()->routeIs('user-management.users')" wire:navigate>
                    {{ __('Users') }}
                </flux:navlist.item>

                <flux:navlist.item icon="shield-check" :href="route('user-management.roles')"
                    :current="request()->routeIs('user-management.roles')" wire:navigate>
                    {{ __('Roles') }}
                </flux:navlist.item>

                <flux:navlist.item icon="key" :href="route('user-management.permissions')"
                    :current="request()->routeIs('user-management.permissions')" wire:navigate>
                    {{ __('Permissions') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />


        @php
            $authenticatedUser = auth()->user();
            $userProfile = $authenticatedUser?->profile;
            $desktopAvatarPath = $userProfile?->avatars;
            $desktopAvatarUrl = $desktopAvatarPath ? asset('storage/' . ltrim($desktopAvatarPath, '/')) : null;
            $formattedBirthDate = $userProfile?->birth_date ? $userProfile->birth_date->translatedFormat('d M Y') : null;
        @endphp

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="$authenticatedUser->name" :initials="$authenticatedUser->initials()"
                icon:trailing="chevrons-up-down" :avatar="$desktopAvatarUrl" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @if ($desktopAvatarUrl)
                                    <img src="{{ $desktopAvatarUrl }}" alt="{{ $authenticatedUser->name }}"
                                        class="h-8 w-8 rounded-lg object-cover" />
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-[#142a28] text-[#d6c172]">
                                        {{ $authenticatedUser->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $authenticatedUser->name }}</span>
                                <span class="truncate text-xs">{{ $authenticatedUser->email }}</span>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1 rounded-lg border border-[#142a28]/60 bg-[#10211f]/50 p-3 text-xs text-[#a9c2bd]">
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Full Name:') }}</span>
                                {{ $userProfile->nama_lengkap ?? $authenticatedUser->name }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Phone:') }}</span>
                                {{ $userProfile->no_telp ?? __('Not provided') }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Birth Date:') }}</span>
                                {{ $formattedBirthDate ?? __('Not provided') }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Address:') }}</span>
                                {{ $userProfile->alamat ?? __('Not provided') }}
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="user-circle" wire:navigate>
                        {{ __('Update Profile') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header
        class="lg:hidden border-b border-[#142a28]/80 bg-[#243f3c]/95 text-[#f4f1ec] shadow-lg shadow-[#0d1a18]/30 backdrop-blur">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="$authenticatedUser->initials()" icon-trailing="chevron-down"
                :avatar="$desktopAvatarUrl" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @if ($desktopAvatarUrl)
                                    <img src="{{ $desktopAvatarUrl }}" alt="{{ $authenticatedUser->name }}"
                                        class="h-8 w-8 rounded-lg object-cover" />
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-[#142a28] text-[#d6c172]">
                                        {{ $authenticatedUser->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $authenticatedUser->name }}</span>
                                <span class="truncate text-xs">{{ $authenticatedUser->email }}</span>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1 rounded-lg border border-[#142a28]/60 bg-[#10211f]/50 p-3 text-xs text-[#a9c2bd]">
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Full Name:') }}</span>
                                {{ $userProfile->nama_lengkap ?? $authenticatedUser->name }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Phone:') }}</span>
                                {{ $userProfile->no_telp ?? __('Not provided') }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Birth Date:') }}</span>
                                {{ $formattedBirthDate ?? __('Not provided') }}
                            </div>
                            <div>
                                <span class="font-semibold text-[#f4f1ec]">{{ __('Address:') }}</span>
                                {{ $userProfile->alamat ?? __('Not provided') }}
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="user-circle" wire:navigate>
                        {{ __('Update Profile') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
