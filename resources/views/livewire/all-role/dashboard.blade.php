<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'dahsboard-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">



    {{--
    ============================================================
    KOMPONEN: DONUT STAT CARD (Pure SVG — tanpa dependency JS)
    Simpan di: resources/views/livewire/global/dashboard/donut-stat.blade.php

    Cara pakai:
    @include('livewire.all-role.dashboard.donut-stat', [
        'icon'         => 'academic-cap',           // nama flux:icon
        'title'        => 'Capaian Prodi',
        'subtitle'     => '8 dari 12 indikator tercapai',
        'value'        => 78,
        'max'          => 100,
        'displayValue' => '78%',
        'accent'       => 'var(--focus-color)',     // warna solid utk ring aktif
        'accentSoft'   => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
    ])
    ============================================================
--}}


    <div class="flex flex-col gap-6 p-3 sm:p-6 max-w-6xl mx-auto">

        @if (Auth::user()->admin)
            @include('livewire.all-role.dashboard.dashboard-admin')
        @endif

        @if (Auth::user()->dosen)
            @include('livewire.all-role.dashboard.dashboard-dosen')
        @endif

        @if (Auth::user()->mahasiswa)
            @include('livewire.all-role.dashboard.dashboard-mahasiswa')
        @endif

    </div>
    @include('livewire.all-role.dashboard.dashboard-card')


</div>
