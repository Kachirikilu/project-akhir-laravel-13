@php
    $itemLabel = data_get($x, $typeXString, '');
    $itemId = data_get($x, 'id', '');
    $itemKode = data_get($x, 'kode', '');
    $itemLabel2 = isset($typeX2String) ? data_get($x, $typeX2String, '') : null;
    $itemLabel3 = isset($typeX3String) ? data_get($x, $typeX3String, '') : null;
    $itemLabel4 = isset($typeX4String) ? data_get($x, $typeX4String, '') : null;
@endphp

<div class="flex flex-col mr-4">
    <span class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $itemLabel }}</span>
    <div class="text-[var(--contrast-main-text) font-medium text-xs flex items-center mt-1">
        <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                {{ $itemId }}</span></span>
        <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
        @if ($idString == 'mahasiswa_id_array')
            <span>NIM: {{ $itemKode }}</span>
        @else
            <span>{{ $itemKode }}</span>
        @endif
        @if ($typeX2String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
                @if ($typeX2String == 'count_scpmk')
                    {{ $itemLabel2 }} Pertemuan
                @else
                    {{ $itemLabel2 }}
                @endif
            </span>
        @endif
        @if ($typeX3String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
                @if ($typeX3String == 'bobot' || $typeX3String == 'total_bobot')
                    Bobot: {{ $itemLabel3 }}%
                @else
                    {{ $itemLabel3 }}
                @endif
            </span>
        @endif
        @if ($typeX4String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
                {{ $itemLabel4 }}
            </span>
        @endif
        @if ($typeX5String ?? null)
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>
                {{ $itemLabel5 }}
            </span>
        @endif
    </div>
</div>
