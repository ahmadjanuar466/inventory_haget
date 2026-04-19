<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-[#1c3432] text-[#f4f1ec]">
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gradient-to-br from-[#243f3c] via-[#1c3432] to-[#0d1a18] p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">


            <div class="flex flex-col gap-6">
                <div
                    class="rounded-lg border border-[#142a28] bg-[#243f3c]/95 text-[#f4f1ec] shadow-2xl shadow-[#0d1a18]/60 backdrop-blur">
                    <div class="px-10 py-8">
                        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium text-[#f4f1ec]"
                            wire:navigate>
                            <span
                                class="flex h-16 w-16 items-center justify-center rounded-md bg-[#142a28] shadow-md shadow-[#0d1a18]/70">
                                <x-app-logo-icon class="h-12 w-12" />
                            </span>

                            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
