@switch(true)
    {{-- CYAN: Poin Sempurna / Di atas 100 --}}
    @case(($sortir ?? $xValue) >= 100)
        <flux:badge color="cyan" size="sm" icon="sparkles">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- HIJAU: Aman / Di atas 80 --}}
    @case(($sortir ?? $xValue) >= 80)
        <flux:badge color="green" size="sm" icon="check-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- KUNING: Peringatan Ringan / Di atas 50 --}}
    @case(($sortir ?? $xValue) >= 50)
        <flux:badge color="yellow" size="sm" icon="exclamation-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- ORANGE: Peringatan Keras / Di atas 20 --}}
    @case(($sortir ?? $xValue) >= 20)
        <flux:badge color="orange" size="sm" icon="exclamation-triangle">
            {{ $xValue }}
        </flux:badge>
        @break

    {{-- MERAH: Bahaya / Batas Kritis di bawah 20 --}}
    @case(is_numeric($sortir ?? $xValue))
        <flux:badge color="red" size="sm" icon="x-circle">
            {{ $xValue }}
        </flux:badge>
        @break

    @default
        <flux:badge size="sm" icon="information-circle">
            {{ $xValue }}
        </flux:badge>
@endswitch