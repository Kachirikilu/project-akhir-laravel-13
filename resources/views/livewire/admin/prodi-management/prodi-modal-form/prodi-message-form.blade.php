<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.error-validation')

    {{-- 💡 2. Tips (Di bawah Error) --}}
    <div
        class="rounded-xl bg-[var(--second-table-trans)] border-[var(--border-wadah-color)] border p-4 shadow-sm backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed" x-data="{
                    {{-- JS Wrap diperbarui untuk mendukung dark mode pada tag strong --}}
                    wrap: (txt) => `<strong class='text-[var(--focus-color)] font-semibold'>${txt}</strong>`,
                
                        get labels() {
                            const mapping = {
                                'prodi': ['Program Studi', 'ID Departemen'],
                                'departemen': ['Departemen', 'ID Fakultas'],
                                'fakultas': ['Fakultas']
                            };
                            return mapping[$store.prodi?.typeModal] || [];
                        },
                
                        formatList(arr) {
                            if (arr.length === 0) return '';
                            if (arr.length === 1) return this.wrap(arr[0]);
                            const last = arr.pop();
                            return arr.map(i => this.wrap(i)).join(', ') + ' dan ' + this.wrap(last);
                        }
                }">
                    Pastikan <span x-html="formatList(labels)"></span> yang dimasukkan adalah
                    <strong class="text-[var(--contrast-main-text)] font-semibold">unik</strong> dan
                    <strong class="text-[var(--contrast-main-text)] font-semibold">valid</strong>.
                </p>
            </div>


            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode
                        <span
                            x-text="
                            $store.prodi?.typeModal === 'prodi' ? 'Program Studi' :
                            $store.prodi?.typeModal === 'departemen' ? 'Departemen' :
                            $store.prodi?.typeModal === 'fakultas' ? 'Fakultas' :
                            'Data'
                        "></span>
                    </strong> minimal 3 huruf.
                </p>
            </div>

            <template x-if="$store.prodi?.typeModal == 'prodi'" x-cloak>

                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Pastikan <strong class="text-[var(--focus-color)] font-semibold">Strata</strong>
                        telah dipilih dengan sesuai (contoh: 
                        <strong class="text-[var(--contrast-main-text)] font-semibold italic">Sarjana</strong>,
                        <strong class="text-[var(--contrast-main-text)] font-semibold italic">Magister</strong>,
                        <strong class="text-[var(--contrast-main-text)] font-semibold italic">Doktor</strong>).
                    </p>
                </div>

            </template>

                @include('livewire.global.modal-form.template-pesan')
        </div>
    </div>
</div>
