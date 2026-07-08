<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.toolbar-rps-mahasiswa-management
        lazy :data="[
            'id' => $n->id,
            'mahasiswa_id' => $mahasiswa->id,
            'pr_id' => $mahasiswa->pr_id,
            'rps_id' => $n->rps_id,
            'kode_rps' => $n->kode_rps,
        
            'name' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
        
            'rps' => $n->rps_rel->rps,
            'draf' => $n->rps_rel->draf,
            'level_mk' => $n->rps_rel->level_mk,
            'mk' => $n->mk,
            'sks' => $n->sks,
        
            'nilai_array' => $n->nilai_array,
            'bobot_rps_array' => $n->bobot_rps_array,
            'kode_cpmk_array' => $n->kode_cpmk_array,
            'kode_scpmk_array' => $n->kode_scpmk_array,
            'metode_array' => $n->metode_array,
        
            'isTrashed' => $n->trashed(),
        ]"
        wire:key="toolbar-rps-mahasiswa-{{ $n->id }}-{{ $key }}-{{ $n->updated_at?->timestamp }}" />
</flux:menu>
