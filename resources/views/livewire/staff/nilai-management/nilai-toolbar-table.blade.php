<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.nilai-management.toolbar-nilai-management lazy :data="[
        'id' => $user->id,
        'label_id1' => $user->label_id1,
        'identity1' => $user->identity1,
        'role' => $user->role,
        'email' => $user->email,
        'pr_id' => $user->mahasiswa->pr_id,
        'dp_id' => $user->mahasiswa->dp_id,
        'fk_id' => $user->mahasiswa->fk_id,
        'prodi' => $user->mahasiswa->pr_rel->prodi,
        'count_rps' => $user->mahasiswa->count_rps,
        'total_sks' => $user->mahasiswa->total_sks,
        'rekap_mhs' => $user->mahasiswa->rekap_mhs ?? '0.00',
        'ipk_mhs' => $user->mahasiswa->ipk_mhs ?? '0.00',
        'mutu_mhs' => $user->mahasiswa->mutu_mhs ?? 'E',
        'angkatan' => $user->mahasiswa->angkatan ?? 'YYYY',
        'isTrashed' => $user->trashed(),
    ]"
        wire:key="toolbar-nilai-{{ $user->id }}-{{ $key }}" />
</flux:menu>
