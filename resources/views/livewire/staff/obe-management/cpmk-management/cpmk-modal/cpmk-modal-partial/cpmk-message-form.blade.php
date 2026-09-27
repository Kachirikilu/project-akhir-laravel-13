<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.footer.error-validation', ['show' => $show])

    <div
        class="form-message">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>
        <div class="space-y-3">

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode CPMK
                    </strong> terpenuhi (contoh: <strong
                        class="text-[var(--contrast-main-text)] font-semibold italic">CPMK-1211</strong>).
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Capaian Pembelajaran
                        Lulusan</strong> <strong class="text-[var(--contrast-main-text)] font-semibold">(CPL)</strong>
                    telah dipilih
                    pada <strong class="text-[var(--contrast-main-text)] font-semibold">kurikulum</strong> yang berlaku.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Kosongkan <strong class="text-[var(--contrast-second-text)] font-semibold">Deskripsi</strong> untuk
                    menyesuaikan dengan <strong class="text-[var(--focus-color)] font-semibold">Capaian Pembelajaran
                        Lulusan</strong> <strong class="text-[var(--contrast-main-text)] font-semibold">(CPL)</strong>
                    yang
                    telah dipilih.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Sub Capaian Pembelajaran Mata
                        Kuliah</strong> <strong
                        class="text-[var(--contrast-main-text)] font-semibold">(Sub-CPMK)</strong>
                    telah dipilih pada <strong class="text-[var(--contrast-main-text)] font-semibold">kurikulum</strong>
                    yang
                    berlaku.
                </p>
            </div>

            <div x-show="$store.cpmk?.isEdit == 1" class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Rencana Pembelajaran
                        Semester</strong> <strong class="text-[var(--contrast-main-text)] font-semibold">(RPS)</strong>
                    telah
                    disesuaikan terhadap perubahan yang akan dilakukan.
                </p>
            </div>

            @include('livewire.global.modal-form.template-pesan')
        </div>
    </div>
</div>
