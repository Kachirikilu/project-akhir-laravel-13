<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.cpmk-management.toolbar-sub-cpmk-management 
        lazy 
        :data="[
            'id'             => $sc->id,
            'kode'           => $sc->kode,
            'kode_scpmk'     => $sc->kode_scpmk,
            'deskripsi'      => $sc->deskripsi,
            'materi'         => $sc->materi,
            'metodologi'     => $sc->metodologi,
            'indikator'      => $sc->indikator,
            'metode'         => $sc->metode,
            'deskripsi_tugas'=> $sc->deskripsi_tugas,
            'waktu_tugas'    => $sc->waktu_tugas,
            'waktu_mandiri'  => $sc->waktu_mandiri,
            'bobot'          => $sc->bobot,
            'isTrashed'      => $sc->trashed(),
        ]"
        wire:key="toolbar-scpmk-{{ $sc->id }}-{{ $key }}" 
    />
</flux:menu>