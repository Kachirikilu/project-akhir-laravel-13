<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Sesi Kelas</h4>



    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'jadwal',
        'nameXString' => 'Hari Pelaksanaan',
        'modelString' => 'hari_pelaksanaan',
        'xOptions' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        'iconString' => 'calendar',
        'placeholder' => 'Pilih Hari...',
        'message' => $errors->first('hari_pelaksanaan'),
    ])

    <div x-data x-init="$store.jadwal.sks_menit = {{ $sks * 50 }};" class="grid sm:grid-cols-4 gap-1">
        <div class="sm:col-span-2">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'jadwal',
                'nameXString' => 'Jam Mulai',
                'modelString' => 'jam_mulai',
                'iconString' => 'clock',
                'isTime' => 1,
                'message' => $errors->first('jam_mulai'),
            ])
        </div>
        <div class="sm:col-span-2 mt-1 sm:mt-0">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'jadwal',
                'nameXString' => 'Jam Berakhir (Default: +' . $sks * 50 . ' Menit)',
                'modelString' => 'jam_berakhir',
                'iconString' => 'clock',
                'isTime' => 1,
                'isRequired' => 0,
                'message' => $errors->first('jam_berakhir'),
            ])
        </div>
    </div>

    <div class="grid sm:grid-cols-4 gap-1">
        <div class="sm:col-span-2">
            <template x-if="$store.jadwal.isEdit == 0">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Tanggal Mulai',
                    'modelString' => 'tanggal_mulai',
                    'iconString' => 'calendar-days',
                    'isWeek' => 1,
                    'message' => $errors->first('tanggal_mulai'),
                ])
            </template>
            <template x-if="$store.jadwal.isEdit == 1">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Tanggal Mulai',
                    'modelString' => 'tanggal_mulai',
                    'iconString' => 'calendar-days',
                    'isWeek' => 1,
                    'message' => $errors->first('tanggal_mulai'),
                    'isRequired' => 0,
                ])
            </template>
        </div>
        <div class="sm:col-span-2  mt-1 sm:mt-0">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'jadwal',
                // 'isLivewire' => 1,
                'nameXString' => 'Tanggal Berakhir (Default: +6 Bulan)',
                'modelString' => 'tanggal_berakhir',
                'iconString' => 'calendar-days',
                'isWeek' => 1,
                'isRequired' => 0,
                'message' => $errors->first('tanggal_berakhir'),
            ])
        </div>
    </div>

</div>
