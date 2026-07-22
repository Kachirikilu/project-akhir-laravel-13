<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
            <livewire:staff.obe-management.rps-management.toolbar-rps-management lazy :data="[
            'id' => $r->id,
            'kode' => $r->kode,
            'noData' => 0,
            'isTrashed' => $r->trashed(),
        ]"
            wire:key="toolbar-rps-{{ $r->id }}-{{ $key }}" />
    @elseif ($noData ?? false)
        <livewire:staff.obe-management.rps-management.toolbar-rps-management lazy :data="[
            'id' => $r->id,
            'kode' => $r->kode,
            'noData' => 1,
            'isTrashed' => $r->trashed(),
        ]"
            wire:key="toolbar-rps-{{ $r->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.rps-management.toolbar-rps-management lazy :data="[
            'id' => $r->id,
            'kode' => $r->kode,
            'level_mk'      => $r->mk_rel->level_mk,
            'pr_id'         => $r->mk_rel->prodis->first()?->id,
            'dp_id'         => $r->mk_rel->prodis->first()?->dp_id,
            'fk_id'         => $r->mk_rel->prodis->first()?->fk_id,
            // 'rps' => $r->rps,
            // 'level_mk' => $r->level_mk,
            'is_draf' => $r->is_draf,
            'deskripsi_rps' => $r->deskripsi_rps,
            'mk_id' => $r->mk_id,
            'kode_mk' => $r->kode_mk,
            'mk' => $r->mk,
            'akademik' => $r->akademik,
            'count_scpmk' => $r->count_scpmk,
            'bobot_uts' => $r->bobot_uts,
            'bobot_uas' => $r->bobot_uas,
            'total_bobot' => $r->total_bobot,
            'kode_semester' => $r->kode_semester,
            'noData' => 0,
            'isMK' => $isMK ?? false,
            'isTrashed' => $r->trashed(),
        ]"
            wire:key="toolbar-rps-{{ $r->id }}-{{ $key }}" />
    @endif

</flux:menu>
