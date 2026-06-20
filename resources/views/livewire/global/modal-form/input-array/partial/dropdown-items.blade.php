@php
    $itemLabel = data_get($x, $typeXString, '');
    $itemId = data_get($x, 'id', '');
    $itemKode = data_get($x, 'kode', '');
    $itemLabel2 = isset($typeX2String) ? data_get($x, $typeX2String, '') : null;
    $itemLabel3 = isset($typeX3String) ? data_get($x, $typeX3String, '') : null;
    $itemLabel4 = isset($typeX4String) ? data_get($x, $typeX4String, '') : null;
@endphp

<div class="flex flex-col mr-4">
    {{-- Teks utama tetap aman dengan break-words --}}
    <span class="text-xs sm:text-sm font-medium text-[var(--contrast-main-text)] break-words">{{ $itemLabel }}</span>
    
    {{-- PERBAIKAN UTAMA: Menggunakan gap-x-3 untuk menjaga spasi horizontal tetap konsisten di semua baris --}}
    <div class="text-xs sm:text-sm text-[var(--contrast-main-text)] font-medium text-xs flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 min-w-0">
        <span class="flex items-center">
            - <span class="text-[var(--hover-focus-color)] font-bold ml-1">ID: {{ $itemId }}</span>
        </span>
        
        {{-- Pembatas menggunakan margin horizontal yang tegas --}}
        <span class="text-[var(--contrast-second-text)]">|</span>
        
        <span class="flex items-center">
            @if ($idString == 'mahasiswa_id_array')
                NIM: {{ $itemKode }}
            @else
                {{ $itemKode }}
            @endif
        </span>
        
        @if ($typeX2String ?? null)
            <span class="text-[var(--contrast-second-text)]">|</span>
            <span class="flex items-center">
                @if ($typeX2String == 'count_scpmk')
                    {{ $itemLabel2 }} Pertemuan
                @else
                    {{ $itemLabel2 }}
                @endif
            </span>
        @endif
        
        @if ($typeX3String ?? null)
            <span class="text-[var(--contrast-second-text)]">|</span>
            <span class="flex items-center">
                @if ($typeX3String == 'bobot' || $typeX3String == 'total_bobot')
                    Bobot: {{ $itemLabel3 }}%
                @else
                    {{ $itemLabel3 }}
                @endif
            </span>
        @endif
        
        @if ($typeX4String ?? null)
            <span class="text-[var(--contrast-second-text)]">|</span>
            <span class="flex items-center">
                {{ $itemLabel4 }}
            </span>
        @endif
        
        @if ($typeX5String ?? null)
            <span class="text-[var(--contrast-second-text)]">|</span>
            <span class="flex items-center">
                {{ $itemLabel5 }}
            </span>
        @endif
    </div>
</div>