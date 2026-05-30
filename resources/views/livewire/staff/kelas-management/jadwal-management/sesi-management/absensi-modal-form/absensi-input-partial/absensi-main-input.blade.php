<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
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
           @forelse(array_slice($list_absensi_array, $indexStart, $indexEnd, true) as $index => $item)
                <div
                    class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col gap-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--focus-color)]">
                            Pertemuan Ke-{{ $item['pertemuan_ke'] }}
                        </span>
                        <span class="flex gap-3">
                            <x-label-card type="sm">{{ $item['tanggal_carbon'] }}</x-label-card>
                            <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $item['kode_scpmk'] ?? '---' }}</flux:badge>
                            @include('livewire.global.table.badge.metode-badge', [
                                'xValue' => $item['metode'],
                            ])
                        </span>
                    </div>

                    <div class="grid sm:grid-cols-4 gap-1">
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
                                'message' => $errors->first("list_absensi_array.$index.status"),
                            ])
                        </div>
                        <div class="sm:col-span-2">
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
                    Tidak ada sesi perkuliahan pada jadwal ini!
                </div>
            @endforelse
        </div>

    </div>


</div>
