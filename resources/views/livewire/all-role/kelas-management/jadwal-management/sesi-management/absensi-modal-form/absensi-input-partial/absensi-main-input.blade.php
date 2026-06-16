<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Histori Absensi Mahasiswa</h4>
    </div>

    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'editAbsensi, updateAbsensi',
        ])

        <div class="space-y-4">
            @forelse(array_slice($list_absensi_array, $indexStart, $indexLenght, true) as $index => $item)
                <div
                    class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--focus-color)]">
                            Pertemuan Ke-{{ $item['pertemuan_ke'] }}
                        </span>
                        <span class="flex flex-wrap gap-2 sm:gap-3">
                            <x-label-card type="sm">{{ $item['tanggal_carbon'] }}</x-label-card>
                            <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                {{ $item['kode_scpmk'] ?? '---' }}
                            </flux:badge>
                            <flux:badge icon="academic-cap" color="sky" size="sm">
                                {{ $item['kode_cpmk'] ?? '---' }}
                            </flux:badge>
                            @include('livewire.global.table.badge.metode-badge', [
                                'xValue' => $item['metode'],
                            ])
                        </span>
                    </div>

                    <div class="grid sm:grid-cols-4 space-y-4 space-x-2 mt-3">
                        <div class="sm:col-span-2">
                            <div class="grid grid-cols-3 space-x-1">
                                <div class="sm:col-span-2">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'sesi',
                                        'isLivewire' => 1,
                                        'nameXString' => 'Nilai',
                                        'modelString' => 'list_absensi_array',
                                        'floatOnly' => 1,
                                        'maxValue' => 100,
                                        'itemsString' => "$index.nilai",
                                        'iconString' => 'chart-bar',
                                        'placeholder' => 'Masukkan Nilai...',
                                        'isRequired' => 0,
                                        'message' => $errors->first("list_absensi_array.$index.nilai"),
                                    ])
                                </div>
                                <div class="sm:col-span-1">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'sesi',
                                        'isLivewire' => 1,
                                        'readonly' => 1,
                                        'nameXString' => 'Bobot',
                                        'modelString' => 'list_absensi_array',
                                        'itemsString' => "$index.bobot",
                                        'iconString' => 'scale',
                                        'placeholder' => 'Masukkan Bobot...',
                                        'isRequired' => 0,
                                        'message' => $errors->first("list_absensi_array.$index.bobot"),
                                    ])

                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            @include('livewire.global.modal-form.select-form', [
                                'alpine' => 'sesi',
                                'isLivewire' => 1,
                                'nameXString' => 'Status',
                                'modelString' => 'list_absensi_array',
                                'itemsString' => "$index.status",
                                'xOptions' => [
                                    'Hadir',
                                    'Dispensasi',
                                    'Terlambat',
                                    'Izin',
                                    'Sakit',
                                    'Absen',
                                    'Belum Presensi',
                                ],
                                'iconString' => 'tag',
                                'placeholder' => 'Pilih Status...',
                                'isRequired' => 0,
                                'message' => $errors->first("list_absensi_array.$index.status"),
                            ])
                        </div>
                        <div class="sm:col-span-4">
                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'sesi',
                                'isLivewire' => 1,
                                'nameXString' => 'Keterangan',
                                'modelString' => 'list_absensi_array',
                                'itemsString' => "$index.keterangan",
                                'iconString' => 'pencil-square',
                                'placeholder' => 'Masukkan Keterangan',
                                'isRequired' => 0,
                                'message' => $errors->first("list_absensi_array.$index.keterangan"),
                            ])
                        </div>

                    </div>

                </div>
            @empty
                <div
                    class="h-48 flex justify-center items-center p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col gap-2">
                    Tidak ada Sesi Perkuliahan pada Jadwal Kelas ini!
                </div>
            @endforelse
        </div>

    </div>


</div>
