<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Sesi Kelas</h4>


    <div x-data x-init="$store.sesi.sks_menit = {{ $sks * 50 }};" class="grid sm:grid-cols-4 gap-1">
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
        <div class="sm:col-span-2 mt-1 sm:mt-0">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'sesi',
                'nameXString' => 'Jam Berakhir (Default: +' . $sks * 50 . ' Menit)',
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
        <div class="flex justify-between text-[9px] sm:text-xs mt-1">
            <div class="text-[var(--secondary-text)]"
                x-text="$store.jadwal.formatHari($store.sesi.tanggal)">
            </div>
            <div x-show="$store.jadwal.isWeekend($store.sesi.tanggal)" class="text-red-500 text-[9px] sm:text-xs">
                Hari Libur Akhir Pekan!
            </div>
        </div>
    </div>

    <div class="p-2"
        x-data="{ 
            get wa_active() { return $store.sesi.sent == 0 }, 
            set wa_active(val) { $store.sesi.sent = val ? 0 : 1 } 
        }">
        <flux:checkbox 
            x-model="wa_active"
            label="Aktifkan Notifikan WhatsApp"
            description="Notifikasi akan dikirim ke pengguna di Kelas melalui nomor WhatsApp aktif." 
            class="cursor-pointer" />
    </div>

</div>
