<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">

    @php
        $user = Auth::user();
        $dosen_id = optional($user->dosen)->id;
        $pr_id = $k->pr_id;

        $isDosenClass = false;
        if ($dosen_id && $k->rps_rel?->tim_dosens) {
            $isDosenClass = $k->rps_rel?->tim_dosens?->where('pr_id', $pr_id)->flatMap->dosens->contains('id', $dosen_id);
        }
    @endphp
    <livewire:all-role.kelas-management.toolbar-kelas-management lazy :data="[
        'id' => $k->id,
        'pr_id' => $pr_id,
        'dp_id' => $k->pr_rel->dp_id,
        'fk_id' => $k->pr_rel->fk_id,
        'rps_id' => $k->rps_id,
        'kode' => $k->kode,
        // 'kode_rps'       => $k->rps_rel->kode ?? null,
        // 'level_mk'       => $k->rps_rel->level_mk ?? null,
        // 'rps'            => $k->rps_rel->rps ?? null,
        'kelas' => $k->kelas,
        'deskripsi_kelas' => $k->deskripsi_kelas,
        // 'draf'           => $k->rps_rel->draf ?? null,
        'isDosenClass' => $isDosenClass,
        'isTrashed' => $k->trashed(),
    ]"
        wire:key="toolbar-kelas-{{ $k->id }}-{{ $key }}" />
</flux:menu>
