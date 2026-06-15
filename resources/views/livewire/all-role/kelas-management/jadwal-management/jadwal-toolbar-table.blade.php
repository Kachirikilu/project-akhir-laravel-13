@if (Auth::user())
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editJadwal($x->id)";
            $deleteCall = "deleteJadwal($x->id, $isTrashed)";
            $restoreCall = "restoreJadwal($x->id)";
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $copyText ?? $x->kode,
            'typeXString' => $copyName ?? 'Kode Jadwal',
        ])

        <flux:menu.separator />

        <flux:menu.item
            href="{{ $isJadwalMhs ?? null ? route('sesi-mahasiswa', [$x->kode_kelas, $x->kode_jadwal]) : route('sesi-management', [$x->kode_kelas, $x->kode_jadwal]) }}"
            wire:navigate
            class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 transition-colors">

            <flux:icon name="calendar-days" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show Jadwal</span>
                <flux:icon wire:loading wire:target="showJadwal" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        @if (Auth::user()?->admin || Auth::user()?->dosen)

            <flux:menu.separator />

            @if (!$isTrashed)
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                    $store.jadwal?.reset();
                    $store.jadwal?.setEdit(1);

                    $store.jadwal?.setColor('text-amber-700 dark:text-amber-400');

                    $store.jadwal?.setValueJadwal(
                        '{{ $x->label_kelas ?? '' }}',
                        '{{ $x->kode_wilayah ?? '' }}',

                        '{{ $x->hari_pelaksanaan ?? '' }}',
                        '{{ $x->jam_mulai ?? '' }}',
                        '{{ $x->jam_berakhir ?? '' }}',
                        '{{ $x->tanggal_mulai ?? '' }}',
                        '{{ $x->tanggal_berakhir ?? '' }}',

                        '{{ $x->kapasitas ?? '' }}',
                        '{{ $x->password ?? '' }}',
                    );

                    $flux.modal('jadwal-modal').show();
                "
                    wire:click="{{ $editCall }}"
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit {{ $nameXString ?? 'Data' }}</span>
                        <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item
                    @click="
                        $store.jadwal?.setDeleteJadwal(
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('jadwal-delete').show();
                    "
                    wire:click="{{ $deleteCall }}"
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus {{ $nameXString ?? 'Data' }}</span>
                        <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>
            @else
                {{-- Tombol Restore --}}
                <flux:menu.item wire:click="{{ $restoreCall }}"
                    class="!cursor-pointer !text-yellow-700 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
                    <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Restore {{ $nameXString ?? 'Data' }}</span>
                        <flux:icon wire:loading wire:target="{{ $restoreCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Tombol Delete Permanent --}}
                <flux:menu.item
                    @click="
                            $store.jadwal?.setDeleteJadwal(
                            '{{ $x->kode ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('jadwal-delete').show();
                "
                    wire:click="{{ $deleteCall }}"
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen {{ $nameXString ?? 'Data' }}</span>
                        <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>
            @endif

        @endif

    </flux:menu>
@endif
