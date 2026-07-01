@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
        @php
            $isTrashed = $r->trashed();
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $r->kode,
            'typeXString' => 'Kode RPS',
        ])

        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Detail --}}
            <flux:menu.item
                @click="
                $store.rps?.resetShow();
                $store.rps?.setShowRPS(
                    '{{ $r->id ?? '' }}',
                    '{{ $r->kode ?? '' }}',
                    '{{ $r->rps ?? '' }}',
                    '{{ $r->draf ?? '' }}',
                    '{{ $r->level_mk ?? '' }}',
                );
                $store.rps?.setColor('text-green-700 dark:text-green-400');
                $flux.modal('rps-detail-modal').show();
            "
                wire:click="showRPS($r->id)"
                class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900 transition-colors">
                <flux:icon name="eye" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Show RPS</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol PDF --}}
            <div x-data="{ isWaiting: false }" @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
                @dblclick="isWaiting = false" wire:dblclick="printPDFRPS({{ $r->id }})"
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
            </div>

            <flux:menu.separator />

            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.rps?.reset();
                    $store.rps?.setFlyout(false);

                    $store.rps?.setEdit(1);

                    $store.rps?.setColor('text-green-700 dark:text-green-400');

                    $store.rps?.setValueRPS(
                        '{{ $r->kode_blok ?? '' }}',
                        '{{ $r->deskripsi_rps ?? '' }}',
                        '{{ $r->mk_id ?? '' }}',
                        '{{ $r->kode_mk ?? '' }}',
                        '{{ $r->mk ?? '' }}',
                        '{{ $r->akademik ?? '' }}',
                        '{{ $r->draf ?? '' }}',
                        '{{ $r->count_scpmk ?? '' }}',
                        '{{ $r->bobot_uts ?? '' }}',
                        '{{ $r->bobot_uas ?? '' }}',
                        '{{ $r->total_bobot ?? '' }}',
                        '{{ $r->kode_semester ?? '' }}'
                    );

                    $flux.modal('rps-modal').show();
                    $dispatch('open-edit-rps-modal', { id: {{ $r->id }} });
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
                        '{{ $r->mk ?? '' }}',
                        '{{ $r->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('rps-delete').show();
                    $dispatch('open-delete-rps-modal', { id: {{ $r->id }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus RPS</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="restoreRPS({{ $r->id }})"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore RPS</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.rps?.setDeleteRPS(
                        '{{ $r->mk ?? '' }}',
                        '{{ $r->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('rps-delete').show();
                    $dispatch('open-delete-rps-modal', { id: {{ $r->id }}, isTrash: {{ $isTrashed }} } );
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen RPS</span>
                </div>
            </flux:menu.item>
        @endif
    </flux:menu>
@endif
