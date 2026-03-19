<div
    class="flex items-center gap-2 rounded-lg border border-[#0f2234]/80 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#8fb3d9]">
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
    <span>{{ __('Show') }}</span>
    <select wire:model.live="{{ $modellive }}" aria-label="{{ __('Results per page') }}"
        class="bg-transparent text-[#e6f1ff]  focus:border-[#ffc600] focus:outline-none focus:ring-0">
        @foreach ($pageOptions as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
    <span>{{ __('per page') }}</span>
</div>
