@if (Auth::user())
    <flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

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

        @if (($j->is_my_class || Auth::user()->admin || Auth::user()->dosen) && !$isTrashed)
            <flux:menu.separator />

            <flux:menu.item
                href="{{ $isJadwalMhs ?? null ? route('sesi-mahasiswa', [$x->kode_kelas, $x->kode_jadwal]) : route('sesi-management', [$x->kode_kelas, $x->kode_jadwal]) }}"
                wire:navigate
                class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900 transition-colors">

                <flux:icon name="calendar-days" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Lihat Jadwal Kelas</span>
                    <flux:icon wire:loading wire:target="showJadwal" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
                </div>
            </flux:menu.item>
        @endif

        @if (Auth::user()?->admin || Auth::user()?->dosen)

            <flux:menu.separator />

            <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
                @dblclick="isWaiting = false" wire:dblclick="exportNilaiExcel({{ $x->id }})"
                :class="isWaiting ? 'ring-2 ring-emerald-400' : ''"
                class="px-2 py-1.5 flex items-center justify-between w-full cursor-pointer
                        !text-emerald-600 dark:!text-emerald-400
                        hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 
                        active:!bg-emerald-200 dark:active:!bg-emerald-900
                        transition-all duration-300 select-none rounded-md">

                <div class="flex items-center">
                    <flux:icon name="arrow-down-tray" class="mr-2 h-4 w-4" />
                    <span x-text="isWaiting ? 'Double click...' : 'Export Nilai'"></span>
                </div>

                <flux:icon wire:loading wire:target="exportNilaiExcel({{ $x->id }})" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>

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
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
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
                            '{{ $x->label_extra ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('jadwal-delete').show();
                    "
                    wire:click="{{ $deleteCall }}"
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
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
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
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
                            '{{ $x->label_extra ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('jadwal-delete').show();
                "
                    wire:click="{{ $deleteCall }}"
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
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
