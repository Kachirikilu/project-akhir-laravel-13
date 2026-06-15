@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $editCall = "editMK($x->id, $typeXString)";
            $deleteCall = "deleteMK($x->id, $isTrashed)";
            $restoreCall = "restoreMK($x->id)";
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $x->kode,
            'typeXString' => 'Kode MK'
        ])

        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                $store.mk?.reset();

                const type = '{{ $typeXString }}';

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
                        '{{ $mk->level_mk ?? '' }}',
                        '{{ $mk->mk ?? '' }}',
                        '{{ $mk->kode_blok ?? '' }}',
                        '{{ $mk->digit_semester ?? '' }}',
                        '{{ $mk->digit_mk ?? '' }}',
                        {{-- '{{ $mk->id_prodi ?? '' }}', --}}
                        {{-- '{{ $mk->kode_pr ?? '' }}', --}}
                        {{-- '{{ $mk->nama_pr ?? '' }}', --}}
                        {{-- '{{ $mk->nama_dp ?? '' }}',
                        '{{ $mk->nama_fk ?? '' }}', --}}
                        '{{ $mk->semester ?? '' }}',
                        '{{ $mk->sks ?? '' }}',
                        '{{ $mk->tipe_sks ?? '' }}',
                        '{{ $mk->wajib ?? '' }}',
                        '{{ $mk->deskripsi ?? '' }}',
                        '{{ $mk->bahan_kajian ?? '' }}',
                    );

                    $flux.modal('mk-modal').show();
            "
                wire:click="{{ $editCall }}"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
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

                        $store.mk?.setDeleteMK(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}'
                        );
                        $flux.modal('mk-delete').show();
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
                            $store.mk?.setDeleteMK(
                            '{{ $x->mk ?? '' }}',
                            '{{ $x->kode ?? '' }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('mk-delete').show();
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

    </flux:menu>
@endif
