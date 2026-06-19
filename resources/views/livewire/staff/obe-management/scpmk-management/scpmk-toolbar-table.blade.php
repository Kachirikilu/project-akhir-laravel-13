    @if (!$isTrashed)
        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.scpmk?.reset();
                $store.scpmk?.setFlyout(false);

                $store.scpmk?.setEdit(1);

                $store.scpmk?.setColor('text-fuchsia-700 dark:text-fuchsia-400');

                $store.scpmk?.setValueSCPMK(
                    '{{ $x->kode_scpmk ?? '' }}',
                    '{{ $x->deskripsi ?? '' }}',
                    '{{ $x->materi ?? '' }}',
                    '{{ $x->metodologi ?? '' }}',
                    '{{ $x->indikator ?? '' }}',
                    '{{ $x->metode ?? '' }}',
                    '{{ $x->deskripsi_tugas ?? '' }}',
                    '{{ $x->waktu_tugas ?? '' }}',
                    '{{ $x->waktu_mandiri ?? '' }}',
                    '{{ $x->bobot ?? '' }}',
                );

                $flux.modal('scpmk-modal').show();
            "
            wire:click="{{ $editCall }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-50 dark:hover:!bg-yellow-900/30 transition-colors">
            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit {{ $nameXString ?? 'Data' }}</span>
                <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        <flux:menu.item
            @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}

                        $store.scpmk?.setDeleteSCPMK(
                            '{{ $x->deskripsi ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('scpmk-delete').show();
                "
            wire:click="{{ $deleteCall }}"
            class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
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
                        $store.scpmk?.setDeleteSCPMK(
                            '{{ $x->deskripsi ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('scpmk-delete').show();
                "
            wire:click="{{ $deleteCall }}"
            class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-50 dark:hover:!bg-red-900/30 transition-colors">
            <flux:icon name="trash" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Hapus Permanen {{ $nameXString ?? 'Data' }}</span>
                <flux:icon wire:loading wire:target="{{ $deleteCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>
    @endif

