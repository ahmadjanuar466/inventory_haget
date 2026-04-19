<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[#1c3432] text-[#f4f1ec]">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gradient-to-br from-[#243f3c] via-[#1c3432] to-[#0d1a18] p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium text-[#f4f1ec]" wire:navigate>
                    <span class="mb-1 flex h-9 w-9 items-center justify-center rounded-md bg-[#142a28] shadow-md shadow-[#0d1a18]/70">
                        <x-app-logo-icon class="h-9 w-9" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
