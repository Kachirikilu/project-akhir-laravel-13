@switch($x->metode)
    @case('Teori')
        <flux:badge icon="book-open" color="emerald" size="sm" variant="pill">{{ $x->metode }}
        </flux:badge>
    @break

    @case('Praktik')
        <flux:badge icon="beaker" color="cyan" size="sm" variant="pill">{{ $x->metode }}
        </flux:badge>
    @break

    @case('Tugas')
        <flux:badge icon="pencil-square" color="blue" size="sm" variant="pill">
            {{ $x->metode }}
        </flux:badge>
    @break

    @case('UTS')
    @case('UAS')
        <flux:badge icon="clipboard-document-check" color="amber" size="sm" variant="pill">
            {{ $x->metode }}</flux:badge>
    @break

    @case('Hasil Proyek')
        <flux:badge icon="light-bulb" color="indigo" size="sm" variant="pill">
            {{ $x->metode }}
        </flux:badge>
    @break

    @case('Kerja Praktek')
        <flux:badge icon="briefcase" color="violet" size="sm" variant="pill">
            {{ $x->metode }}
        </flux:badge>
    @break

    @case('Skripsi')
        <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="pill">
            {{ $x->metode }}
        </flux:badge>
    @break

    @case('Aktivitas Partisipasif')
        <flux:badge icon="user-group" color="rose" size="sm" variant="pill">
            {{ $x->metode }}
        </flux:badge>
    @break

    @case('Mandiri')
        <flux:badge icon="user" color="slate" size="sm" variant="pill">{{ $x->metode }}
        </flux:badge>
    @break

    @default
        <flux:badge icon="information-circle" color="zinc" size="sm" variant="pill">
            {{ $x->metode ?? '-' }}</flux:badge>
@endswitch
