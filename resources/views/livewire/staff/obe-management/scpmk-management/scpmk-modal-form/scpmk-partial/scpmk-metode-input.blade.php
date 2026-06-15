<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Metode Sub-CPMK</h4>


    <div class="relative space-y-4">

        <div class="grid sm:grid-cols-4 gap-3 items-start">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'scpmk',
                    'modelString' => 'metode',
                    'xOptions' => [
                        'Teori',
                        'Aktivitas Partisipasif',
                        'Tugas',
                        'Mandiri',
                        'UTS',
                        'UAS',
                        'Evaluasi Awal',
                        'Evaluasi Akhir',
                        'Kuis',
                        'Laporan Akhir',
                        'Hasil Proyek',
                        'Skripsi',
                        'Kerja Praktek',
                        'Responsi',
                        'Logbook',
                        'Portofolio',
                    ],
                    'iconString' => 'tag',
                    'placeholder' => 'Pilih Metode...',
                    'isRequired' => 0,
                    'message' => $errors->first('metode'),
                ])
            </div>
            <div class="sm:col-span-2">


                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'scpmk',
                    'modelString' => 'bobot',
                    'iconString' => 'variable',
                    'floatOnly' => 1,
                    'maxValue' => 100,
                    'placeholder' => 'Masukkan Bobot Sub-CPMK...',
                    'message' => $errors->first('bobot'),
                ])
            </div>
        </div>

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'scpmk',
            'nameXString' => 'Deskripsi Tugas',
            'modelString' => 'deskripsi_tugas',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan Deskripsi Tugas...',
            'message' => $errors->first('deskripsi_tugas'),
            'isRequired' => 0,
        ])

        <div class="grid sm:grid-cols-4 gap-3 items-start">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'scpmk',
                    'nameXString' => 'Waktu Tugas (Menit)',
                    'modelString' => 'waktu_tugas',
                    'numberOnly' => 1,
                    'maxLength' => 3,
                    'iconString' => 'clock',
                    'placeholder' => 'Default: 60 menit/SKS',
                    'isRequired' => 0,
                    'message' => $errors->first('waktu_tugas'),
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'scpmk',
                    'nameXString' => 'Waktu Mandiri (Menit)',
                    'modelString' => 'waktu_mandiri',
                    'numberOnly' => 1,
                    'maxLength' => 3,
                    'iconString' => 'clock',
                    'placeholder' => 'Default: 60 menit/SKS',
                    'isRequired' => 0,
                    'message' => $errors->first('waktu_mandiri'),
                ])
            </div>
        </div>

    </div>

</div>
