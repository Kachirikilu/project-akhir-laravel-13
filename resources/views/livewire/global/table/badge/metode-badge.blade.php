@switch($sortir ?? $xValue)
    @case('Teori')
        <flux:badge icon="book-open" color="emerald" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Praktik')
    @case('Responsi')
        <flux:badge icon="beaker" color="cyan" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Tugas')
    @case('Logbook')
        <flux:badge icon="pencil-square" color="blue" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('UTS')
    @case('Evaluasi Awal')
    @case('Kuis')
        <flux:badge icon="clipboard-document-check" color="amber" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('UAS')
    @case('Evaluasi Akhir')
    @case('Laporan Akhir')
        <flux:badge icon="document-check" color="orange" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Hasil Proyek')
    @case('Hasil Projek') {{-- Cadangan jika ada variasi ketikan c --}}
    @case('Portofolio')
        <flux:badge icon="light-bulb" color="indigo" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Kerja Praktek')
        <flux:badge icon="briefcase" color="violet" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Skripsi')
        <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Aktivitas Partisipasif')
        <flux:badge icon="user-group" color="rose" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @case('Mandiri')
        <flux:badge icon="user" color="slate" size="sm" variant="pill">
            {{ $xValue }}
        </flux:badge>
    @break

    @default
        <flux:badge icon="information-circle" color="zinc" size="sm" variant="pill">
            {{ $xValue ?? '-' }}
        </flux:badge>
@endswitch