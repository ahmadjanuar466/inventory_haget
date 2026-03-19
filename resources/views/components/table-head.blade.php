<!-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama -->
<thead class="bg-[#0f2234]/80 text-xs uppercase tracking-wide text-[#8fb3d9]">

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
