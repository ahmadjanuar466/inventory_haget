<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-[#1c3432] text-[#f4f1ec] antialiased">
    <main class="flex min-h-svh items-center justify-center bg-[#1c3432] px-5 py-8 md:px-10">
        <div class="grid w-full max-w-6xl overflow-hidden rounded-lg border border-[#142a28]/80 bg-[#243f3c]/90 shadow-2xl shadow-[#0d1a18]/50 lg:grid-cols-[1.08fr_0.92fr]">
            <section class="relative flex min-h-[380px] flex-col justify-between overflow-hidden bg-[#355856] p-8 text-[#f4f1ec] md:p-10">
                <div class="absolute inset-0 opacity-[0.08]"
                    style="background-image: linear-gradient(#f4f1ec 1px, transparent 1px), linear-gradient(90deg, #f4f1ec 1px, transparent 1px); background-size: 42px 42px;">
                </div>

                <div class="relative z-10">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-[#f4f1ec]" wire:navigate>
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-[#1c3432]/80 shadow-lg shadow-[#0d1a18]/30">
                            <x-app-logo-icon class="h-12 w-12" />
                        </span>
                        <span class="text-lg font-semibold leading-none"> Inventory System</span>
                    </a>

                    <div class="mt-12 max-w-xl">
                        <p class="text-lg font-semibold text-[#d6c172]">haget.id | </p>
                        <h1 class="mt-4 text-4xl font-semibold leading-tight md:text-5xl">
                                 Sensasi Kesegaran sejak tegukan pertama
                        </h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-[#d7e2df]">
                            Menghadirkan produk susu kurma yang segar, higienis, dan menggunakan bahan alami tanpa tambahan gula buatan.
                        </p>
                    </div>
                </div>

                <div class="relative z-10 mt-10 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-[#f4f1ec]/15 bg-[#1c3432]/45 p-4">
                        <p class="text-2xl font-semibold">01</p>
                        <p class="mt-2 text-sm text-[#d7e2df]">Trusted Brand</p>
                    </div>
                    <div class="rounded-lg border border-[#f4f1ec]/15 bg-[#1c3432]/45 p-4">
                        <p class="text-2xl font-semibold">02</p>
                        <p class="mt-2 text-sm text-[#d7e2df]">Healthy Lifestyle.</p>
                    </div>
                    <div class="rounded-lg border border-[#f4f1ec]/15 bg-[#1c3432]/45 p-4">
                        <p class="text-2xl font-semibold">03</p>
                        <p class="mt-2 text-sm text-[#d7e2df]">Berkah dan Amanah.</p>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center bg-[#203936] px-6 py-10 md:px-10">
                <div class="w-full max-w-md">
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-[#d6c172]">Selamat datang kembali</p>
                        <h2 class="mt-3 text-3xl font-semibold leading-tight text-[#f4f1ec]">
                            Masuk ke akun Haget
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-[#a9c2bd]">
                            Gunakan email dan password yang sudah terdaftar untuk melanjutkan pekerjaan hari ini.
                        </p>
                    </div>

                    <x-auth-session-status class="mb-5 text-center text-sm font-medium text-[#d6c172]" :status="session('status')" />

                    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
                        @csrf

                        <flux:input name="email" :label="__('Email address')" type="email" required autofocus
                            autocomplete="email" placeholder="admin@haget.id" />

                        <div class="relative">
                            <flux:input name="password" :label="__('Password')" type="password" required
                                autocomplete="current-password" :placeholder="__('Password')" viewable />

                            @if (Route::has('password.request'))
                                <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                                    {{ __('Forgot your password?') }}
                                </flux:link>
                            @endif
                        </div>

                        <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

                        <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                            {{ __('Log in') }}
                        </flux:button>
                    </form>

                    @if (Route::has('register'))
                        <div class="mt-7 space-x-1 text-center text-sm text-[#a9c2bd] rtl:space-x-reverse">
                            <span>{{ __('Don\'t have an account?') }}</span>
                            <flux:link class="text-accent" :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>

    @fluxScripts
</body>

</html>
