<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode Jadwal',
    ])
    <flux:menu.separator />

    @if (($data['is_my_class'] || Auth::user()->admin || Auth::user()->dosen) && !$data['isTrashed'])
        <flux:menu.item
            href="{{ $data['isJadwalOnly'] ?? null ? route('sesi-jadwal-kelas', [$data['kode_kelas'], $data['kode_jadwal']]) : route('sesi-management', [$data['kode_kelas'], $data['kode_jadwal']]) }}"
            wire:navigate
            class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900 transition-colors">

            <flux:icon name="calendar-days" class="mr-2 h-4 w-4" />
            <div class="flex justify-between items-center w-full">
                <span>Lihat Jadwal Kelas</span>
            </div>
        </flux:menu.item>
    @else
        @if (!empty($data['with_pw']))
            <flux:menu.item
                @click="
                $store.jadwal?.setEdit(0);
                $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                $flux.modal('join-jadwal-modal').show();
                $store.jadwal?.setValueJoinJadwal(
                    '{{ $data['id'] ?? '' }}',
                    '{{ $data['kode'] ?? '' }}',
                    '{{ $data['kode_kelas'] ?? '' }}',
                    '{{ $data['label_extra'] ?? '' }}',
                );
                $dispatch('open-join-jadwal-modal');
            "
                class="!cursor-pointer !text-orange-600 dark:!text-orange-400 hover:!bg-orange-100 dark:hover:!bg-orange-900/30 active:!bg-orange-200 dark:active:!bg-orange-900 transition-colors">

                <flux:icon name="user-plus" class="mr-2 h-4 w-4" />
                <div class="flex justify-between items-center w-full">
                    <span>Join Kelas</span>
                </div>
            </flux:menu.item>
        @else
            <flux:menu.item
                @click="
                    $store.jadwal?.setValueJoinJadwal(
                        '{{ $data['id'] ?? '' }}',
                    );
                    $dispatch('join-jadwal-function', { data: $store.jadwal.getDataJoinJadwal() });
                "
                class="!cursor-pointer !text-orange-600 dark:!text-orange-400 hover:!bg-orange-100 dark:hover:!bg-orange-900/30 active:!bg-orange-200 dark:active:!bg-orange-900 transition-colors">

                <flux:icon name="user-plus" class="mr-2 h-4 w-4" />
                <div class="flex justify-between items-center w-full">
                    <span>Join Kelas</span>
                </div>
            </flux:menu.item>
        @endif

    @endif

    @if ($data['canAccess'])
        <flux:menu.separator />

        @if (!$data['isTrashed'])
            <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
                @dblclick="isWaiting = false" wire:dblclick="exportNilaiExcel({{ $data['id'] }})"
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
                <flux:icon wire:loading wire:target="exportNilaiExcel({{ $data['id'] }})" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>

            <flux:menu.separator />
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.jadwal?.reset();
                    $store.jadwal?.setEdit(1);

                    $store.jadwal?.setColor('text-amber-700 dark:text-amber-400');

                    $store.jadwal?.setValueJadwal(
                        '{{ $data['label_kelas'] ?? '' }}',
                        '{{ $data['kode_wilayah'] ?? '' }}',

                        {{-- '{{ $data['hari_pelaksanaan'] ?? '' }}', --}}
                        {{-- '{{ $data['jam_mulai'] ?? '' }}', --}}
                        {{-- '{{ $data['jam_berakhir'] ?? '' }}', --}}
                        '{{ $data['tanggal_mulai'] ?? '' }}',
                        {{-- '{{ $data['tanggal_berakhir'] ?? '' }}', --}}

                        {{-- '{{ $data['kapasitas'] ?? '' }}', --}}
                        '{{ $data['password'] ?? '' }}',
                    );

                    $flux.modal('jadwal-modal').show();
                    $dispatch('open-edit-jadwal-modal', { id: {{ $data['id'] }}, kelas_id: {{ $data['kelas_id'] }}, kode_kelas: '{{ $data['kode_kelas'] }}', sks: '{{ $data['sks'] }}' });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Jadwal</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                        $store.jadwal?.setDeleteJadwal(
                            '{{ $data['label_extra'] ?? '' }}',
                            '{{ $data['kode'] ?? '' }}'
                        );
                        $flux.modal('jadwal-delete').show();
                        $dispatch('open-delete-jadwal-modal', { id: {{ $data['id'] }} });
                    "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Jadwal</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-jadwal', { id: {{ $data['id'] }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore Jadwal</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.jadwal?.setDeleteJadwal(
                       '{{ $data['label_extra'] ?? '' }}',
                       '{{ $data['kode'] ?? '' }}',
                       '{{ $data['isTrashed'] }}'
                    );
                    $flux.modal('jadwal-delete').show();
                    $dispatch('open-delete-jadwal-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen Jadwal</span>
                </div>
            </flux:menu.item>
        @endif

    @endif

</div>
