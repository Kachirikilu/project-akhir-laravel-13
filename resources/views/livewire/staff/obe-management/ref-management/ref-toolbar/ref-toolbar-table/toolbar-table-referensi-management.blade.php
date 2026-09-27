<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode Referensi',
    ])
    @php
        $user = Auth::user();
    @endphp
    @if ($user->admin || $user->dosen)
        @if ($user->tingkat < 5)
            <flux:menu.separator />

            @if (!$data['isTrashed'])
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                    $store.ref?.reset();
                    $store.ref?.setFlyout(false);
                    $store.ref?.setEdit(1);
                    $store.ref?.setColor('text-orange-700 dark:text-orange-400');
                    $store.ref?.setValueRef(
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['judul'] ?? '' }}',
                        '{{ $data['penulis'] ?? '' }}',
                        '{{ $data['penerbit'] ?? '' }}',
                        '{{ $data['tahun'] ?? '' }}',
                        '{{ $data['link'] ?? '' }}',
                    );

                    $flux.modal('ref-modal').show();
                    $dispatch('open-edit-ref-modal', { id: {{ $data['id'] }} });
                "
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit Referensi</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item
                    @click="
                    $store.ref?.setDeleteRef(
                        '{{ $data['citation'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        {{ $data['isTrashed'] ? 1 : 0 }}
                    );
                    $flux.modal('ref-delete').show();
                    $dispatch('open-delete-ref-modal', { id: {{ $data['id'] }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Referensi</span>
                    </div>
                </flux:menu.item>
            @else
                {{-- Tombol Restore --}}
                <flux:menu.item wire:click="restoreRef({{ $data['id'] }})"
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Restore Referensi</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Tombol Delete Permanent --}}
                <flux:menu.item
                    @click="
                    $store.ref?.setDeleteRef(
                        '{{ $data['citation'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        {{ $data['isTrashed'] ? 1 : 0 }}
                    );
                    $flux.modal('ref-delete').show();
                    $dispatch('open-delete-ref-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} } );
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen Referensi</span>
                    </div>
                </flux:menu.item>
            @endif
        @endif
    @endif

</div>
