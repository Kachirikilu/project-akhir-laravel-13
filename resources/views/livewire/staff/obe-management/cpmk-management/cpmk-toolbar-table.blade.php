<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.cpmk-management.toolbar-cpmk-management 
        lazy 
        :data="[
            'id'            => $c->id,
            'kode'          => $c->kode,
            'kode_cpmk'     => $c->kode_cpmk,
            'deskripsi_cpl' => $c->deskripsi_cpl,
            'isTrashed'     => $c->trashed(),
        ]"
        wire:key="toolbar-cpmk-{{ $c->id }}-{{ $key }}" 
    />
</flux:menu>