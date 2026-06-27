    @if (!$isTrashed)
        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.tim_dosen?.reset();
                $store.tim_dosen?.setFlyout(false);

                $store.tim_dosen?.setEdit(1);

                $store.tim_dosen?.setColor('text-blue-700 dark:text-blue-400');

                $store.tim_dosen?.setValueTimDosen(
                    '{{ $x->kode_tim_dosen ?? '' }}',
                    '{{ $x->tim ?? '' }}',
                    '{{ $x->pr_id ?? '' }}',
                    '{{ $x->kode_pr ?? '' }}',
                    '{{ $x->prodi ?? '' }}',
                    '{{ $x->pr_rel?->departemen_dp ?? '' }}',
                    '{{ $x->pr_rel?->fakultas_fk ?? '' }}',
                );

                $flux.modal('tim-dosen-modal').show();
            "
            wire:click="{{ $editCall }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
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

                        $store.tim_dosen?.setDeleteTimDosen(
                            '{{ $x->tim ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('tim-dosen-delete').show();
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
                        $store.tim_dosen?.setDeleteTimDosen(
                            '{{ $x->tim ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('tim-dosen-delete').show();
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

