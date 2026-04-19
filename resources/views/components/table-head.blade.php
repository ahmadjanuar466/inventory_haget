<!-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama -->
<thead class="bg-[#142a28]/80 text-xs uppercase tracking-wide text-[#a9c2bd]">

    <tr>
        @foreach ($head as $datas)
            @if ($datas !== 'Actions')
                <th class="px-4 py-3">{{ __($datas) }}</th>
            @else
                <th class="px-4 py-3 text-right">{{ __($datas) }}</th>
            @endif
        @endforeach
    </tr>
</thead>
