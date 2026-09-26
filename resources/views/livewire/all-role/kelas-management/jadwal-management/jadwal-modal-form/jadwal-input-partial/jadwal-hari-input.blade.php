<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Sesi Kelas</h4>


    <div>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'jadwal',
            'isLivewire' => 1,
            'nameXString' => 'Hari Pelaksanaan',
            'modelString' => 'hari_pelaksanaan',
            'xOptions' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'iconString' => 'calendar',
            'placeholder' => 'Pilih Hari...',
            'message' => $errors->first('hari_pelaksanaan'),
        ])
        <template x-if="$store.jadwal.hari_pelaksanaan == 'Sabtu' || $store.jadwal.hari_pelaksanaan == 'Minggu'">
            <div class="text-[9px] sm:text-xs mt-1 text-red-500 text-[9px] sm:text-xs">
                Hari Libur Akhir Pekan!
            </div>
        </template>
    </div>


    @include(
        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-input-partial.sesi-jam-input',
        [
            'alpine' => 'jadwal',
            'isLivewire' => 1,
            'isXModal' => 1,
        ]
    )
    <div x-data="{
        init() {
            this.$watch('$store.jadwal.tanggal_mulai', () => $store.jadwal.updateFormattedDates());
            this.$watch('$store.jadwal.tanggal_berakhir', () => $store.jadwal.updateFormattedDates());
            this.$watch('$store.jadwal.hari_pelaksanaan', () => $store.jadwal.updateFormattedDates());
    
            this.$nextTick(() => $store.jadwal.updateFormattedDates());
        }
    }">
        <!-- Grid Input Tanggal Mulai & Tanggal Berakhir -->
        <div class="grid sm:grid-cols-4 gap-1">
            <div class="sm:col-span-2">
                <template x-if="$store.jadwal.isEdit == 0">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'jadwal',
                        'isLivewire' => 1,
                        'isXModal' => 1,
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
                        'isLivewire' => 1,
                        'isXModal' => 1,
                        'nameXString' => 'Tanggal Mulai',
                        'modelString' => 'tanggal_mulai',
                        'iconString' => 'calendar-days',
                        'isWeek' => 1,
                        'message' => $errors->first('tanggal_mulai'),
                        'isRequired' => 0,
                    ])
                </template>
                <div class="text-[9px] sm:text-xs mt-1 text-[var(--secondary-text)]"
                    x-text="'Tanggal Mulai: ' + ($store.jadwal.tanggal_mulai_ddmmyyyy || '-')">
                </div>
            </div>

            <div class="sm:col-span-2 mt-1 sm:mt-0">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'isLivewire' => 1,
                    'isXModal' => 1,
                    'nameXString' => 'Tanggal Berakhir (Default: +6 Bulan)',
                    'modelString' => 'tanggal_berakhir',
                    'iconString' => 'calendar-days',
                    'isWeek' => 1,
                    'isRequired' => 0,
                    'message' => $errors->first('tanggal_berakhir'),
                ])
                <div class="text-[9px] sm:text-xs mt-1 text-[var(--secondary-text)]"
                    x-text="'Tanggal Berakhir: ' + ($store.jadwal.tanggal_berakhir_ddmmyyyy || '-')">
                </div>
            </div>
        </div>


    </div>



</div>
