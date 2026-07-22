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

    @php
        $user = Auth::user();
        if ($user->admin || $user->dosen) {
            $prodi = $rps->mk_rel->prodis->first() ?? null;
            $isSameFk = $user->tingkat <= 2 && $user->fk_id == ($prodi->id ?? null);
            $isSameDp = $user->tingkat <= 3 && $user->dp_id == ($prodi->dp_id ?? null);
            $isSamePr = $user->tingkat <= 4 && $user->pr_id == ($prodi->fk_id ?? null);

            if ($user->dosen) {
                $isDosenClass =
                    $rps->tim_dosens
                        ?->where('pr_id', $user->dosen->pr_id)
                        ->flatMap->dosens?->contains('id', optional($user->dosen)->id) ?? false;
            } else {
                $isDosenClass = false;
            }
            $canAccess = $user->tingkat <= 1 || $isSameFk || $isSameDp || $isSamePr || $isDosenClass;
        } else {
            $canAccess = false;
        }
    @endphp


    @include('livewire.global.header.tag-user')

    @include('livewire.staff.nilai-management.rps-capaian-mahasiswa-management.rps-capaian-mahasiswa-header')
    @include('livewire.admin.user-management.user-search-and-filters', ['role' => 'mahasiswa'])
    @include(
        'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-cpmk-sesi-table',
        ['isRPS' => 1]
    )

</div>
