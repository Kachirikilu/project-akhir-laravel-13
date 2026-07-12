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

    @include('livewire.global.header.tag-user')

    <div class="flex flex-col gap-6 p-1 sm:p-3 max-w-6xl mx-auto">

        @if (Auth::user()->admin)
            @include('livewire.all-role.dashboard-management.dashboard-admin')
        @endif

        @if (Auth::user()->dosen)
            @include('livewire.all-role.dashboard-management.dashboard-dosen')
        @endif

        @if (Auth::user()->mahasiswa)
            @include('livewire.all-role.dashboard-management.dashboard-mahasiswa')
        @endif

    </div>
    @include('livewire.all-role.dashboard-management.dashboard-card')


</div>
