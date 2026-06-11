 @php
    $editCallCPL = "editCPL($x->id, $x->level_cpl)";
@endphp
    @if (!$isTrashed)

    
        {{-- Tombol Edit --}}
        <flux:menu.item
            @click="
                $store.cpl?.reset();
                $store.cpl?.setFlyout(false);

                const type = '{{ $x->level_cpl }}';

                $store.cpl?.setEdit(1);

                {{-- $store.cpl?.setColor('text-sky-700 dark:text-sky-400'); --}}
                const colors = {
                    '1': 'text-emerald-700 dark:text-emerald-400',
                    '2': 'text-amber-700 dark:text-amber-400',
                    '3': 'text-indigo-700 dark:text-indigo-400',
                    '4': 'text-red-700 dark:text-red-400'
                };
                $store.cpl?.setColor(colors[type] ?? 'text-sky-700 dark:text-sky-400');

                $store.cpl?.setValueCPL(
                    '{{ $x->level_cpl ?? '' }}',
                    '{{ $x->kode_cpl ?? '' }}',
                    '{{ $x->deskripsi ?? '' }}',
                );

                $flux.modal('cpl-modal').show();
            "
            wire:click="{{ $editCallCPL }}"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit {{ $nameXString ?? 'Data' }}</span>
                <flux:icon wire:loading wire:target="{{ $editCallCPL }}" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        <flux:menu.item
            @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}

                        $store.cpl?.setDeleteCPL(
                            '{{ $x->deskripsi ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('cpl-delete').show();
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
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
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
                        $store.cpl?.setDeleteCPL(
                            '{{ $x->deskripsi ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            {{ $isTrashed ? 1 : 0 }}
                        );
                        $flux.modal('cpl-delete').show();
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

