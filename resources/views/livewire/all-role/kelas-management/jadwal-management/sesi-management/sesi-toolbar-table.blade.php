<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->admin || Auth::user()->dosen)
        <livewire:all-role.kelas-management.jadwal-management.sesi-management.toolbar-sesi-management lazy
            :data="[
                'id' => $s->id,
                'kode' => $s->kode,
                'jam_mulai' => $s->jam_mulai,
                'jam_berakhir' => $s->jam_berakhir,
                'pertemuan_ke' => $s->pertemuan_ke,
                'tanggal_fix' => $s->tanggal_fix,
                'sks' => $kelas->sks,
                'sent' => $s->sent,
                'canAccess'         => $canAccess,
                'isTrashed' => $s->trashed(),
            ]" wire:key="toolbar-sesi-{{ $s->id }}-{{ $key }}-{{ $s->updated_at?->timestamp }}" />
    @elseif (Auth::user()->mahasiswa)
        <livewire:all-role.kelas-management.jadwal-management.sesi-management.toolbar-sesi-management lazy
            :data="[
                'id' => $s->id,
                'kode' => $s->kode,
                'kode_jadwal' => $jadwal->kode,
                'kode_scpmk' => $s->kode_scpmk,
                'keterangan' => $kehadiran_mhs->keterangan ?? null,
                'pertemuan_ke' => $s->pertemuan_ke,
                'waktu_pelaksanaan' => $s->waktu_pelaksanaan,
                'waktu_berakhir' => $s->waktu_berakhir,
                'waktu_telat' => $s->waktu_telat,
                'waktu_dispensasi' => $s->waktu_dispensasi,
                'sks' => $kelas->sks,
                'mhs_status' => $kehadiran_mhs->status ?? 0,
                'mhs_waktu_presensi' => $kehadiran_mhs?->waktu_presensi?->format('H:i') ?? 0,
                'mhs_keterangan' => $kehadiran_mhs->keterangan ?? 0,
                'isTrashed' => $s->trashed(),
            ]" wire:key="toolbar-sesi-{{ $s->id }}-{{ $key }}-{{ $s->updated_at?->timestamp }}" />
    @endif
</flux:menu>