<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.tim-dosen-management.toolbar-tim-dosen-management 
        lazy 
        :data="[
            'id'                 => $d->id,
            'kode'               => $d->kode,
            'kode_tim_dosen'     => $d->kode_tim_dosen,
            'tim'                => $d->tim,
            'ketua'              => $d->ketua,
            'nip'                => $d->nip,
            'prodi'              => $d->prodi,
            'count_koordinator'  => $d->count_koordinator,
            'count_pengajar'     => $d->count_pengajar,
            'count_asisten'      => $d->count_asisten,
            'count_rps'          => $d->count_rps,
            'total_sks'          => $d->total_sks,
            'isTrashed'          => $d->trashed(),
        ]"
        wire:key="toolbar-tim-dosen-{{ $d->id }}-{{ $key }}" 
    />
</flux:menu>