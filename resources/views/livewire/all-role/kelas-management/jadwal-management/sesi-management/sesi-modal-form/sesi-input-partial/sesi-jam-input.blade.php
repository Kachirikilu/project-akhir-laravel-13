@php
    $alpine = $alpine ?? 'sesi';
    $isLivewire = $isLivewire ?? false;
    $isXModal = $isXModal ?? false;
@endphp

<div x-data="{
    sksMenit: {{ $sks * config('rps.faktor_sks', 50) }},
    updateJamBerakhir() {
        let jamMulai = $store.{{ $alpine }}.jam_mulai;
        if (!jamMulai) return;

        let [hours, minutes] = jamMulai.split(':').map(Number);
        let date = new Date();
        date.setHours(hours, minutes + this.sksMenit);

        let h = String(date.getHours()).padStart(2, '0');
        let m = String(date.getMinutes()).padStart(2, '0');

        $store.{{ $alpine }}.jam_berakhir = `${h}:${m}`;
    }
}" class="grid sm:grid-cols-4 gap-1">

    <div class="sm:col-span-2" @change="updateJamBerakhir()" @input="updateJamBerakhir()">
        @include('livewire.global.modal-form.input-form', [
            'alpine' => $alpine,
            'isLivewire' => $isLivewire,
            'isXModal' => $isXModal,
            'nameXString' => 'Jam Mulai',
            'modelString' => 'jam_mulai',
            'iconString' => 'clock',
            'isTime' => 1,
            'message' => $errors->first('jam_mulai'),
        ])
        <div class="text-[9px] sm:text-xs mt-1 text-[var(--secondary-text)]" x-text="'Jam Mulai: ' + $store.{{ $alpine }}.jam_mulai">
        </div>
    </div>

    <div class="sm:col-span-2 mt-1 sm:mt-0">
        @include('livewire.global.modal-form.input-form', [
            'alpine' => $alpine,
            'isLivewire' => $isLivewire,
            'isXModal' => $isXModal,
            'nameXString' => 'Jam Berakhir (Default: +' . $sks * config('rps.faktor_sks', 50) . ' Menit)',
            'modelString' => 'jam_berakhir',
            'iconString' => 'clock',
            'isTime' => 1,
            'isRequired' => 0,
            'message' => $errors->first('jam_berakhir'),
        ])
        <div class="text-[9px] sm:text-xs mt-1 text-[var(--secondary-text)]"
                x-text="'Jam Berakhir: ' + $store.{{ $alpine }}.jam_berakhir">
            </div>
        </div>
    </div>
</div>
