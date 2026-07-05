<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:all-role.kelas-management.toolbar-kelas-management 
        lazy 
        :data="[
            'id'             => $k->id,
            'pr_id'          => $k->pr_id,
            'rps_id'         => $k->rps_id,
            'kode'           => $k->kode,
            'kode_rps'       => $k->rps_rel->kode ?? null,
            'level_mk'       => $k->rps_rel->level_mk ?? null,
            'rps'            => $k->rps_rel->rps ?? null,
            'kelas'          => $k->kelas,
            'deskripsi_kelas'=> $k->deskripsi_kelas,
            'draf'           => $k->rps_rel->draf ?? null,
            'isTrashed'      => $k->trashed(),
        ]"
        wire:key="toolbar-kelas-{{ $k->id }}-{{ $key }}" 
    />
</flux:menu>