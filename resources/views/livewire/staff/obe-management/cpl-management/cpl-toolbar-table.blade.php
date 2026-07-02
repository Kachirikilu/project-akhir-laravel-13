<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.cpl-management.toolbar-cpl-management 
        lazy 
        :data="[
            'id'            => $c->id,
            'kode'          => $c->kode,
            'kode_cpl'      => $c->kode_cpl,
            'level_cpl'     => $c->level_cpl,
            'deskripsi'     => $c->deskripsi,
            'rekap_cpl_pr'  => $c->rekap_cpl_pr ?? null,
            'index_cpl_pr'  => $c->index_cpl_pr ?? null,
            'mutu_cpl_pr'   => $c->mutu_cpl_pr ?? null,
            'isTrashed'     => $c->trashed(),
        ]"
        wire:key="toolbar-cpl-{{ $c->id }}-{{ $key }}" 
    />
</flux:menu>