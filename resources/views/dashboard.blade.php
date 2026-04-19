<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl text-[#f4f1ec]">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-[#142a28]/80 bg-[#243f3c]/60">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-[#a9c2bd]/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-[#142a28]/80 bg-[#243f3c]/60">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-[#a9c2bd]/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-[#142a28]/80 bg-[#243f3c]/60">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-[#a9c2bd]/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-[#142a28]/80 bg-[#243f3c]/60">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-[#a9c2bd]/20" />
        </div>
    </div>
</x-layouts.app>
