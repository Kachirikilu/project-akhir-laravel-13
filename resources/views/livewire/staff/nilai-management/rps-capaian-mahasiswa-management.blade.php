<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'rps-capaian-mahasiswa-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.global.header.tag-user')

    @include('livewire.staff.nilai-management.rps-capaian-mahasiswa-management.rps-capaian-mahasiswa-header')
    @include('livewire.admin.user-management.user-search-and-filters', ['role' => 'mahasiswa'])
    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-cpmk-sesi-table', ['isRPS' => 1])

</div>
