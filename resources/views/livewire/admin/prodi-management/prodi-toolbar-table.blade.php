@if (Auth::user()?->admin)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editProdi($x->id, '$typeXString')";
            $deleteCall = "deleteProdi($x->id, '$typeXString', $isTrashed)";
            $restoreCall = "restoreProdi($x->id, '$typeXString')";

            $typeX2String = $typeXString;
            if ($typeX2String == '' || $typeX2String == 'prodi') {
                $typeX2String = 'Program Studi';
            }
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $x->kode,
            'typeXString' => 'Kode ' . $typeX2String
        ])

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
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->strata ?? '' }}',
                            '{{ $x->dp_id ?? '' }}',
                            '{{ $x->departemenDp ?? '' }}',
                            '{{ $x->fk_id ?? '' }}',
                            '{{ $x->fakultasFk ?? '' }}',
                            '{{ $x->kode_short ?? '' }}',
                            '{{ $x->kode_dp ?? '' }}',
                            '{{ $x->kode_fk ?? '' }}'
                        );
                        $flux.modal('prodi-modal').show();
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

            {{-- Logika Tombol Hapus --}}
            <flux:menu.item
                @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                        $store.prodi?.setDeleteProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->departemen ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $typeXString ?? '' }}'
                        );
                        $flux.modal('prodi-delete').show();
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
            <flux:menu.item
                wire:click="{{ $restoreCall }}"
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
                        $store.prodi?.setDeleteProdi(
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->departemen ?? '' }}',
                            '{{ $x->fakultas ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $typeXString ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('prodi-delete').show();
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



    </flux:menu>
@endif
