 <nav aria-label="{{ __('Breadcrumb') }}" class="text-sm text-[#8fb3d9]">
     <flux:breadcrumbs>
         @foreach ($datas as $crumb)
             @php
                 $routeName = $crumb['routes'] ?? null;
             @endphp

             @if (!empty($routeName))
                 <flux:breadcrumbs.item wire:navigate href="{{ route($routeName) }}" wire:navigate>
                     {{ __($crumb['title']) }}
                 </flux:breadcrumbs.item>
             @else
                 <flux:breadcrumbs.item>
                     {{ __($crumb['title']) }}
                 </flux:breadcrumbs.item>
             @endif
         @endforeach
     </flux:breadcrumbs>

 </nav>
