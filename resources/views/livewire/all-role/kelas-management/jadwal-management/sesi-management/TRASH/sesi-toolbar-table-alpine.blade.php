<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    
    @php
        $isTrashed = $s->trashed();
    @endphp
    
    @include('livewire.global.table.text-copy', [
        'xType' => $s->kode,
        'typeXString' => 'Kode Sesi',
    ])

    @if (Auth::user()?->admin || Auth::user()?->dosen)
        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.sesi?.reset();
                    $store.sesi?.setEdit(1);
                    $store.sesi?.setColor('text-amber-700 dark:text-amber-400');

                    $store.sesi?.setValueSesi(
                        '{{ $s->jam_mulai ?? '' }}',
                        '{{ $s->jam_berakhir ?? '' }}',

                        '{{ $s->pertemuan_ke ?? '' }}',
                        '{{ $s->tanggal_fix ?? '' }}',

                        '{{ $s->deskripsi ?? '' }}',
                        '{{ $s->materi ?? '' }}',
                        '{{ $s->metodologi ?? '' }}',
                        '{{ $s->indikator ?? '' }}',
                        '{{ $s->tugas ?? '' }}',
                        '{{ $s->w_tugas ?? '' }}',
                        '{{ $s->w_mandiri ?? '' }}',
                        '{{ $kelas->sks ?? '' }}',

                        '{{ $s->sent ?? '' }}',
                    );
                    $flux.modal('sesi-modal').show();
                    $dispatch('open-edit-sesi-modal', { id: {{ $s->id }}, sks: '{{ $kelas->sks }}' });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Sesi</span>
                </div>
            </flux:menu.item>
        @endif
    @endif
</flux:menu>
