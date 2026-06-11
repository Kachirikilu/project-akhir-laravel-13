<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.footer.error-validation', ['show' => $show])

    <div
        class="rounded-xl bg-[var(--second-table-trans)] border-[var(--border-wadah-color)] border p-4 shadow-sm backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>
        <div class="space-y-3">

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode CPL
                    </strong> terpenuhi (contoh: <strong class="text-[var(--contrast-main-text)] font-semibold"><span
                            class="italic">TKE-CPL121104</span></strong>).
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Deskripsi</strong>
                    telah diisi dengan sesuai pada <strong
                        class="text-[var(--contrast-main-text)] font-semibold">kurikulum</strong>
                    yang
                    berlaku.
                </p>
            </div>

            <div x-show="$store.cpl?.isEdit == 1" class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
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
