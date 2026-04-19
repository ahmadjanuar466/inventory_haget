<flux:modal {{ $attributes->class([$class])->merge([
    'name' => $name,
    'focusable' => true,
    'wire:key' => $attributes->get('wire:key', 'modal-' . $name),
]) }} @close="$wire.{{ $closeAction }}()">
    <div class="relative space-y-6">
        @if (! empty($wireTarget))
            <div wire:loading wire:target="{{ $wireTarget }}"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ $loadingMessage ?? __('Processing...') }}
                </span>
            </div>
        @endif

        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">{{ __($title) }}</flux:heading>
                <flux:text variant="subtle">{{ __($subtitle) }}</flux:text>
            </div>

            <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="{{ $closeAction }}">
                <span class="sr-only">{{ __('Close') }}</span>
            </flux:button>
        </div>

        @if ($createFeedback !== '')
            <div
                class="rounded-lg border border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-100 shadow-inner shadow-green-900/30">
                {{ $createFeedback }}
            </div>
        @endif

        {{ $slot }}
    </div>
</flux:modal>
