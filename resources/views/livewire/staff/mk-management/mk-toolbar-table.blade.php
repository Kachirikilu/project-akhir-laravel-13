<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.mk-management.toolbar-mk-management lazy :data="[
            'id' => $mk->id,
            'kode' => $mk->kode,
        ]"
            wire:key="toolbar-mk-{{ $mk->id }}-{{ $key }}" />
    @else
        <livewire:staff.mk-management.toolbar-mk-management lazy :data="[
            'id' => $mk->id,
            'kode' => $mk->kode,
            'level_mk' => $mk->level_mk,
            'pr_id' => $mk->prodis->first()?->id,
            'dp_id' => $mk->prodis->first()?->dp_id,
            'fk_id' => $mk->prodis->first()?->fk_id,
            'kode_blok' => $mk->kode_blok,
            // 'digit_semester' => $mk->digit_semester,
            'digit_mk' => $mk->digit_mk,
            'mk' => $mk->mk,
            'semester' => $mk->semester,
            // 'sks' => $mk->sks,
            // 'tipe_sks' => $mk->tipe_sks,
            // 'wajib' => $mk->wajib,
            'deskripsi' => $mk->deskripsi,
            // 'bahan_kajian' => $mk->bahan_kajian,
            'isTrashed' => $mk->trashed(),
        ]"
            wire:key="toolbar-mk-{{ $mk->id }}-{{ $key }}" />
    @endif
</flux:menu>
