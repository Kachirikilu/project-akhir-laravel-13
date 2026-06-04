@switch(true)
    {{-- CYAN: Poin Sempurna / Istimewa --}}
    @case(($sortir ?? $xValue) == 'A')
        <flux:badge color="cyan" size="sm" icon="sparkles">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- HIJAU: Aman / Sangat Baik --}}
    @case(($sortir ?? $xValue) == 'A-')
        <flux:badge color="green" size="sm" icon="check-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- EMERALD/LIME: Baik --}}
    @case(($sortir ?? $xValue) == 'B+')
        <flux:badge color="emerald" size="sm" icon="check">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- KUNING: Peringatan Ringan --}}
    @case(($sortir ?? $xValue) == 'B')
        <flux:badge color="yellow" size="sm" icon="exclamation-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- AMBER: Cukup Pas --}}
    @case(($sortir ?? $xValue) == 'B-')
        <flux:badge color="amber" size="sm" icon="exclamation-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- ORANGE: Batas Bawah Cukup --}}
    @case(($sortir ?? $xValue) == 'C+')
        <flux:badge color="orange" size="sm" icon="exclamation-triangle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- ORANGE PEKAT / ZINC: Peringatan Keras --}}
    @case(($sortir ?? $xValue) == 'C')
        <flux:badge color="orange" size="sm" icon="exclamation-triangle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- MERAH: Bahaya / Hampir Tidak Lulus --}}
    @case(($sortir ?? $xValue) == 'D')
        <flux:badge color="red" size="sm" icon="x-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- MERAH PEKAT: Gagal / Tidak Lulus --}}
    @case(($sortir ?? $xValue) == 'E')
        <flux:badge color="red" size="sm" icon="no-symbol">
            {{ $xValue }}
        </flux:badge>
        @break

    @default
        <flux:badge size="sm" icon="information-circle">
            {{ $xValue }}
        </flux:badge>
@endswitch