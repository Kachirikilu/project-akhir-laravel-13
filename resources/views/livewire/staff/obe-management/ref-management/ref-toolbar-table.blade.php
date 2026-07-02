<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.referensi-management.toolbar-referensi-management 
        lazy 
        :data="[
            'id'        => $r->id,
            'kode'      => $r->kode,
            'kode_ref'  => $r->kode_ref,
            'citation'  => $r->citation,
            'judul'     => $r->judul,
            'penulis'   => $r->penulis,
            'penerbit'  => $r->penerbit,
            'tahun'     => $r->tahun,
            'link'      => $r->link,
            'isTrashed' => $r->trashed(),
        ]"
        wire:key="toolbar-referensi-{{ $r->id }}-{{ $key }}" 
    />
</flux:menu>