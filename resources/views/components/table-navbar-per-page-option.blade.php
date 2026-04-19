<div
    class="flex items-center gap-2 rounded-lg border border-[#142a28]/80 bg-[#10211f]/70 px-3 py-2 text-sm text-[#a9c2bd]">
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
    <span>{{ __('Show') }}</span>
    <select wire:model.live="{{ $modellive }}" aria-label="{{ __('Results per page') }}"
        class="bg-transparent text-[#f4f1ec]  focus:border-[#d6c172] focus:outline-none focus:ring-0">
        @foreach ($pageOptions as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
    <span>{{ __('per page') }}</span>
</div>
