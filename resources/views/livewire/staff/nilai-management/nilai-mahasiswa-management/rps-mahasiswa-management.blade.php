<div
    x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'rps-mahasiswa-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    {{-- @include('livewire.staff.obe-management.rps-management.rps-show-modal', [
        'alpineKey' => 'nilai?.rps_id_show',
        'isEdit' => 0,
    ]) --}}

    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-header', [
        'alpine' => 'nilai',
        'backUrl' => $isNilaiMhs ? route('nilai-mahasiswa') : route('nilai-mahasiswa-management', ['nim' => $nim_url]),
    ])

    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-card')
    
    {{-- @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form') --}}
    {{-- @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-delete') --}}
</div>
