<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:all-role.kelas-management.jadwal-management.sesi-management.toolbar-mahasiswa-sesi-management 
        lazy 
        :data="[
            'id'             => $user->mahasiswa->id,
            'jadwal_id'      => $jadwal_id_url,
            'nim'            => $user->mahasiswa->nim,
            'name'           => $user->mahasiswa->name,
            'count_sesi'     => $stats['sesi'] ?? 16,
            'isTrashed'      => $user->trashed(),
        ]"
        wire:key="toolbar-mahasiswa-sesi-{{ $user->id }}-{{ $key }}" 
    />
</flux:menu>