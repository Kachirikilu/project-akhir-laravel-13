<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.obe-management.cpl-management.toolbar-table-cpl-management lazy :data="[
            'id' => $c->id,
        
            'kode' => $c->kode,
            'tingkat_cpl' => $c->tingkat_cpl,

            'rekap_cpl' => $c->rekap_cpl_pr ?? $c->rekap_cpl_mhs ?? null,
            'index_cpl' => $c->index_cpl_pr ?? $c->index_cpl_mhs ?? null,
            'huruf_cpl' => $c->huruf_cpl_pr ?? $c->huruf_cpl_mhs ?? null,
            'idInduk' => $pr_id_url ?? ($mahasiswa_id_url ?? null),
            'isNilaiMahasiswa' => $c->isNilaiMahasiswa ?? null,
            'isTrashed' => $c->trashed(),
        ]"
            wire:key="toolbar-cpl-{{ $c->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.cpl-management.toolbar-table-cpl-management lazy :data="[
            'id' => $c->id,
            'kode' => $c->kode,
            'tingkat_cpl' => $c->tingkat_cpl,
            'pr_id' => $c->prodis->first()?->id,
            'dp_id' => $c->prodis->first()?->dp_id,
            'fk_id' => $c->prodis->first()?->fk_id,
            'deskripsi' => $c->deskripsi,
            'rekap_cpl' => $c->rekap_cpl_pr ?? $c->rekap_cpl_mhs ?? null,
            'index_cpl' => $c->index_cpl_pr ?? $c->index_cpl_mhs ?? null,
            'huruf_cpl' => $c->huruf_cpl_pr ?? $c->huruf_cpl_mhs ?? null,
            'idInduk' => $pr_id_url ?? ($mahasiswa_id_url ?? null),
            'isNilaiMahasiswa' => $c->isNilaiMahasiswa ?? null,
            'isTrashed' => $c->trashed(),
        ]"
            wire:key="toolbar-cpl-{{ $c->id }}-{{ $key }}" />
    @endif
</flux:menu>
