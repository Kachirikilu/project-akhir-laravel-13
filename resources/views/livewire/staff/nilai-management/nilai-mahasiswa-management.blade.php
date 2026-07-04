<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'nilai-mahasiswa-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    
    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-header', [
        'alpine' => 'periode',
        'noBackUrl' => $isNilaiMhs ? 1 : 0,
    ])

    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-card')

</div>
