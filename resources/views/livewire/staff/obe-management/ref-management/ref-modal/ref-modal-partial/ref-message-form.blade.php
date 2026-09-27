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
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode Referensi
                    </strong> terpenuhi (contoh: <strong
                        class="text-[var(--contrast-main-text)] font-semibold italic">REF-121104</strong>).
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Judul Referensi</strong> dan
                    <strong class="text-[var(--focus-color)] font-semibold">Penulis</strong>
                    telah diisi dengan sesuai pada <strong
                        class="text-[var(--contrast-main-text)] font-semibold">kurikulum</strong>
                    yang
                    berlaku.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Tahun Referensi</strong>
                    telah diisi dengan sesuai.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Link</strong>
                    yang dimasukkan adalah link yang <strong
                        class="text-[var(--contrast-main-text)] font-semibold">valid</strong>.
                </p>
            </div>

            <div x-show="$store.ref?.isEdit == 1" class="flex items-start gap-3">
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
