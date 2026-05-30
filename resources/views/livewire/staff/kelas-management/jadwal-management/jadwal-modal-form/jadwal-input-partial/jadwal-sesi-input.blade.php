<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Sesi 16 Pertemuan</h4>


    <template x-if="$store.jadwal.isEdit == 1">
        <div class="flex items-center gap-2 p-2">
            <flux:checkbox x-model.number="$store.jadwal.restart_sesi" :value="1"
                label="Restart setiap Sesi ke Kondisi Awal"
                description="Mereset perhitungan setiap sesi perkuliahan yang telah dikustom." class="cursor-pointer" />
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
                <div class="flex justify-between text-xs mt-1">
                    <div class="text-[var(--secondary-text)]"
                        x-text="$store.jadwal.formatHari($store.jadwal.sesi_{{ $i }})">
                    </div>
                    <div x-show="$store.jadwal.isWeekend($store.jadwal.sesi_{{ $i }})"
                        class="text-red-500 text-xs">
                        Hari Libur Akhir Pekan!
                    </div>
                </div>
            </div>
        @endfor

    </div>

</div>
