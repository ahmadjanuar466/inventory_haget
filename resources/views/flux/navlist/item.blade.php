@php $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware([ 'variant' ])

@props([
    'iconVariant' => 'outline',
    'iconTrailing' => null,
    'badgeColor' => null,
    'variant' => null,
    'iconDot' => null,
    'accent' => true,
    'badge' => null,
    'icon' => null,
])

@php
$square ??= $slot->isEmpty();

$iconClasses = Flux::classes($square ? 'size-5!' : 'size-4!');

$activeText = 'text-[#d6c172]';
$inactiveText = 'text-[#f4f1ec]';

$classes = Flux::classes()
    ->add('h-10 lg:h-8 relative flex items-center gap-3 rounded-lg transition-colors duration-150')
    ->add($square ? 'px-2.5!' : '')
    ->add('py-0 text-start w-full px-3 my-px')
    ->add($inactiveText)
    ->add(match ($variant) {
        'outline' => match ($accent) {
            true => [
                "data-current:{$activeText} hover:{$activeText}",
                'data-current:bg-[#243f3c]/80 data-current:border data-current:border-[#142a28]/60',
                'hover:text-[#d6c172] hover:bg-[#243f3c]/60',
                'border border-transparent',
            ],
            false => [
                "data-current:{$activeText}",
                'data-current:bg-[#243f3c]/80 data-current:border data-current:border-[#142a28]/60 data-current:shadow-xs',
                'hover:text-[#d6c172] hover:bg-[#243f3c]/40',
            ],
        },
        default => match ($accent) {
            true => [
                "data-current:{$activeText} hover:{$activeText}",
                'data-current:bg-[#243f3c]/80',
                'hover:text-[#d6c172] hover:bg-[#243f3c]/60',
            ],
            false => [
                "data-current:{$activeText}",
                'data-current:bg-[#243f3c]/80',
                'hover:text-[#d6c172] hover:bg-[#243f3c]/60',
            ],
        },
    })
    ;
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navlist-item>
    <?php if ($icon): ?>
        <div class="relative">
            <?php if (is_string($icon) && $icon !== ''): ?>
                <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
            <?php else: ?>
                {{ $icon }}
            <?php endif; ?>

            <?php if ($iconDot): ?>
                <div class="absolute top-[-2px] end-[-2px]">
                    <div class="size-[6px] rounded-full bg-[#d6c172]"></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($slot->isNotEmpty()): ?>
        <div class="flex-1 text-sm font-semibold leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block" data-content>{{ $slot }}</div>
    <?php endif; ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
        <flux:icon :icon="$iconTrailing" :variant="$iconVariant" class="size-4!" />
    <?php elseif ($iconTrailing): ?>
        {{ $iconTrailing }}
    <?php endif; ?>

    <?php if (isset($badge) && $badge !== ''): ?>
        <flux:navlist.badge :attributes="Flux::attributesAfter('badge:', $attributes, ['color' => $badgeColor])">{{ $badge }}</flux:navlist.badge>
    <?php endif; ?>
</flux:button-or-link>
