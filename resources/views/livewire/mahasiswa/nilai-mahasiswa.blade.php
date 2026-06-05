<div
    x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'nilai-mahasiswa' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    {{-- @include('livewire.admin.user-management.user-toolbar')

    @include('livewire.admin.user-management.user-switch-table')
    @include('livewire.admin.user-management.user-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.user-management.user-table')
    </div>

    @include('livewire.admin.user-management.user-modal-form')
    @include('livewire.admin.user-management.user-excel-modal-form')
    @include('livewire.admin.user-management.user-modal-delete') --}}
    @include('livewire.mahasiswa.nilai-mahasiswa.nilai-card')
</div>
