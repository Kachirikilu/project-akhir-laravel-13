<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.footer.error-validation')

    <div
        class="form-message">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>

        <div class="space-y-3">
            {{-- Tips 1: Nama & Kode Unik --}}
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed" x-data="{
                    wrap: (txt) => `<strong class='text-[var(--focus-color)] font-semibold'>${txt}</strong>`,
                    get labels() {
                        const mapping = {
                            'mk-prodi': ['Program Studi'],
                            'mk-departemen': ['Program Studi', 'Departemen'],
                            'mk-fakultas': ['Program Studi', 'Fakultas'],
                            'mk-universitas': ['Program Studi']
                        };
                        return mapping[$store.mk?.typeModal] || [];
                    },
                    formatList(arr) {
                        if (arr.length === 0) return '';
                        const items = [...arr];
                        if (items.length === 1) return this.wrap(items[0]);
                        const last = items.pop();
                        return items.map(i => this.wrap(i)).join(', ') + ' dan ' + this.wrap(last);
                    }
                }">
                    Pastikan <span x-html="formatList(labels)"></span> yang dimasukkan <strong
                        class="text-[var(--contrast-main-text)] font-semibold">sesuai</strong> dengan
                    kurikulum yang berlaku.
                </p>
            </div>

            {{-- Tips 2: Validasi Khusus MK (SKS & Semester) --}}
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">SKS</strong> dan <strong
                        class="text-[var(--focus-color)] font-semibold">Semester</strong> <strong
                        class="text-[var(--contrast-main-text)] font-semibold">(1-8)</strong> diisi
                    dengan angka yang
                    valid.
                </p>
            </div>

            {{-- Tips 3: Minimal Karakter Kode --}}
            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Kode Mata Kuliah
                    </strong> terpenuhi (contoh: <strong
                        class="text-[var(--contrast-main-text)] font-semibold italic">TKE1107</strong>).
                </p>
            </div>

            {{-- Tips 4: Relasi Program Studi --}}
            <template x-if="$store.mk?.typeModal == 'mk'" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-xs sm:text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Pilih <strong class="text-[var(--contrast-main-text)] font-semibold">Program Studi</strong> yang
                        tepat agar Mata Kuliah muncul pada kurikulum yang sesuai.
                    </p>
                </div>
            </template>

            {{-- Tips Umum --}}
            @include('livewire.global.modal-form.template-pesan')
        </div>
    </div>

</div>
