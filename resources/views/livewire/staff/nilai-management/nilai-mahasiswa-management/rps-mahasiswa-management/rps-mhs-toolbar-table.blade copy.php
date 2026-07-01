<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">

    @php
        $isTrashed = $x->trashed();

        $editCall = "editNilai($x->id)";
        $editRPSCall = "editNilai($x->id)";
        $deleteCall = "deleteNilai($x->id, $isTrashed)";
        $restoreCall = "restoreNilai($x->id)";

    @endphp

   
    <h1>Trash: {{ $isTrashed }}</h1>

    @include('livewire.global.table.text-copy', [
        'xType' => $copyText,
        'typeXString' => $copyName,
    ])


    <template x-if="!item.is_trashed">
        {{-- Tombol RPS --}}
        <flux:menu.item
            @click="
                        $store.nilai?.resetShow();

                            $store.nilai?.setShowRPS(
                                '{{ $n->rps_rel->id ?? '' }}',
                            );

                            $flux.modal('rps-detail-modal').show();
                    "
            wire:click="showRPS({{ $n->rps_rel->id }})"
            class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 active:!bg-cyan-200 dark:hover:!bg-yellow-900/30 active:!bg-cyan-200 dark:active:!bg-yellow-900 transition-colors">
            <flux:icon name="eye" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show RPS</span>
                <flux:icon wire:loading wire:target="{{ $editRPSCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        @if (Auth::user()?->admin || Auth::user()?->dosen)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.nilai?.reset();
                    $store.nilai?.setEdit(1);
                    $store.nilai?.setColor('text-cyan-700 dark:text-cyan-400');
                    $store.nilai?.setValueNilai(
                        '{{ $x->id ?? '' }}',
                        '{{ $mahasiswa->name ?? '' }}',
                        '{{ $mahasiswa->nim ?? '' }}',

                        '{{ $x->kode_rps ?? '' }}',
                        '{{ $x->mk ?? '' }}',
                        '{{ $x->sks ?? '' }}',

                        {{-- '{{ $x->nilai ?? '0.00' }}',
                        '{{ $x->nilai_index ?? '0.00' }}',
                        '{{ $x->nilai_mutu ?? 'E' }}', --}}

                        JSON.parse('{{ json_encode($x->nilai_array ?? []) }}'),
                        JSON.parse('{{ json_encode($x->bobot_rps_array ?? []) }}'),
                        JSON.parse('{{ json_encode($x->kode_cpmk_array ?? []) }}'),
                        JSON.parse('{{ json_encode($x->kode_scpmk_array ?? []) }}'),
                        JSON.parse('{{ json_encode($x->metode_array ?? []) }}'),
                    );
                    $flux.modal('nilai-modal').show();
                "
                {{-- wire:click="{{ $editCall }}" --}}
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit {{ $nameXString ?? 'Data' }}</span>
                    {{-- <flux:icon wire:loading wire:target="{{ $editCall }}" name="arrow-path" class="animate-spin h-4 w-4 ml-2" /> --}}
                </div>
            </flux:menu.item>

            {{-- Logika Tombol Hapus --}}
            <flux:menu.separator />

            <flux:menu.item
                @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                        $store.nilai?.setDeleteNilai(
                            '{{ $mahasiswa->name ?? '' }}',
                            '{{ $mahasiswa->nim ?? '' }}',

                            '{{ $x->kode_rps ?? '' }}',
                            '{{ $x->mk ?? '' }}',
                        );
                        $flux.modal('nilai-delete').show();
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
        @endif
    </template>
    <template x-if="item.is_trashed">
        @if (Auth::user()?->admin || Auth::user()?->dosen)
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
                        $store.nilai?.setDeleteNilai(
                            '{{ $mahasiswa->name ?? '' }}',
                            '{{ $mahasiswa->nim ?? '' }}',

                            '{{ $x->kode_rps ?? '' }}',
                            '{{ $x->mk ?? '' }}',
                            1
                        );
                        $flux.modal('nilai-delete').show();
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
    </template>


</flux:menu>
