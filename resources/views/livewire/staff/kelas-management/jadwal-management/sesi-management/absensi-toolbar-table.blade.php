    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        @php
            $mahasiswaId = $x->mahasiswa?->id ?? 0;
            $editCall = "editAbsensi({$mahasiswaId}, {$jadwal_id})";
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $x->mahasiswa?->nim ?? '---',
            'typeXString' => 'NIM Mahasiswa',
        ])

@if (Auth::user()?->admin || Auth::user()?->dosen)


        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.sesi?.reset();
                $store.sesi?.setEdit(1);
                $store.sesi?.setColor('text-cyan-700 dark:text-cyan-400');

                $store.sesi?.setValueAbsensi(
                    '{{ $x->name ?? '' }}',
                    '{{ $x->mahasiswa->nim ?? '' }}',
                    '{{ round((($x->mhs_poin_absensi ?? 0) / (2 * ($totalSesiKelas ?? 16))) * 100, 2) }}',
                    '{{ $x->mhs_masuk ?? 0 }}',
                    '{{ $x->mhs_dispensasi ?? 0 }}',
                    '{{ $x->mhs_terlambat ?? 0 }}',
                    '{{ $x->mhs_izin ?? 0 }}',
                    '{{ $x->mhs_sakit ?? 0 }}',
                    '{{ $x->mhs_mangkir ?? 0 }}',
                    '{{ $x->mhs_tidak_masuk ?? 0 }}'
                );
                $flux.modal('absensi-modal').show();
            "
            wire:click="{{ $editCall }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">

            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit Absensi</span>

                <flux:icon wire:loading wire:target="editAbsensi" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>
@endif


    </flux:menu>
