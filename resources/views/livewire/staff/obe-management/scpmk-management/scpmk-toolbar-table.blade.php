@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
        @php
            $isTrashed = $sc->trashed();
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $sc->kode,
            'typeXString' => 'Kode Sub-CPMK',
        ])

        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.scpmk?.reset();
                    $store.scpmk?.setFlyout(false);

                    $store.scpmk?.setEdit(1);

                    $store.scpmk?.setColor('text-fuchsia-700 dark:text-fuchsia-400');

                    $store.scpmk?.setValueSCPMK(
                        '{{ $sc->kode_scpmk ?? '' }}',
                        '{{ $sc->deskripsi ?? '' }}',
                        '{{ $sc->materi ?? '' }}',
                        '{{ $sc->metodologi ?? '' }}',
                        '{{ $sc->indikator ?? '' }}',
                        '{{ $sc->metode ?? '' }}',
                        '{{ $sc->deskripsi_tugas ?? '' }}',
                        '{{ $sc->waktu_tugas ?? '' }}',
                        '{{ $sc->waktu_mandiri ?? '' }}',
                        '{{ $sc->bobot ?? '' }}',
                    );

                    $flux.modal('scpmk-modal').show();
                    $dispatch('open-edit-scpmk-modal', { id: {{ $sc->id }} });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Sub-CPMK</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                    $store.scpmk?.setDeleteSCPMK(
                        '{{ $sc->deskripsi ?? '' }}',
                        '{{ $sc->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('scpmk-delete').show();
                    $dispatch('open-delete-scpmk-modal', { id: {{ $sc->id }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Sub-CPMK</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-scpmk', { id: {{ $sc->id }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore Sub-CPMK</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.scpmk?.setDeleteSCPMK(
                        '{{ $sc->deskripsi ?? '' }}',
                        '{{ $sc->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('scpmk-delete').show();
                    $dispatch('open-delete-scpmk-modal', { id: {{ $sc->id }}, isTrash: {{ $isTrashed }} } );
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen Sub-CPMK</span>
                </div>
            </flux:menu.item>
        @endif
    </flux:menu>
@endif
