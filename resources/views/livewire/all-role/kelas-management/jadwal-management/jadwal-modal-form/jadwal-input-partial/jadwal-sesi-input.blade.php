<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Sesi 16 Pertemuan</h4>


    <template x-if="$store.jadwal.isEdit == 1">
        <div class="pt-2">
            <flux:checkbox x-model.number="$store.jadwal.restart_sesi" :value="1"
                label="Restart setiap Sesi ke Kondisi Awal"
                description="Mereset perubahan setiap sesi perkuliahan yang telah dikustom." class="cursor-pointer" />
        </div>

    </template>
    <template x-if="$store.jadwal.isEdit == 1">
        <div class="pb-2">
            <flux:checkbox x-model.number="$store.jadwal.restart_absensi" :value="0"
                label="Hapus semua Absensi Terdata"
                description="Kecuali data absensi Mahasiswa yang masih terhubung di kelas." class="cursor-pointer" />
        </div>
    </template>

    <template x-if="$store.jadwal.isEdit == 0">
        <div class="p-2">
            <flux:checkbox x-model="$store.jadwal.sesi_sent" :value="0"
                label="Aktifkan fitur notifikan WhatsApp"
                description="Notifikasi akan dikirim ke pengguna di Kelas melalui nomor WhatsApp aktif."
                class="cursor-pointer" />
        </div>
    </template>
    <template x-if="$store.jadwal.isEdit == 1">
        <div class="mb-6 space-y-2 select-none" x-init="if (!$store.jadwal.sesi_sent_edit) $store.jadwal.sesi_sent_edit = 'keep'">

            <span class="text-xs sm:text-sm font-semibold text-zinc-700 dark:text-zinc-300 block mb-1">
                Pengaturan Notifikasi WhatsApp untuk Pertemuan 1-16:
            </span>

            <div class="py-1 px-2">
                <flux:checkbox ::checked="$store.jadwal.sesi_sent_edit === 'keep'"
                    x-on:change="$store.jadwal.sesi_sent_edit = 'keep'" label="Biar seperti awal" {{-- description="Meneruskan pengaturan centang default dari masing-masing pertemuan." --}}
                    class="cursor-pointer" />
            </div>

            <div class="py-1 px-2">
                <flux:checkbox ::checked="$store.jadwal.sesi_sent_edit === 'active'"
                    x-on:change="$store.jadwal.sesi_sent_edit = 'active'" label="Aktifkan untuk semua sesi"
                    {{-- description="Mengaktifkan paksa broadcast WA untuk semua pertemuan." --}} class="cursor-pointer" />
            </div>

            <div class="py-1 px-2">
                <flux:checkbox ::checked="$store.jadwal.sesi_sent_edit === 'inactive'"
                    x-on:change="$store.jadwal.sesi_sent_edit = 'inactive'" label="Nonaktifkan untuk semua sesi"
                    {{-- description="Mematikan paksa broadcast WA untuk semua pertemuan." --}} class="cursor-pointer" />
            </div>

        </div>
    </template>

    <div class="grid sm:grid-cols-2 gap-4">



        @for ($i = 1; $i <= 16; $i++)
            <div class="relative">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'isLivewire' => 1,
                    'nameXString' => 'Pertemuan ' . $i,
                    'modelString' => 'sesi_' . $i,
                    'iconString' => 'calendar-days',
                    'isDate' => 1,
                    'message' => $errors->first('sesi_' . $i),
                ])
                <div class="flex justify-between text-[9px] sm:text-xs mt-1">
                    <div class="text-[var(--secondary-text)]"
                        x-text="$store.jadwal.formatHari($store.jadwal.sesi_{{ $i }})">
                    </div>
                    <div x-show="$store.jadwal.isWeekend($store.jadwal.sesi_{{ $i }})"
                        class="text-red-500 text-[9px] sm:text-xs">
                        Hari Libur Akhir Pekan!
                    </div>
                </div>
            </div>
        @endfor

    </div>

</div>
