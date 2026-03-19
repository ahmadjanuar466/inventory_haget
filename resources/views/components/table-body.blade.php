<!-- It always seems impossible until it is done. - Nelson Mandela -->
@props([
    'items' => [],
    'rowView',
    'columns' => 1,
    'itemKey' => 'item',
    'rowData' => [],
    'emptyMessage' => '',
])
<tbody {{ $attributes->class('divide-y divide-[#0f2234]/60 text-[#e6f1ff]') }}>
    @forelse ($items as $item)
        @php
            $rowContext = $rowData;
            $rowContext[$itemKey] = $item;
            $rowContext['loop'] = $loop;
        @endphp
        @include($rowView, $rowContext)
    @empty
        <tr>
            <td colspan="{{ $columns }}" class="px-4 py-6 text-center text-[#8fb3d9]">
                {{ $emptyMessage }}
            </td>
        </tr>
    @endforelse
</tbody>
