<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/color-template.css', 'resources/css/app.css', 'resources/js/app.js',
                'resources/js/User.js', 'resources/js/ProgramStudi.js', 'resources/js/MataKuliah.js',
                'resources/js/RPS.js', 'resources/js/CPMK.js', 'resources/js/SubCPMK.js', 'resources/js/CPL.js', 'resources/js/Referensi.js',
                'resources/js/Kelas.js', 'resources/js/KelasJadwal.js', 'resources/js/KelasSesi.js', 'resources/js/Nilai.js', 'resources/js/NilaiPeriode.js'])
@fluxAppearance
