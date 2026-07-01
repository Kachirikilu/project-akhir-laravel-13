<div>
    @php
        $typeXString = empty($switchTable) ? 'prodi' : $switchTable;

        $typeX2String = $typeXString;
        if ($typeX2String == '' || $typeX2String == 'prodi') {
            $typeX2String = 'Program Studi';
        }
    @endphp


    @include('livewire.global.table.text-copy', [
        'xType' => $kode,
        'typeXString' => 'Kode ' . $typeX2String,
    ])
    @if (Auth::user()?->admin)



        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.prodi?.reset();
                    const type = '{{ $typeXString }}';
                    $store.prodi?.setType(type);
                    $store.prodi?.setEdit(1);

                    const colors = {
                        prodi: 'text-emerald-700 dark:text-emerald-400',
                        departemen: 'text-amber-700 dark:text-amber-400',
                        fakultas: 'text-indigo-700 dark:text-indigo-400'
                    };
                    $store.prodi?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                    $store.prodi?.setValueProdi(
                        '{{ $prodi ?? '' }}',
                        '{{ $strata ?? '' }}',
                        '{{ $dp_id ?? '' }}',
                        '{{ $departemen_dp ?? '' }}',
                        '{{ $fk_id ?? '' }}',
                        '{{ $fakultas_fk ?? '' }}',
                        '{{ $kode_short ?? '' }}',
                        '{{ $kode_dp ?? '' }}',
                        '{{ $kode_fk ?? '' }}'
                    );
                    $flux.modal('prodi-modal').show();
                    $dispatch('open-edit-prodi-modal', { id: {{ $id }}, type: '{{ $typeXString }}' });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit {{ $xNameString ?? 'Data' }}</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Logika Tombol Hapus --}}
            <flux:menu.item
                @click="
                    $store.prodi?.setDeleteProdi(
                        '{{ $prodi ?? '' }}',
                        '{{ $departemen ?? '' }}',
                        '{{ $fakultas ?? '' }}',
                        '{{ $kode ?? '' }}',
                        '{{ $typeXString ?? '' }}'
                    );
                    $flux.modal('prodi-delete').show();
                    $dispatch('open-delete-prodi-modal', { id: {{ $id }}, type: '{{ $typeXString }}' });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus {{ $xNameString ?? 'Data' }}</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-prodi', { id: {{ $id }}, type: '{{ $typeXString }}' })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore {{ $xNameString ?? 'Data' }}</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.prodi?.setDeleteProdi(
                        '{{ $prodi ?? '' }}',
                        '{{ $departemen ?? '' }}',
                        '{{ $fakultas ?? '' }}',
                        '{{ $kode ?? '' }}',
                        '{{ $typeXString ?? '' }}',
                        '{{ $isTrashed }}'
                    );
                    $flux.modal('prodi-delete').show();
                    $dispatch('open-delete-prodi-modal', { id: {{ $id }}, type: '{{ $typeXString }}', isTrash: {{ $isTrashed }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen {{ $xNameString ?? 'Data' }}</span>
                </div>
            </flux:menu.item>
        @endif

    @endif
</div>
