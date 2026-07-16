<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if ($kj_id_url ?? false)
        <livewire:all-role.kelas-management.jadwal-management.sesi-management.toolbar-mahasiswa-sesi-management lazy
            :data="[
                'id' => $user->mahasiswa->id,
                'kj_id' => $kj_id_url ?? null,
                'nim' => $user->mahasiswa->nim,
                'name' => $user->mahasiswa->name,
                'count_sesi' => $stats['sesi'] ?? 16,
                'isTrashed' => $user->trashed(),
            ]" wire:key="toolbar-mahasiswa-sesi-{{ $user->id }}-{{ $key }}" />
    @else
        @php
            $nilaiTerbaik = null;
            if ($rps ?? false) {
                $nilaiTerbaik = $user->mahasiswa->nilai_mahasiswas
                    ->where('rps_id', $rps->id)
                    ->sortByDesc('total_skor')
                    ->first();
            }
        @endphp

        <livewire:all-role.kelas-management.jadwal-management.sesi-management.toolbar-mahasiswa-sesi-management lazy
            :data="[
                'id' => $user->mahasiswa->id,
                'name' => $user->mahasiswa->name,
                'nim' => $user->mahasiswa->nim,
                'kode_rps' => $rps->kode,
                'mk' => $rps->mk_rel->mk,
                'sks' => $rps->mk_rel->sks,
                'nilai_id' => $nilaiTerbaik->id ?? false,
                'nilai_array' => $nilaiTerbaik->nilai_array ?? false,
                'bobot_rps_array' => $nilaiTerbaik->bobot_rps_array ?? false,
                'kode_cpmk_array' => $nilaiTerbaik->kode_cpmk_array ?? false,
                'kode_scpmk_array' => $nilaiTerbaik->kode_scpmk_array ?? false,
                'metode_array' => $nilaiTerbaik->metode_array ?? false,
            ]" wire:key="toolbar-mahasiswa-sesi-{{ $user->id }}-{{ $key }}" />
    @endif

</flux:menu>
