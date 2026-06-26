    @if (!$isTrashed)
        {{-- Tombol Detail --}}
        <flux:menu.item
            @click="
                $store.rps?.resetShow();
                $store.rps?.setShowRPS(
                    '{{ $x->id ?? '' }}',
                    '{{ $x->kode ?? '' }}',
                    '{{ $x->rps ?? '' }}',
                    '{{ $x->draf ?? '' }}',
                    '{{ $x->level_mk ?? '' }}',
                );
                $store.rps?.setColor('text-green-700 dark:text-green-400');
                $flux.modal('rps-detail-modal').show();
            "
            wire:click="{{ $showCall }}"
            class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900 transition-colors">
            <flux:icon name="eye" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show RPS</span>
                <flux:icon wire:loading wire:target="{{ $showCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        {{-- Tombol PDF --}}
        <div x-data="{ isWaiting: false }"
            @click="isWaiting = true; setTimeout(() => isWaiting = false, 1000)"
            @dblclick="isWaiting = false"
            wire:dblclick="printPDFRPS({{ $x->id }})"
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

            <flux:icon wire:loading wire:target="printPDFRPS({{ $x->id }})" name="arrow-path"
                class="animate-spin h-4 w-4 ml-2" />
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
                    '{{ $x->kode_blok ?? '' }}',
                    '{{ $x->deskripsi_rps ?? '' }}',
                    '{{ $x->mk_id ?? '' }}',
                    '{{ $x->kode_mk ?? '' }}',
                    '{{ $x->mk ?? '' }}',
                    '{{ $x->akademik ?? '' }}',
                    '{{ $x->draf ?? '' }}',
                    '{{ $x->count_scpmk ?? '' }}',
                    '{{ $x->bobot_uts ?? '' }}',
                    '{{ $x->bobot_uas ?? '' }}',
                    '{{ $x->total_bobot ?? '' }}',
                    '{{ $x->kode_semester ?? '' }}'
                );

                $flux.modal('rps-modal').show();
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
                {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                    $store.rps?.setDeleteRPS(
                        '{{ $x->mk ?? '' }}',
                        '{{ $x->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('rps-delete').show();
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
                        $store.rps?.setDeleteRPS(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('rps-delete').show();
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
