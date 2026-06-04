<div>
    {{-- ⚠️ 1. Error Validation (Paling Atas) --}}
    @include('livewire.global.modal-form.footer.error-validation')

    {{-- 💡 2. Tips (Di bawah Error) --}}
    <div
        class="rounded-xl bg-[var(--second-table-trans)] border-[var(--border-wadah-color)] border p-4 shadow-sm backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="calendar" variant="mini" class="text-[var(--focus-color)]" />
            <span class="font-bold text-slate-900 dark:text-gray-200 text-xs uppercase tracking-wider">Tips</span>
        </div>

        <div class="space-y-3">
            <template x-if="$store.user?.typeModal == 'excel'" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Unggah file <strong class="text-[var(--focus-color)] font-semibold">Excel</strong> dengan format
                        yang
                        sesuai untuk menambahkan banyak pengguna sekaligus.
                    </p>
                </div>
            </template>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Email</strong> diisi dengan format
                    yang sesuai (contoh: <strong
                        class="text-[var(--contrast-main-text)] font-semibold italic">muttaqien.wildan12@gmail.com</strong>).
                </p>
            </div>

            <template x-if="$store.user?.isEdit == 1" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Kosongkan kolom <strong class="text-[var(--focus-color)] font-semibold">Password</strong>
                        untuk mempertahankan password lama.
                    </p>
                </div>
            </template>

            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                    Pastikan <strong class="text-[var(--focus-color)] font-semibold">Program Studi</strong>
                    telah dipilih dengan sesuai.
                </p>
            </div>


            <div class="flex items-start gap-3">
                <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed" x-data="{
                    {{-- Diperbarui agar warna strong di dalam JS Alpine juga mendukung dark mode --}}
                    wrap: (txt) => `<strong class='text-[var(--focus-color)] font-semibold'>${txt}</strong>`,
                
                        get labels() {
                            const mapping = {
                                'admin': ['NIP', 'NITK', 'NIK'],
                                'dosen': ['NIP', 'NIDN', 'NIDK', 'NIK'],
                                'mahasiswa': ['NIM', 'NIK'],
                                'excel': ['NIP', 'NITK', 'NIDN', 'NIDK', 'NIM', 'NIK']
                            };
                            return mapping[$store.user?.typeModal] || [];
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

            <template x-if="$store.user?.typeModal == 'mahasiswa' || $store.user?.typeModal == 'excel'" x-cloak>
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-[var(--focus-color)] shrink-0"></div>
                    <p class="text-sm text-[var(--contrast-second-text)] leading-relaxed">
                        Pastikan <strong class="text-[var(--focus-color)] font-semibold">Tahun Angkatan</strong> minimal
                        <strong class="text-[var(--contrast-main-text)] font-semibold">tahun 1960</strong>.
                    </p>
                </div>
            </template>

            @include('livewire.global.modal-form.template-pesan')

        </div>
    </div>
</div>
