<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.nilai-management.toolbar-nilai-management 
        lazy 
        :data="[
            'id'        => $user->id,
            'email'     => $user->email,
            'label_id1' => $user->label_id1,
            'identity1' => $user->identity1,
            'prodi'     => $user->mahasiswa->pr_rel->prodi,
            'role'      => $user->role,
            'rekap_mhs' => $user->mahasiswa->rekap_mhs ?? '0.00',
            'ipk_mhs'   => $user->mahasiswa->ipk_mhs ?? '0.00',
            'mutu_mhs'  => $user->mahasiswa->mutu_mhs ?? 'E',
            'angkatan'  => $user->mahasiswa->angkatan ?? 'YYYY',
            'isTrashed' => $user->trashed(),
        ]"
        wire:key="toolbar-nilai-{{ $user->id }}-{{ $key }}" 
    />
</flux:menu>