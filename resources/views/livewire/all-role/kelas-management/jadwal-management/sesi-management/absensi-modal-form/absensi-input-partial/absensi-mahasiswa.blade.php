<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg">
                <flux:icon icon="user" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide"
                    x-text="$store.sesi?.nama_mahasiswa"></h3>
                <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5"
                    x-text="'NIM: ' + $store.sesi?.nim_mahasiswa"></p>
            </div>
        </div>

        <div>
            @include('livewire.global.table.badge.kode-wilayah-badge', [
                'xValue' => $jadwal->kode,
                'sortir' => $jadwal->kode_wilayah,
            ])
        </div>
    </div>

    <div x-data="{
        getIndex(n) {
                if (n >= 86) return '4.00';
                if (n >= 80) return '3.70';
                if (n >= 75) return '3.30';
                if (n >= 70) return '3.00';
                if (n >= 65) return '2.70';
                if (n >= 60) return '2.30';
                if (n >= 56) return '2.00';
                if (n >= 40) return '1.00';
                return '0.00';
            },
    
            getHuruf(n) {
                if (n >= 86) return 'A';
                if (n >= 80) return 'A-';
                if (n >= 75) return 'B+';
                if (n >= 70) return 'B';
                if (n >= 65) return 'B-';
                if (n >= 60) return 'C+';
                if (n >= 56) return 'C';
                if (n >= 40) return 'D';
                return 'E';
            },
    
            getStats() {
                let data = $store.sesi?.list_absensi_array ?? [];
    
                if (!Array.isArray(data)) {
                    data = Object.values(data);
                }
    
                let totalBobot = 0;
                let nilaiBobot = 0;
    
                let hadir = 0;
                let dispensasi = 0;
                let terlambat = 0;
                let izin = 0;
                let sakit = 0;
                let tidakMasuk = 0;
                let poin = 0;
    
                data.forEach(item => {
                    const nilai = parseFloat(item?.nilai ?? 0);
                    let bobot = item?.bobot ?? '0%';
                    bobot = parseFloat(String(bobot).replace('%', ''));
    
                    if (isNaN(bobot)) bobot = 0;
    
                    const bobotDecimal = bobot / 100;
                    totalBobot += bobotDecimal;
                    nilaiBobot += nilai * bobotDecimal;
                });
    
                const nilaiAkhir = totalBobot > 0 ? (nilaiBobot / totalBobot) : 0;
    
                data.forEach(item => {
                    const status = item?.status ?? '';
    
                    switch (status) {
                        case 'Hadir':
                            hadir++;
                            poin += 2;
                            break;
                        case 'Dispensasi':
                            dispensasi++;
                            poin += 2;
                            break;
                        case 'Terlambat':
                            terlambat++;
                            poin += 1;
                            break;
                        case 'Izin':
                            izin++;
                            tidakMasuk++;
                            poin += 1;
                            break;
                        case 'Sakit':
                            sakit++;
                            tidakMasuk++;
                            poin += 1;
                            break;
                        default:
                            tidakMasuk++;
                            break;
                    }
                });
    
                const maxPoin = data.length * 2;
    
                return {
                    masuk: (hadir + dispensasi + terlambat),
                    hadir,
                    dispensasi,
                    terlambat,
                    izin,
                    sakit,
                    tidakMasuk,
                    poinPersen: maxPoin ? Number(((poin / maxPoin) * 100).toFixed(2)) : null,
                    nilai_akhir: Number(nilaiAkhir.toFixed(2)),
                    nilai_index: this.getIndex(nilaiAkhir),
                    nilai_huruf: this.getHuruf(nilaiAkhir)
                };
            }
    }">
        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
            Akumulasi Rekap Absensi
        </label>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2 justify-center">
            <div
                class="col-span-2 p-2.5 rounded-lg border border-lime-200 dark:border-lime-900/60 bg-lime-50/50 dark:bg-lime-950/20 text-center">
                <span class="block text-xs font-medium text-lime-700 dark:text-lime-400">Poin</span>
                <span class="block text-lg font-bold text-lime-800 dark:text-lime-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi" x-text="$store.sesi?.mhs_poin_absensi + '%'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="(getStats()?.poinPersen ?? $store.sesi?.mhs_poin_absensi ?? 0) + '%'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Hadir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_masuk + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().masuk + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Dispensasi</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_dispensasi + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().dispensasi + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Terlambat</span>
                <span class="block text-lg font-bold text-amber-800 dark:text-amber-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_terlambat + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().terlambat + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Izin</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_izin + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().izin + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-sky-200 dark:border-sky-900/60 bg-sky-50/50 dark:bg-sky-950/20 text-center">
                <span class="block text-xs font-medium text-sky-700 dark:text-sky-400">Sakit</span>
                <span class="block text-lg font-bold text-sky-800 dark:text-sky-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_sakit + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().sakit + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 text-center">
                <span class="block text-xs font-medium text-rose-700 dark:text-rose-400">Tidak Hadir</span>
                <span class="block text-lg font-bold text-rose-800 dark:text-rose-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi"
                        x-text="$store.sesi?.mhs_tidak_masuk + ' / ' + '{{ $totalSesiKelas }}'"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().tidakMasuk + ' / ' + '{{ $totalSesiKelas }}'"></span>
                </span>
            </div>

            <div
                class="sm:col-span-2 md:col-span-3 px-2.5 py-4 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Nilai Akhir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi" x-text="$store.sesi?.mhs_nilai_akhir"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().nilai_akhir"></span>
                </span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-3 px-2.5 py-4 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Index (0-4)</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi" x-text="$store.sesi?.mhs_nilai_index"></span>
                    <span wire:loading.remove wire:target="editAbsensi"
                        x-text="getStats().nilai_index"></span></span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-2 px-2.5 py-4 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Huruf</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                    <span wire:loading wire:target="editAbsensi" x-text="$store.sesi?.mhs_nilai_huruf"></span>
                    <span wire:loading.remove wire:target="editAbsensi" x-text="getStats().nilai_huruf"></span>
                </span>
            </div>
        </div>
    </div>
</div>
