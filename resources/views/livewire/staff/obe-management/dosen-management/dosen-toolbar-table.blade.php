<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:staff.obe-management.dosen-management.toolbar-dosen-management 
        lazy 
        :data="[
            'id'        => $user->id,
            'email'     => $user->email,
            'name'      => $user->dosen->name,
            'identity1' => $user->dosen->nip,
            'prodi'  => $user->dosen->pr_rel->prodi ?? '',
            'label_id1' => 'NIP',
            'role'      => $user->role,
            'count_rps' => $user->count_rps,
            'total_sks' => $user->total_sks,
            'isTrashed' => $user->trashed(),
        ]"
        wire:key="toolbar-dosen-{{ $user->id }}-{{ $key }}" 
    />
</flux:menu>