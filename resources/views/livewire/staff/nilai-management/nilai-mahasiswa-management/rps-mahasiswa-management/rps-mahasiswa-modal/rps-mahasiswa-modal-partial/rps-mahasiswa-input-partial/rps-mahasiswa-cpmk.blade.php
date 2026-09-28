<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">
        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium">
            Capaian Nilai Mahasiswa
        </h4>
    </div>

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'editNilaiMahasiswa, updateNilaiMahasiswa',
        ])

        <div class="gap-y-4">
            @forelse($nilai_input['list_cpmk_array'] ?? [] as $index => $item)
                @php
                    $nilaiKon = (float) ($item['nilai_kontribusi'] ?? 0);

                    $predikat = match (true) {
                        $nilaiKon >= 85 => [
                            'huruf' => 'A',
                            'badge' => 'emerald',
                            'teks' => 'Sangat Memuaskan',
                        ],
                        $nilaiKon >= 80 => ['huruf' => 'A-', 'badge' => 'teal', 'teks' => 'Sangat Baik'],
                        $nilaiKon >= 75 => ['huruf' => 'B+', 'badge' => 'cyan', 'teks' => 'Baik Sekali'],
                        $nilaiKon >= 70 => ['huruf' => 'B', 'badge' => 'sky', 'teks' => 'Baik'],
                        $nilaiKon >= 65 => ['huruf' => 'B-', 'badge' => 'blue', 'teks' => 'Cukup Baik'],
                        $nilaiKon >= 60 => ['huruf' => 'C+', 'badge' => 'amber', 'teks' => 'Cukup'],
                        $nilaiKon >= 55 => ['huruf' => 'C', 'badge' => 'yellow', 'teks' => 'Hampir Cukup'],
                        $nilaiKon >= 40 => ['huruf' => 'D', 'badge' => 'orange', 'teks' => 'Kurang'],
                        default => ['huruf' => 'E', 'badge' => 'rose', 'teks' => 'Sangat KUrang'],
                    };
                @endphp

                <div
                    class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col gap-4">

                    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700/60 pb-3">
                        <flux:badge icon="academic-cap" color="violet" size="md" class="font-bold">
                            {{ $item['kode_cpmk'] ?? '---' }}
                        </flux:badge>

                        <flux:badge color="{{ $predikat['badge'] }}" size="sm" variant="pill">
                            Predikat: {{ $predikat['huruf'] }} ({{ $predikat['teks'] }})
                        </flux:badge>
                    </div>

                    {{-- 2. Input Readonly (Skor Murni, Kontribusi, Bobot) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                        {{-- Kiri: Skor Murni --}}
                        <div>
                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'nilai',
                                'isLivewire' => 1,
                                'isReadonly' => 1,
                                'nameXString' => 'Skor Murni',
                                'modelString' => 'list_cpmk_array',
                                'itemsString' => "$index.nilai_murni",
                                'iconString' => 'calculator',
                                'placeholder' => '0',
                                'isRequired' => 0,
                            ])
                        </div>

                        {{-- Tengah: Kontribusi (Skala 100) --}}
                        <div>
                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'nilai',
                                'isLivewire' => 1,
                                'isReadonly' => 1,
                                'nameXString' => 'Kontribusi (Skala 100)',
                                'modelString' => 'list_cpmk_array',
                                'itemsString' => "$index.nilai_kontribusi",
                                'iconString' => 'chart-bar-square',
                                'placeholder' => '0',
                                'isRequired' => 0,
                            ])
                        </div>

                        {{-- Kanan: Bobot CPMK --}}
                        <div>
                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'nilai',
                                'isLivewire' => 1,
                                'isReadonly' => 1,
                                'nameXString' => 'Bobot CPMK',
                                'modelString' => 'list_cpmk_array',
                                'itemsString' => "$index.bobot_cpmk",
                                'iconString' => 'scale',
                                'placeholder' => '0',
                                'isRequired' => 0,
                            ])
                        </div>

                    </div>

                    {{-- 3. Deskripsi & Predikat Nilai Kontribusi --}}
                    {{-- 3. Deskripsi CPL & Predikat Nilai Kontribusi --}}
                    <div
                        class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 bg-white dark:bg-zinc-900/60 p-3 rounded-lg border border-zinc-200/80 dark:border-zinc-800 leading-relaxed space-y-2">
                        <div>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100 block mb-1">Evaluasi
                                Capaian:</span>
                            Capaian nilai mahasiswa pada <strong
                                class="text-indigo-600 dark:text-indigo-400">{{ $item['kode_cpmk'] ?? 'CPMK' }}</strong>
                            memperoleh nilai kontribusi sebesar <strong
                                class="text-emerald-600 dark:text-emerald-400">{{ $item['nilai_kontribusi'] ?? 0 }}</strong>
                            dengan kualifikasi mutu <strong
                                class="uppercase text-indigo-600 dark:text-indigo-400">{{ $predikat['huruf'] }}</strong>
                            ({{ $predikat['teks'] }}).
                        </div>
                        @if (!empty($item['deskripsi']))
                            <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                                <span class="font-semibold text-zinc-800 dark:text-zinc-100 block mb-0.5">Deskripsi
                                    Capaian Pembelajaran:</span>
                                <p class="text-zinc-500 dark:text-zinc-400 italic">
                                    "{{ $item['deskripsi'] }}"
                                </p>
                            </div>
                        @endif
                    </div>
                    <FollowUp label="Mau dilanjutkan dengan penyesuaian simpan/update data nilai ini di Livewire?"
                        query="Tunjukkan cara membuat method updateNilaiMahasiswa() untuk menyimpan perubahan nilai dari $nilai_input ke database." />

                    {{-- 4. Daftar Badge CPL Terkait (Flex Wrap Auto-Down) --}}
                    @if (!empty($item['cpls']))
                        <div class="flex flex-col gap-1.5">
                            <span
                                class="text-[11px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                CPL Terhubung:
                            </span>
                            <div class="flex flex-wrap items-center gap-2">
                                @foreach ($item['cpls'] as $cpl)
                                    <flux:badge icon="document-text" color="sky" size="sm">
                                        {{ $cpl['kode_cpl'] ?? ($cpl['kode'] ?? 'CPL') }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @empty
                <div
                    class="h-48 flex justify-center items-center p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 text-zinc-500 dark:text-zinc-400 text-sm">
                    Belum ada pemetaan CPMK / CPL untuk komponen nilai ini!
                </div>
            @endforelse
        </div>

    </div>

</div>
