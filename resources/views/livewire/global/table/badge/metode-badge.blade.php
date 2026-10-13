@php
    $rawVal = $sortir ?? $xValue ?? '';
    $normVal = strtoupper(trim((string) $rawVal));

    $utsFields = array_map('strtoupper', config('rps.uts_fields', ['UTS', 'EVALUASI AWAL']));
    $uasFields = array_map('strtoupper', config('rps.uas_fields', ['UAS', 'EVALUASI AKHIR', 'LAPORAN AKHIR', 'HASIL PROYEK', 'HASIL PROJEK']));
    $proyekFields = ['HASIL PROYEK', 'HASIL PROJEK', 'PORTOFOLIO'];
    $pureUasFields = array_diff($uasFields, $proyekFields);
@endphp

@switch(true)
    @case($normVal === 'TEORI')
        <flux:badge icon="book-open" color="emerald" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case(in_array($normVal, ['PRAKTIK', 'RESPONSI']))
        <flux:badge icon="beaker" color="cyan" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case(in_array($normVal, ['TUGAS', 'LOGBOOK', 'KUIS']))
        <flux:badge icon="pencil-square" color="blue" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case(in_array($normVal, $utsFields))
        <flux:badge icon="clipboard-document-check" color="amber" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case(in_array($normVal, $pureUasFields))
        <flux:badge icon="document-check" color="orange" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case(in_array($normVal, $proyekFields))
        <flux:badge icon="light-bulb" color="indigo" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case($normVal === 'KERJA PRAKTEK')
        <flux:badge icon="briefcase" color="violet" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case($normVal === 'SKRIPSI')
        <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case($normVal === 'AKTIVITAS PARTISIPASIF')
        <flux:badge icon="user-group" color="rose" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @case($normVal === 'MANDIRI')
        <flux:badge icon="user" color="slate" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue }}
        </flux:badge>
    @break

    @default
        <flux:badge icon="information-circle" color="zinc" size="sm" variant="{{ $variant ?? 'pill' }}">
            {{ $xValue ?? '-' }}
        </flux:badge>
@endswitch