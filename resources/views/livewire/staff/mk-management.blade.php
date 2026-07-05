<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'mata-kuliah-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.global.header.tag-user')

    @include('livewire.staff.mk-management.mk-toolbar')
    @include('livewire.staff.mk-management.mk-switch-table')

    @include('livewire.staff.mk-management.mk-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.staff.mk-management.mk-table')
    </div>


    {{-- @include('livewire.staff.mk-management.mk-modal-form')
    @include('livewire.staff.mk-management.mk-modal-delete') --}}
</div>
