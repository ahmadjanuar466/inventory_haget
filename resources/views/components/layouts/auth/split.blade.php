<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[#193549] text-[#e6f1ff]">
        <div class="relative grid h-dvh flex-col items-center justify-center bg-gradient-to-br from-[#102a43] via-[#193549] to-[#0b1424] px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="relative hidden h-full flex-col border-e border-[#0f2234] p-10 text-[#e6f1ff] lg:flex">
                <div class="absolute inset-0 bg-[#102a43]/90"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md bg-[#0f2234] shadow-md shadow-[#0b1424]/70">
                        <x-app-logo-icon class="me-2 h-7 w-7" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto max-w-xl">
                    <blockquote class="space-y-2 text-[#e6f1ff]">
                        <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 text-[#e6f1ff] sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium text-[#e6f1ff] lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md bg-[#0f2234] shadow-md shadow-[#0b1424]/70">
                            <x-app-logo-icon class="h-9 w-9" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
