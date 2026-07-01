<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $kode,
        'typeXString' => 'Kode MK',
    ])
    
    @if (Auth::user()?->admin || Auth::user()?->dosen)
            <flux:menu.separator />

            @if (!$isTrashed)
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                $store.mk?.reset();

                const type = {{ $level_mk }};

                $store.mk?.setType(type);
                $store.mk?.setEdit(1);

                const colors = {
                    '1': 'text-emerald-700 dark:text-emerald-400',
                    '2': 'text-amber-700 dark:text-amber-400',
                    '3': 'text-indigo-700 dark:text-indigo-400',
                    '4': 'text-red-700 dark:text-red-400'
                };
                $store.mk?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                    $store.mk?.setValueMK(
                        '{{ $level_mk ?? '' }}',
                        '{{ $mk ?? '' }}',
                        '{{ $kode_blok ?? '' }}',
                        '{{ $digit_semester ?? '' }}',
                        '{{ $digit_mk ?? '' }}',
                        '{{ $semester ?? '' }}',
                        '{{ $sks ?? '' }}',
                        '{{ $tipe_sks ?? '' }}',
                        '{{ $wajib ?? '' }}',
                        '{{ $deskripsi ?? '' }}',
                        '{{ $bahan_kajian ?? '' }}',
                    );
                    $flux.modal('mk-modal').show();
                    $dispatch('open-edit-mk-modal', { id: {{ $id }}, tingkatan: {{ $level_mk }} });
            "
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit Mata Kuliah</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item
                    @click="
                        $store.mk?.setDeleteMK(
                            '{{ $mk ?? '' }}',
                            '{{ $kode ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
                    $dispatch('open-delete-mk-modal', { id: {{ $id }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Mata Kuliah</span>
                    </div>
                </flux:menu.item>
            @else
                {{-- Tombol Restore --}}
                <flux:menu.item wire:click="$dispatch('restore-mk', { id: {{ $id }} })"
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Restore Mata Kuliah</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Tombol Delete Permanent --}}
                <flux:menu.item
                    @click="
                    $store.mk?.setDeleteMK(
                            '{{ $mk ?? '' }}',
                            '{{ $kode ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('mk-delete').show();
                    $dispatch('open-delete-mk-modal', { id: {{ $id }}, isTrash: {{ $isTrashed }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen Mata Kuliah</span>

                    </div>
                </flux:menu.item>
            @endif
    @endif
</div>
