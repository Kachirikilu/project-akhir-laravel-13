<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'rps-capaian-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.staff.obe-management.obe-table', [
        'xResults' => match ($this->switchTable) {
            'rps' => $rps,
            default => collect([]),
        },
        'xNameString' => match ($this->switchTable) {
            'rps' => 'RPS',
            default => 'Data',
        },
        'withCapaian' => 1,
    ])

</div>
