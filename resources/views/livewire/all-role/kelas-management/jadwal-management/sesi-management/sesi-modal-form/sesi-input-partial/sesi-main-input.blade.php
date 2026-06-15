<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Sesi Kelas</h4>


    <div x-data x-init="$store.sesi.sks_menit = {{ $kelas->sks * 50 }};" class="grid sm:grid-cols-4 gap-1">
        <div class="sm:col-span-2">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'sesi',
                'nameXString' => 'Jam Mulai',
                'modelString' => 'jam_mulai',
                'iconString' => 'clock',
                'isTime' => 1,
                'message' => $errors->first('jam_mulai'),
            ])
        </div>
        <div class="sm:col-span-2">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'sesi',
                'nameXString' => 'Jam Berakhir (Default: +' . $kelas->sks * 50 . ' Menit)',
                'modelString' => 'jam_berakhir',
                'iconString' => 'clock',
                'isTime' => 1,
                'isRequired' => 0,
                'message' => $errors->first('jam_berakhir'),
            ])
        </div>
    </div>

    <div class="relative">
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'sesi',
            'nameAlpine' => 'pertemuan_ke_name',
            'modelString' => 'tanggal',
            'iconString' => 'calendar-days',
            'isDate' => 1,
            'message' => $errors->first('tanggal'),
        ])
        <div class="flex justify-between text-xs mt-1">
            <div class="text-[var(--secondary-text)]"
                x-text="$store.jadwal.formatHari($store.sesi.tanggal)">
            </div>
            <div x-show="$store.jadwal.isWeekend($store.sesi.tanggal)" class="text-red-500 text-xs">
                Hari Libur Akhir Pekan!
            </div>
        </div>
    </div>

</div>
