<div>
    @if ($isMK ?? false)
        @include('livewire.global.table.text-copy', [
            'xType' => $data['kode_mk'],
            'typeXString' => 'Kode MK',
        ])
    @else
        @include('livewire.global.table.text-copy', [
            'xType' => $data['kode'],
            'typeXString' => 'Kode RPS',
        ])
    @endif
    @if (Auth::user()?->admin || Auth::user()?->dosen)

        @if (!$data['isTrashed'] && $data['noData'])
            <flux:menu.separator />

            <flux:menu.item href="{{ route('rps-capaian-mahasiswa-management', ['kode_rps' => $data['kode']]) }}"
                navigate
                class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900 transition-colors">
                <flux:icon name="document-text" class="mr-2 h-4 w-4" />
                <div class="flex justify-between items-center w-full">
                    <span>Lihat Nilai</span>
                </div>
            </flux:menu.item>
        @endif

        <flux:menu.separator />

        @if (!$data['isTrashed'])
            {{-- Tombol Detail --}}
            <flux:menu.item
                @click="
                    $store.rps?.resetShow();
                    $store.rps?.setShowRPS(
                        '{{ $data['id'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                    );
                    $store.rps?.setColor('text-green-700 dark:text-green-400');
                    $flux.modal('rps-detail-modal').show();
                    $dispatch('open-show-rps-modal', { id: {{ $data['id'] }} });
                "
                {{-- wire:click="showRPS({{ $id }})" --}}
                class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900 transition-colors">
                <flux:icon name="eye" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Show RPS</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
                @dblclick="isWaiting = false" wire:dblclick="printPDFRPS({{ $data['id'] }})"
                :class="isWaiting ? 'ring-2 ring-rose-400' : ''"
                class="px-2 py-1.5 flex items-center justify-between w-full cursor-pointer
                    !text-rose-600 dark:!text-rose-400
                    hover:!bg-rose-100 dark:hover:!bg-rose-900/30 
                    active:!bg-rose-200 dark:active:!bg-rose-900
                    transition-all duration-300 select-none rounded-md">
                <div class="flex items-center">
                    <flux:icon name="arrow-down-tray" class="mr-2 h-4 w-4" />
                    <span x-text="isWaiting ? 'Double click...' : 'Export RPS'"></span>
                </div>
                <flux:icon wire:loading wire:target="printPDFRPS()" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>

            @if ($data['noData'])
                <flux:menu.separator />
                <flux:menu.item wire:navigate
                    href="{{ route('obe-management', ['switchTable' => 'rps']) . '?search=' . urlencode($data['kode']) }}"
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">

                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit RPS di OBE</span>
                    </div>
                </flux:menu.item>
            @else
                <flux:menu.separator />

                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                        $store.rps?.reset();
                        $store.rps?.setFlyout(false);

                        $store.rps?.setEdit(1);

                        $store.rps?.setColor('text-green-700 dark:text-green-400');

                        $store.rps?.setValueRPS(
                            '{{ $data['digit_akademik'] ?? '' }}',
                            '{{ $data['deskripsi_rps'] ?? '' }}',
                            '{{ $data['mk_id'] ?? '' }}',
                            '{{ $data['kode_mk'] ?? '' }}',
                            '{{ $data['mk'] ?? '' }}',
                            '{{ $data['akademik'] ?? '' }}',
                            '{{ $data['draf'] ?? '' }}',
                            '{{ $data['count_scpmk'] ?? '' }}',
                            '{{ $data['bobot_uts'] ?? '' }}',
                            '{{ $data['bobot_uas'] ?? '' }}',
                            '{{ $data['total_bobot'] ?? '' }}',
                            '{{ $data['kode_semester'] ?? '' }}'
                        );

                        $flux.modal('rps-modal').show();
                        $dispatch('open-edit-rps-modal', { id: {{ $data['id'] }} });
                    "
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit RPS</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item
                    @click="
                        $store.rps?.setDeleteRPS(
                            '{{ $data['mk'] ?? '' }}',
                            '{{ $data['kode'] ?? '' }}',
                            {{ $data['isTrashed'] ? 1 : 0 }}
                        );
                        $flux.modal('rps-delete').show();
                        $dispatch('open-delete-rps-modal', { id: {{ $data['id'] }} });
                    "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus RPS</span>
                    </div>
                </flux:menu.item>
            @endif
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-rps', { id: {{ $data['id'] }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore RPS</span>
                </div>
            </flux:menu.item>

            @if (!$data['noData'])
                <flux:menu.separator />

                {{-- Tombol Delete Permanent --}}
                <flux:menu.item
                    @click="
                    $store.rps?.setDeleteRPS(
                        '{{ $data['mk'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        {{ $data['isTrashed'] ? 1 : 0 }}
                    );
                    $flux.modal('rps-delete').show();
                    $dispatch('open-delete-rps-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} } );
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen RPS</span>
                    </div>
                </flux:menu.item>
            @endif
        @endif
    @endif
</div>
