<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode Kelas',
    ])

    @if (!$data['isTrashed'])
        <flux:menu.separator />
        <flux:menu.item href="{{ route('jadwal-management', $data['kode']) }}" wire:navigate
            class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900 transition-colors">

            <flux:icon name="rectangle-group" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Lihat Kelas</span>
                <flux:icon wire:loading wire:target="showKelas" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>
    @endif

    <flux:menu.separator />

    <flux:menu.item
        @click="
                $store.rps?.resetShow();
                $store.rps?.setShowRPS(
                    '{{ $data['rps_id'] ?? '' }}',
                    '{{ $data['kode'] ?? '' }}',
                    '{{ $data['pr_id'] ?? '' }}',
                );
                $store.rps?.setColor('text-green-700 dark:text-green-400');
                $flux.modal('rps-detail-modal').show();
                $dispatch('open-show-rps-modal', { id: {{ $data['rps_id'] }}, prId: {{ $data['pr_id'] }} });
            "
        class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900 transition-colors">
        <flux:icon name="eye" class="mr-2 h-4 w-4" />
        <div class="flex justify-between items-center w-full">
            <span>Show RPS</span>
        </div>
    </flux:menu.item>

    <flux:menu.separator />

    {{-- <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
            @dblclick="isWaiting = false" wire:dblclick="printPDFRPS({{ $data['rps_id'] }}, {{ $data['pr_id'] }})"
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

            <flux:icon wire:loading wire:target="printPDFRPS({{ $data['rps_id'] }})" name="arrow-path"
                class="animate-spin h-4 w-4 ml-2" />
        </div> --}}

    <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
        @dblclick="isWaiting = false" wire:dblclick="printPDFRPS({{ $data['rps_id'] }})"
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
        <flux:icon wire:loading wire:target="printPDFRPS()" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
    </div>


    @if (Auth::user()?->admin || Auth::user()?->dosen)

        <flux:menu.separator />

        @if (!$data['isTrashed'])
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.kelas?.reset();
                    $store.kelas?.setEdit(1);

                    $store.kelas?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.kelas?.setValueKelas(
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['kelas'] ?? '' }}',
                        '{{ $data['deskripsi_kelas'] ?? '' }}',
                    );

                    $flux.modal('kelas-modal').show();
                    $dispatch('open-edit-kelas-modal', { id: {{ $data['id'] }} });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Kelas</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                    $store.kelas?.setDeleteKelas(
                        '{{ $data['kelas'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}'
                    );
                    $flux.modal('kelas-delete').show();
                    $dispatch('open-delete-kelas-modal', { id: {{ $data['id'] }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Kelas</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-kelas', { id: {{ $data['id'] }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />
                <div class="flex justify-between items-center w-full">
                    <span>Restore Kelas</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.kelas?.setDeleteKelas(
                        '{{ $data['kelas'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['isTrashed'] }}'
                    );
                    $flux.modal('kelas-delete').show();
                    $dispatch('open-delete-kelas-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen Kelas</span>
                </div>
            </flux:menu.item>
        @endif

    @endif
</div>
