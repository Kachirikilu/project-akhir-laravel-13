<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.obe-management.cpmk-management.toolbar-sub-cpmk-management lazy :data="[
            'id' => $sc->id,
            'kode' => $sc->kode,
        ]"
            wire:key="toolbar-scpmk-{{ $sc->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.cpmk-management.toolbar-sub-cpmk-management lazy :data="[
            'id' => $sc->id,
            'kode' => $sc->kode,
            'deskripsi' => $sc->deskripsi,
            // 'materi' => $sc->materi,
            // 'metodologi' => $sc->metodologi,
            // 'indikator' => $sc->indikator,
            // 'metode' => $sc->metode,
            // 'deskripsi_tugas' => $sc->deskripsi_tugas,
            // 'waktu_tugas' => $sc->waktu_tugas,
            // 'waktu_mandiri' => $sc->waktu_mandiri,
            // 'bobot' => $sc->bobot,
            'isTrashed' => $sc->trashed(),
        ]"
            wire:key="toolbar-scpmk-{{ $sc->id }}-{{ $key }}" />
    @endif
</flux:menu>
