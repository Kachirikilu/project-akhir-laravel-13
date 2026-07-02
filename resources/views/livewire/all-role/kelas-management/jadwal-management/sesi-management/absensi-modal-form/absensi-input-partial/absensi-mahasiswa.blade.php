<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">


        <div class="flex items-start gap-3 min-w-0 flex-1">
            <div
                class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg shrink-0 mt-0.5">
                <flux:icon icon="user" variant="mini" class="w-5 h-5" />
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide leading-tight break-words"
                    x-text="$store.sesi?.nama"></h3>

                <div class="text-xs text-[var(--contrast-second-text)] mt-1">
                    <span class="font-mono font-medium" x-text="'NIM: ' + $store.sesi?.nim"></span>
                </div>

                <div
                    class="mt-1 text-xs text-[var(--contrast-second-text)] flex items-start gap-1.5 font-semibold min-w-0">
                    <flux:icon icon="book-open" variant="mini" class="w-3.5 h-3.5 mt-0.5 shrink-0" />
                    <span class="break-words leading-relaxed">{{ $jadwal->kelas_rel->mk ?? 'Mata Kuliah' }}</span>
                </div>
            </div>
        </div>

        

        <div class="flex flex-wrap items-center gap-2 lg:justify-end">
            <span
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-100/60 dark:border-white/10 dark:bg-white/5 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-zinc-600 dark:text-white/70">
                <span>{{ $jadwal->kelas_rel->sks ?? 0 }} SKS</span>
            </span>
            <span
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-100/60 dark:border-white/10 dark:bg-white/5 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-zinc-600 dark:text-white/70">
                <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5 opacity-80" />
                <span>RPS: {{ $jadwal->kelas_rel->rps_rel->kode ?? 'Kode-RPS' }}</span>
            </span>
            @include('livewire.global.table.badge.kode-wilayah-badge', [
                'xValue' => $jadwal->kode_jadwal ?? 'Kelas',
                'sortir' => $jadwal->kode_wilayah ?? null,
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
    
            getMutu(n) {
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
                    // PERBAIKAN: Validasi ekstra jika hasil parsing NaN atau string kosong, set ke 0
                    let nilaiParsed = parseFloat(item?.nilai);
                    const nilai = (!isNaN(nilaiParsed) && item?.nilai !== '') ? nilaiParsed : 0;
    
                    let bobot = item?.bobot ?? '0%';
                    bobot = parseFloat(String(bobot).replace('%', ''));
    
                    if (isNaN(bobot)) bobot = 0;
    
                    const bobotDecimal = bobot / 100;
                    totalBobot += bobotDecimal;
                    nilaiBobot += nilai * bobotDecimal;
                });
    
                // PERBAIKAN: Memastikan pembagian tidak menghasilkan NaN jika totalBobot 0
                const nilaiAkhir = (totalBobot > 0 && !isNaN(nilaiBobot)) ? (nilaiBobot / totalBobot) : 0;
    
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
                    poinPersen: maxPoin ? Number(((poin / maxPoin) * 100).toFixed(2)) : 0, // Ubah null ke 0 agar konsisten teksnya
                    nilai_akhir: nilaiAkhir.toFixed(2),
                    nilai_index: this.getIndex(nilaiAkhir),
                    nilai_mutu: this.getMutu(nilaiAkhir)
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
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_poin_absensi ?? 0) + '%'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats()?.poinPersen ?? $store.sesi?.mhs_poin_absensi ?? 0) + '%'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Hadir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_masuk ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().masuk ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Dispensasi</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_dispensasi ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().dispensasi ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Terlambat</span>
                <span class="block text-lg font-bold text-amber-800 dark:text-amber-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_terlambat ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().terlambat ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Izin</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_izin ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().izin ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-sky-200 dark:border-sky-900/60 bg-sky-50/50 dark:bg-sky-950/20 text-center">
                <span class="block text-xs font-medium text-sky-700 dark:text-sky-400">Sakit</span>
                <span class="block text-lg font-bold text-sky-800 dark:text-sky-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_sakit ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().sakit ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 text-center">
                <span class="block text-xs font-medium text-rose-700 dark:text-rose-400">Tidak Hadir</span>
                <span class="block text-lg font-bold text-rose-800 dark:text-rose-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi"
                        x-text="($store.sesi?.mhs_tidak_masuk ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi"
                        x-text="(getStats().tidakMasuk ?? 0) + ' / ' + '{{ $count_sesi ?? 16 }}'"></span>
                </span>
            </div>

            <div
                class="sm:col-span-2 md:col-span-3 px-2.5 py-4 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Nilai Akhir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi" x-text="$store.sesi?.mhs_nilai_akhir ?? '0.00'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi" x-text="getStats().nilai_akhir"></span>
                </span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-3 px-2.5 py-4 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Index (0-4)</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi" x-text="$store.sesi?.mhs_nilai_index ?? '0.00'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi" x-text="getStats().nilai_index"></span>
                </span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-2 px-2.5 py-4 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Mutu</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                    <span wire:loading wire:target="editNilaiAbsensi" x-text="$store.sesi?.mhs_nilai_mutu ?? 'E'"></span>
                    <span wire:loading.remove wire:target="editNilaiAbsensi" x-text="getStats().nilai_mutu"></span>
                </span>
            </div>
        </div>
    </div>
</div>
