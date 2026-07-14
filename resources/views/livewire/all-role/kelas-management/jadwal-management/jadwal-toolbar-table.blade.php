<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:all-role.kelas-management.jadwal-management.toolbar-jadwal-management 
        lazy 
        :data="[
            'id'                => $j->id,
            'kelas_id'          => $kelas->id ?? 0,
            'kode'              => $j->kode,
            'kode_jadwal'       => $j->kode_jadwal,
            'kode_kelas'        => $j->kode_kelas,
            'label_kelas'       => $j->label_kelas,
            'kode_wilayah'      => $j->kode_wilayah,
            'label_extra'       => $j->label_extra,
            'sks'               => $j->sks,
            'hari_pelaksanaan'  => $j->hari_pelaksanaan,
            'jam_mulai'         => $j->jam_mulai,
            'jam_berakhir'      => $j->jam_berakhir,
            'tanggal_mulai'     => $j->tanggal_mulai,
            'tanggal_berakhir'  => $j->tanggal_berakhir,
            'kapasitas'         => $j->kapasitas,
            'password'          => $j->password,
            'is_my_class'       => $j->is_my_class,
            'with_pw'           => $j->with_pw,
            'isJadwalOnly'       => $isJadwalOnly ?? 0,
            'isTrashed'         => $j->trashed(),
        ]"
        wire:key="toolbar-jadwal-{{ $j->id }}-{{ $key }}" 
    />
</flux:menu>