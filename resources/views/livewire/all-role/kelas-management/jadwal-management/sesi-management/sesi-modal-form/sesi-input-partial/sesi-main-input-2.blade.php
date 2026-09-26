<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Sesi Kelas</h4>


    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-input-partial.sesi-jam-input')

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
            <div class="text-[var(--secondary-text)]" x-text="$store.jadwal.formatHari($store.sesi.tanggal)">
            </div>
            <div x-show="$store.jadwal.isWeekend($store.sesi.tanggal)" class="text-red-500 text-[9px] sm:text-xs">
                Hari Libur Akhir Pekan!
            </div>
        </div>
    </div>

    <div class="p-2" x-data="{
        get wa_active() { return $store.sesi.sent == 0 },
        set wa_active(val) { $store.sesi.sent = val ? 0 : 1 }
    }">
        <flux:checkbox x-model="wa_active" label="Aktifkan Notifikan WhatsApp"
            description="Notifikasi akan dikirim ke pengguna di Kelas melalui nomor WhatsApp aktif."
            class="cursor-pointer" />
    </div>

</div>
