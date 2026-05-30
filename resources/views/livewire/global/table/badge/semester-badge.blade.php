@switch($sortir ?? $xValue)
    {{-- Tahun 1: Biru/Cyan --}}
    @case(1)
        <flux:badge color="blue" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    @case(2)
        <flux:badge color="cyan" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    {{-- Tahun 2: Hijau/Emerald --}}
    @case(3)
        <flux:badge color="green" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    @case(4)
        <flux:badge color="emerald" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    {{-- Tahun 3: Kuning/Oranye --}}
    @case(5)
        <flux:badge color="yellow" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    @case(6)
        <flux:badge color="orange" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    {{-- Tahun 4: Merah/Ungu (Fase Tugas Akhir) --}}
    @case(7)
        <flux:badge color="red" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
    @break

    @default
        <flux:badge color="purple" size="sm">{{ $textString ?? null }} {{ $xValue ?? '---' }}</flux:badge>
@endswitch
