<!-- It always seems impossible until it is done. - Nelson Mandela -->
@props([
    'items' => [],
    'rowView',
    'columns' => 1,
    'itemKey' => 'item',
    'rowData' => [],
    'emptyMessage' => '',
])
<tbody {{ $attributes->class('divide-y divide-[#142a28]/60 text-[#f4f1ec]') }}>
    @forelse ($items as $item)
        @php
            $rowContext = $rowData;
            $rowContext[$itemKey] = $item;
            $rowContext['loop'] = $loop;
        @endphp
        @include($rowView, $rowContext)
    @empty
        <tr>
            <td colspan="{{ $columns }}" class="px-4 py-6 text-center text-[#a9c2bd]">
                {{ $emptyMessage }}
            </td>
        </tr>
    @endforelse
</tbody>
