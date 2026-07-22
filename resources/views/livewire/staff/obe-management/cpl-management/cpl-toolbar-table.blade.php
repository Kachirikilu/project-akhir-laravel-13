<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.obe-management.cpl-management.toolbar-cpl-management lazy :data="[
            'id' => $c->id,
        
            'kode' => $c->kode,
            'level_cpl' => $c->level_cpl,

        
            'rekap_cpl_pr' => $c->rekap_cpl_pr ?? null,
            'index_cpl_pr' => $c->index_cpl_pr ?? null,
            'mutu_cpl_pr' => $c->mutu_cpl_pr ?? null,
            'isTrashed' => $c->trashed(),
        ]"
            wire:key="toolbar-cpl-{{ $c->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.cpl-management.toolbar-cpl-management lazy :data="[
            'id' => $c->id,
            'kode' => $c->kode,
            'level_cpl' => $c->level_cpl,
            'pr_id' => $c->prodis->first()?->id,
            'dp_id' => $c->prodis->first()?->dp_id,
            'fk_id' => $c->prodis->first()?->fk_id,
            'deskripsi' => $c->deskripsi,
            'rekap_cpl_pr' => $c->rekap_cpl_pr ?? null,
            'index_cpl_pr' => $c->index_cpl_pr ?? null,
            'mutu_cpl_pr' => $c->mutu_cpl_pr ?? null,
            'isTrashed' => $c->trashed(),
        ]"
            wire:key="toolbar-cpl-{{ $c->id }}-{{ $key }}" />
    @endif
</flux:menu>
