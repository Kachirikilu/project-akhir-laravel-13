@if (Auth::user()->admin || Auth::user()->dosen)
    {{-- Total Absensi Semua Mahasiswa --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Total Poin Absensi
        </span>

        <span class="text-lg font-semibold text-[var(--focus-color)]">
            {{ $absensi['mhs_poin_absensi_percent'] }}%
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Poin Seluruh Sesi
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Masuk
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span class="font-semibold text-emerald-500">{{ $absensi['mhs_masuk'] ?? 0 }}</span>
            / {{ ((int) $totalSesiKelas) * ((int) ($totalMahasiswaKelas ?? 0)) }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Akumulasi Seluruh Sesi
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Izin / Sakit
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span class="font-semibold text-amber-500">{{ ((int) ($absensi['mhs_izin'] ?? 0)) + ((int) ($absensi['mhs_sakit'] ?? 0)) }}</span>
            / {{ ((int) $totalSesiKelas) * ((int) ($totalMahasiswaKelas ?? 0)) }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Akumulasi Seluruh Sesi
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Tidak Hadir
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span class="font-semibold text-red-500">{{ $absensi['mhs_tidak_masuk'] ?? 0 }}</span>
            / {{ ((int) $totalSesiKelas) * ((int) ($totalMahasiswaKelas ?? 0)) }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Akumulasi Seluruh Sesi
        </span>
    </div>
@elseif(Auth::user()->mahasiswa)
    {{-- Absensi Mahasiswa Login --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Poin Absensi Saya
        </span>

        <span class="text-lg font-semibold text-[var(--focus-color)]">
            {{ $absensi['mahasiswa']['mhs_poin_absensi_percent'] ?? 0 }}%
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Total Poin Kehadiran
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Masuk
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span class="font-semibold text-emerald-500">{{ $absensi['mahasiswa']['mhs_masuk'] ?? 0 }}</span>
            / {{ $totalSesiKelas ?? 16 }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Kehadiran Saya
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Izin / Sakit
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span
                class="font-semibold text-amber-500">{{ ($absensi['mahasiswa']['mhs_izin'] ?? 0) + ($absensi['mahasiswa']['mhs_sakit'] ?? 0) }}</span>
            / {{ $totalSesiKelas ?? 16 }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Izin & Sakit Saya
        </span>
    </div>

    <div class="flex flex-col gap-1">
        <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Tidak Hadir
        </span>

        <span class="text-lg text-[var(--focus-color)]">
            <span class="font-semibold text-red-500">{{ $absensi['mahasiswa']['mhs_tidak_masuk'] ?? 0 }}</span>
            / {{ $totalSesiKelas ?? 16 }}
        </span>

        <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
            Ketidakhadiran Saya
        </span>
    </div>

    {{-- $absensi['mahasiswa'] = [
        'mhs_poin_absensi_percent' => ($myUser?->mhs_poin_absensi ?? 0) / (2 * ($totalSesiKelas ?? 16)) * 100,
        'mhs_poin_absensi' => $myUser?->mhs_poin_absensi ?? 0,
        'mhs_absensi' => $myUser?->mhs_absensi ?? 0,
        'mhs_masuk' => $myUser?->mhs_masuk ?? 0,
        'mhs_hadir' => $myUser?->mhs_hadir ?? 0,
        'mhs_terlambat' => $myUser?->mhs_terlambat ?? 0,
        'mhs_izin' => $myUser?->mhs_izin ?? 0,
        'mhs_sakit' => $myUser?->mhs_sakit ?? 0,
        'mhs_dispensasi' => $myUser?->mhs_dispensasi ?? 0,
        'mhs_absen' => $myUser?->mhs_absen ?? 0,
        'mhs_tidak_masuk' => $myUser?->mhs_tidak_masuk ?? 0,
    ]; --}}
@endif
