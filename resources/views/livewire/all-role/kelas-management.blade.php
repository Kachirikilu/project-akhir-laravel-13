<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'kelas-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">


    @include('livewire.global.header.tag-user')

    @include('livewire.all-role.kelas-management.kelas-toolbar')
    @include('livewire.all-role.kelas-management.kelas-switch-table')

    @include('livewire.all-role.kelas-management.kelas-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable, switchingTable2">
        @if ($switchTable2 == 'card')
            @include('livewire.all-role.kelas-management.kelas-card')
        @elseif ($switchTable2 == 'table')
            @include('livewire.all-role.kelas-management.kelas-table')
        @endif

    </div>

</div>
