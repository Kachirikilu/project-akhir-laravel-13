<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">
        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium">
            Histori Nilai Mahasiswa
        </h4>
    </div>

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'editNilaiMahasiswa, updateNilaiMahasiswa',
        ])

        <div class="space-y-4">
            @forelse(array_slice($nilai_input['list_nilai_array'] ?? [], $indexStart ?? 0, $indexLenght ?? 16, true) as $index => $item)
                <div
                    class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col">

                    {{-- Header Sub-CPMK, CPMK & Metode --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--focus-color)]">
                            Evaluasi Ke-{{ $index + 1 }}
                        </span>
                        <span class="flex flex-wrap gap-2 sm:gap-3">

                            <flux:badge icon="academic-cap" color="violet" size="sm">
                                {{ $item['kode_cpmk'] ?? '---' }}
                            </flux:badge>

                            <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                {{ $item['kode_scpmk'] ?? '---' }}
                            </flux:badge>

                            {{-- Reusable Metode Badge Component --}}
                            @include('livewire.global.table.badge.metode-badge', [
                                'xValue' => $item['metode'] ?? '',
                            ])
                        </span>
                    </div>

                    {{-- Form Input Nilai & Bobot --}}
                    <div class="grid sm:grid-cols-4 space-y-4 sm:space-y-0 gap-4 mt-3">
                        <div class="sm:col-span-4 w-full">
                            <div class="grid grid-cols-4 gap-3">

                                {{-- Input Nilai --}}
                                <div class="col-span-3 sm:col-span-3">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'nilai',
                                        'isLivewire' => 1,
                                        'nameXString' => 'Nilai',
                                        'modelString' => 'list_nilai_array',
                                        'itemsString' => "$index.nilai",
                                        'floatOnly' => 1,
                                        'maxValue' => 100,
                                        'isReadonly' => !(Auth::user()->admin || Auth::user()->dosen),
                                        'iconString' => 'chart-bar',
                                        'placeholder' => 'Masukkan Nilai...',
                                        'isRequired' => 0,
                                        'message' => $errors->first("list_nilai_array.$index.nilai"),
                                    ])
                                </div>

                                {{-- Display Bobot (Readonly) --}}
                                <div class="col-span-1 sm:col-span-1">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'nilai',
                                        'isLivewire' => 1,
                                        'isReadonly' => 1,
                                        'nameXString' => 'Bobot',
                                        'modelString' => 'list_nilai_array',
                                        'itemsString' => "$index.bobot_text",
                                        'iconString' => 'scale',
                                        'placeholder' => 'Bobot...',
                                        'isRequired' => 0,
                                        'message' => $errors->first("list_nilai_array.$index.bobot"),
                                    ])
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div
                    class="h-48 flex justify-center items-center p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col gap-2 text-zinc-500 dark:text-zinc-400">
                    Belum ada data bobot/komponen nilai RPS untuk mahasiswa ini!
                </div>
            @endforelse
        </div>

    </div>

</div>
