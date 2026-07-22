<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode MK',
    ])

    @php
        $user = Auth::user();
    @endphp

    @if ($user->admin || $user->dosen)
        @php
            $isSameFk =
                $user->tingkat <= 2 && ($data['fk_id'] ?? null) == $user->fk_id && ($data['level_mk'] ?? null) == 3;
            $isSameDp =
                $user->tingkat <= 3 && ($data['dp_id'] ?? null) == $user->dp_id && ($data['level_mk'] ?? null) == 2;
            $isSamePr =
                $user->tingkat <= 4 && ($data['pr_id'] ?? null) == $user->pr_id && ($data['level_mk'] ?? null) == 1;
            $canAccess = $user->tingkat <= 1 || $isSameFk || $isSameDp || $isSamePr;
        @endphp
        @if ($canAccess)
            <flux:menu.separator />

            @if (!$data['isTrashed'])
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                $store.mk?.reset();

                const type = {{ $data['level_mk'] }};

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
                        '{{ $data['level_mk'] ?? '' }}',
                        '{{ $data['mk'] ?? '' }}',
                        '{{ $data['kode_blok'] ?? '' }}',
                        '{{ $data['digit_semester'] ?? '' }}',
                        '{{ $data['digit_mk'] ?? '' }}',
                        '{{ $data['semester'] ?? '' }}',
                        {{-- '{{ $data['sks'] ?? '' }}', --}}
                        {{-- '{{ $data['tipe_sks'] ?? '' }}', --}}
                        {{-- '{{ $data['wajib'] ?? '' }}', --}}
                        {{-- '{{ $data['deskripsi'] ?? '' }}', --}}
                        {{-- '{{ $data['bahan_kajian'] ?? '' }}', --}}
                    );
                    $flux.modal('mk-modal').show();
                    $dispatch('open-edit-mk-modal', { id: {{ $data['id'] }}, tingkatan: {{ $data['level_mk'] }} });
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
                            '{{ $data['mk'] ?? '' }}',
                            '{{ $data['kode'] ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
                    $dispatch('open-delete-mk-modal', { id: {{ $data['id'] }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Mata Kuliah</span>
                    </div>
                </flux:menu.item>
            @else
                {{-- Tombol Restore --}}
                <flux:menu.item wire:click="$dispatch('restore-mk', { id: {{ $data['id'] }} })"
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
                            '{{ $data['mk'] ?? '' }}',
                            '{{ $data['kode'] ?? '' }}',
                            '{{ $data['isTrashed'] }}'
                        );
                        $flux.modal('mk-delete').show();
                    $dispatch('open-delete-mk-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen Mata Kuliah</span>

                    </div>
                </flux:menu.item>
            @endif
        @endif
    @endif
</div>
