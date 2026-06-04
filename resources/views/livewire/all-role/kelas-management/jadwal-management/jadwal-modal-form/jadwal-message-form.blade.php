<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.footer.error-validation')

    <div
        class="rounded-xl bg-[var(--second-table-trans)] border-[var(--border-wadah-color)] border p-4 shadow-sm backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips & Panduan</span>
        </div>
        <div class="space-y-3">

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode Jadwal Kelas
                    </strong> terpenuhi (contoh: <strong
                        class="text-[var(--contrast-main-text)] font-semibold italic">KXD-121104-A-IDL-26</strong>).
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Jadwal Perkuliahan</strong> telah dipilih
                    pada <strong class="text-[var(--contrast-main-text)] font-semibold">kurikulum</strong> yang berlaku.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Sesi Perkuliahan</strong> berjumlah <strong class="text-[var(--contrast-main-text)] font-semibold">16 Pertemuan</strong>.
                </p>
            </div>

            {{-- 💡 TAMBAHAN PANDUAN FORMULA PENCARIAN NIM UNSRI --}}
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Gunakan pintasan <strong class="text-[var(--focus-color)] font-semibold">Rumus</strong> untuk input cepat (contoh: 
                    <strong class="text-[var(--contrast-main-text)] font-semibold italic">A-IDL.2024;</strong>, atau 
                    <strong class="text-[var(--contrast-main-text)] font-semibold italic">S1.TKE.2024.NIM128;</strong>).
                </p>
            </div>

            @include('livewire.global.modal-form.template-pesan')
        </div>
    </div>
</div>