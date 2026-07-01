@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
        @php
            $isTrashed = $d->trashed();
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $d->kode,
            'typeXString' => 'Kode Tim Dosen',
        ])

        <flux:menu.separator />

        @if (!$isTrashed)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.tim_dosen?.reset();
                    $store.tim_dosen?.setFlyout(false);

                    $store.tim_dosen?.setEdit(1);

                    $store.tim_dosen?.setColor('text-blue-700 dark:text-blue-400');

                    $store.tim_dosen?.setValueTimDosen(
                            '{{ $d->kode_tim_dosen ?? '' }}',
                            '{{ $d->tim ?? '' }}',
                            '{{ $d->pr_id ?? '' }}',
                            '{{ $d->kode_pr ?? '' }}',
                            '{{ $d->prodi ?? '' }}',
                            '{{ $d->pr_rel?->departemen_dp ?? '' }}',
                            '{{ $d->pr_rel?->fakultas_fk ?? '' }}',
                        );

                    $flux.modal('tim-dosen-modal').show();
                    $dispatch('open-edit-tim-dosen-modal', { id: {{ $d->id }} });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Tim Dosen</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                    $store.tim_dosen?.setDeleteTimDosen(
                        '{{ $d->tim ?? '' }}',
                        '{{ $d->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('tim-dosen-delete').show();
                    $dispatch('open-delete-tim-dosen-modal', { id: {{ $d->id }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Tim Dosen</span>
                </div>
            </flux:menu.item>
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-tim-dosen', { id: {{ $d->id }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore Tim Dosen</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                    $store.tim_dosen?.setDeleteTimDosen(
                        '{{ $d->tim ?? '' }}',
                        '{{ $d->kode ?? '' }}',
                        {{ $isTrashed ? 1 : 0 }}
                    );
                    $flux.modal('tim-dosen-delete').show();
                    $dispatch('open-delete-tim-dosen-modal', { id: {{ $d->id }}, isTrash: {{ $isTrashed }} } );
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen Tim Dosen</span>
                </div>
            </flux:menu.item>
        @endif
    </flux:menu>
@endif
