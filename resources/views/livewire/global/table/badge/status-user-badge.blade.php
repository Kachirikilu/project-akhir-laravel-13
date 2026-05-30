@switch($sortir ?? $xValue)
    {{-- HIJAU: Status Lulus --}}
    @case('Lulus')
        <flux:badge color="blue" size="sm" icon="academic-cap">{{ $xValue }}</flux:badge>
    @break

    {{-- HIJAU: Status Aktif --}}
    @case('Aktif')
        <flux:badge color="green" size="sm" icon="check-circle">{{ $xValue }}</flux:badge>
    @break

    {{-- KUNING: Status Transisi/Sementara --}}
    @case('Tugas Belajar')
    @case('Izin Belajar')
        <flux:badge color="yellow" size="sm" icon="book-open">{{ $xValue }}</flux:badge>
    @break

    @case('Mutasi')
    @case('Pindah')
        <flux:badge color="yellow" size="sm" icon="arrows-right-left">{{ $xValue }}</flux:badge>
    @break

    @case('Cuti')
    @case('Cuti Sabatika')

    @case('Cuti Luar Tanggungan')
        <flux:badge color="yellow" size="sm" icon="clock">{{ $xValue }}</flux:badge>
    @break

    {{-- ORANGE: Keluar Prosedural / Masalah Administrasi --}}
    @case('Pensiun')
        <flux:badge color="orange" size="sm" icon="briefcase">{{ $xValue }}</flux:badge>
    @break

    @case('Resign')
    @case('Mengundurkan Diri')

    @case('Alih Tugas')
        <flux:badge color="orange" size="sm" icon="arrow-right-start-on-rectangle">{{ $xValue }}</flux:badge>
    @break

    @case('Non-Aktif')
        <flux:badge color="orange" size="sm" icon="minus-circle">{{ $xValue }}</flux:badge>
    @break

    {{-- MERAH: Berhenti Permanen / Sanksi / Masalah Berat --}}
    @case('Diberhentikan')
    @case('Drop Out')
        <flux:badge color="red" size="sm" icon="x-mark">{{ $xValue }}</flux:badge>
    @break

    @case('Meninggal Dunia')
        <flux:badge color="red" size="sm" icon="heart-broken">{{ $xValue }}</flux:badge>
    @break

    @case('Hilang')
        <flux:badge color="red" size="sm" icon="question-mark-circle">{{ $xValue }}</flux:badge>
    @break

    @default
        <flux:badge size="sm" icon="information-circle">{{ $xValue }}</flux:badge>
@endswitch
