<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">

        <div class="flex items-start gap-3 min-w-0 flex-1">
            <div
                class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg shrink-0 mt-0.5">
                <flux:icon icon="user" variant="mini" class="w-5 h-5" />
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide leading-tight break-words"
                    x-text="$store.nilai?.nama"></h3>

                <div class="text-xs text-[var(--contrast-second-text)] mt-1">
                    <span class="font-mono font-medium" x-text="'NIM: ' + $store.nilai?.nim"></span>
                </div>

                <div
                    class="mt-1 text-xs text-[var(--contrast-second-text)] flex items-start gap-1.5 font-semibold min-w-0">
                    <flux:icon icon="book-open" variant="mini" class="w-3.5 h-3.5 mt-0.5 shrink-0" />
                    <span class="break-words leading-relaxed" x-text="$store.nilai?.mk || '---'"></span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 lg:justify-end">
            <span
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-100/60 dark:border-white/10 dark:bg-white/5 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-zinc-600 dark:text-white/70">
                <span x-text="($store.nilai?.sks || '0') + ' SKS'"></span>
            </span>
            <span
                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-100/60 dark:border-white/10 dark:bg-white/5 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-zinc-600 dark:text-white/70">
                <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5 opacity-80" />
                <span x-text="'RPS: ' + ($store.nilai?.kode_rps || '---')"></span>
            </span>
        </div>

    </div>

    <div x-data="{
        nilaiAkhir: 0,
        nilaiMutu: 'E',
        nilaiIndex: '0.00',
    
        hitungRekap() {
            let totalNilaiAkhir = 0;
            let totalBobotTerpakai = 0;
    
            for (let i = 1; i <= 16; i++) {
                let n = parseFloat($store.nilai['nilai_' + i]);
                let b = parseFloat($store.nilai['bobot_' + i]);
    
                // Hanya hitung jika input nilai dan bobot valid (bukan NaN atau kosong)
                if (!isNaN(n) && !isNaN(b)) {
                    totalNilaiAkhir += n * b;
                    totalBobotTerpakai += b;
                }
            }
    
            // 1. Set Nilai Akhir (dibulatkan maksimal 2 angka di belakang koma)
            this.nilaiAkhir = parseFloat(totalNilaiAkhir.toFixed(2));
    
            // 2. Evaluasi Huruf Mutu berdasarkan Nilai Akhir
            let na = this.nilaiAkhir;
            if (na >= 85) this.nilaiMutu = 'A';
            else if (na >= 80) this.nilaiMutu = 'A-';
            else if (na >= 75) this.nilaiMutu = 'B+';
            else if (na >= 70) this.nilaiMutu = 'B';
            else if (na >= 65) this.nilaiMutu = 'B-';
            else if (na >= 60) this.nilaiMutu = 'C+';
            else if (na >= 55) this.nilaiMutu = 'C';
            else if (na >= 40) this.nilaiMutu = 'D';
            else this.nilaiMutu = 'E';
    
            // 3. Evaluasi Angka Index berdasarkan Huruf Mutu
            switch (this.nilaiMutu) {
                case 'A':
                    this.nilaiIndex = '4.00';
                    break;
                case 'A-':
                    this.nilaiIndex = '3.70';
                    break;
                case 'B+':
                    this.nilaiIndex = '3.30';
                    break;
                case 'B':
                    this.nilaiIndex = '3.00';
                    break;
                case 'B-':
                    this.nilaiIndex = '2.70';
                    break;
                case 'C+':
                    this.nilaiIndex = '2.30';
                    break;
                case 'C':
                    this.nilaiIndex = '2.00';
                    break;
                case 'D':
                    this.nilaiIndex = '1.00';
                    break;
                default:
                    this.nilaiIndex = '0.00';
            }
        }
    }" x-effect="hitungRekap()">

        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
            Akumulasi Rekap Nilai
        </label>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2 justify-center">

            <div
                class="sm:col-span-2 md:col-span-3 px-2.5 py-4 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Nilai Akhir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                    <span x-text="nilaiAkhir">0</span>
                </span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-3 px-2.5 py-4 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Index (0-4)</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                    <span x-text="nilaiIndex">0.00</span>
                </span>
            </div>

            <div
                class="sm:col-span-1 md:col-span-2 px-2.5 py-4 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Mutu</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                    <span x-text="nilaiMutu">E</span>
                </span>
            </div>
        </div>
    </div>
</div>
