<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">

    @php
        $user = Auth::user();
        if ($user->admin || $user->dosen) {
            // dump($mahasiswa->pr_id ?? null);
            $isSameFk = $user->tingkat <= 2 && $user->fk_id == ($mahasiswa->pr_id ?? null);
            $isSameDp = $user->tingkat <= 3 && $user->dp_id == ($mahasiswa->pr_rel->dp_id ?? null);
            $isSamePr = $user->tingkat <= 4 && $user->pr_id == ($mahasiswa->pr_rel->fk_id ?? null);

            if ($user->dosen) {
                $checkDosenInRps = function ($rpsRel, $prId) use ($user) {
                    if (!$user?->dosen || !$rpsRel) {
                        return false;
                    }
                    return $rpsRel->tim_dosens
                        ?->where('pr_id', $prId)
                        ->flatMap->dosens?->contains('id', $user->dosen->id) ?? false;
                };
                $isDosenClass =
                    $checkDosenInRps($n->rps_rel, $n->pr_id) ||
                    $checkDosenInRps($n->jadwal_rel?->kelas_rel?->rps_rel, $n->jadwal_rel?->kelas_rel?->pr_id);
            } else {
                $isDosenClass = false;
            }
            $canAccess = $user->tingkat <= 1 || $isSameFk || $isSameDp || $isSamePr || $isDosenClass;
        } else {
            $canAccess = false;
        }
    @endphp

    <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.toolbar-rps-mahasiswa-management
        lazy :data="[
            'id' => $n->id,
            'mahasiswa_id' => $mahasiswa->id,
            'pr_id' => $mahasiswa->pr_id,
            'rps_id' => $n->rps_id,
            'kode_rps' => $n->kode_rps,
        
            'name' => $mahasiswa->name,
            'nim' => $mahasiswa->nim,
        
            // 'rps' => $n->rps_rel->rps,
            // 'draf' => $n->rps_rel->draf,
            // 'level_mk' => $n->rps_rel->level_mk,
            'mk' => $n->mk,
            'sks' => $n->sks,
        
            'nilai_array' => $n->nilai_array,
            'bobot_rps_array' => $n->bobot_rps_array,
            'kode_cpmk_array' => $n->kode_cpmk_array,
            'kode_scpmk_array' => $n->kode_scpmk_array,
            'metode_array' => $n->metode_array,
        
            'canAccess' => $canAccess ?? false,
        
            'isTrashed' => $n->trashed(),
        ]"
        wire:key="toolbar-rps-mahasiswa-{{ $n->id }}-{{ $key }}-{{ $n->updated_at?->timestamp }}" />
</flux:menu>
