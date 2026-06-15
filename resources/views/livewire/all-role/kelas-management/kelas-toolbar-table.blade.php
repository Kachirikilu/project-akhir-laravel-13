@if (Auth::user())
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();

            $showRPSCall = "showRPS($x->rps_id)";
            $editCall = "editKelas($x->id)";
            $deleteCall = "deleteKelas($x->id, $isTrashed)";
            $restoreCall = "restoreKelas($x->id)";
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $copyText ?? $x->kode,
            'typeXString' => $copyName ?? 'Kode Kelas',
        ])

        <flux:menu.separator />

        <flux:menu.item href="{{ route('jadwal-management', $x->kode) }}" wire:navigate
            class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 transition-colors">

            <flux:icon name="rectangle-group" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show Kelas</span>
                <flux:icon wire:loading wire:target="showKelas" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>


        <flux:menu.separator />


        <flux:menu.item
            @click="
                $store.kelas?.resetShow();
                $store.kelas?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.kelas?.setShowRPS(
                        '{{ $x->rps_id ?? '' }}',
                    );

                    $flux.modal('rps-detail-modal').show();
            "
            wire:click="{{ $showRPSCall }}"
            class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 transition-colors">
            <flux:icon name="eye" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show RPS</span>
                <flux:icon wire:loading wire:target="{{ $showRPSCall }}" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-2" />
            </div>
        </flux:menu.item>



        <flux:menu.separator />

        <div wire:click="printPDFRPS({{ $x->rps_id }})"
            class="px-3 py-2 flex items-center justify-between w-full cursor-pointer
                !text-rose-600 dark:!text-rose-400
                hover:!bg-rose-100 dark:hover:!bg-rose-900/30
                transition-colors select-none rounded-md">
            <div class="flex items-center">
                <flux:icon name="printer" class="mr-2 h-4 w-4" />
                <span>Print PDF RPS</span>
            </div>
            <flux:icon wire:loading wire:target="printPDFRPS({{ $x->rps_id }})" name="arrow-path"
                class="animate-spin h-4 w-4 ml-2" />
        </div>


        @if (Auth::user()?->admin || Auth::user()?->dosen)

            <flux:menu.separator />

            @if (!$isTrashed)
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                        $store.kelas?.reset();
                        $store.kelas?.setEdit(1);

                        $store.kelas?.setColor('text-emerald-700 dark:text-emerald-400');

                        $store.kelas?.setValueKelas(
                            '{{ $x->kode ?? '' }}',
                            '{{ $x->kelas ?? '' }}',
                            '{{ $x->deskripsi_kelas ?? '' }}',

                            '{{ $x->pr_id ?? '' }}',
                            '{{ $x->kode_pr ?? '' }}',
                            '{{ $x->prodi ?? '' }}',
                            '{{ $x->pr_rel?->departemen_dp ?? '' }}',
                            '{{ $x->pr_rel?->fakultas_fk ?? '' }}',

                            '{{ $x->rps_id ?? '' }}',
                            '{{ $x->kode_rps ?? '' }}',
                            '{{ $x->rps_rel?->rps ?? '' }}',
                            '{{ $x->rps_rel?->sks_full ?? '' }}',
                            '{{ $x->wajib_text ?? '' }}',
                            '{{ $x->rps_rel?->draf_full ?? '' }}',
                        );

                        $flux.modal('kelas-modal').show();
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

                            $store.kelas?.setDeleteKelas(
                                '{{ $x->kelas ?? '' }}',
                                '{{ $x->kode ?? '' }}'
                            );
                            $flux.modal('kelas-delete').show();
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
                    class="!cursor-pointer !text-yellow-700 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 transition-colors">
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
                                $store.kelas?.setDeleteKelas(
                                '{{ $x->kelas ?? '' }}',
                                '{{ $x->kode ?? '' }}',
                                '{{ $isTrashed }}'
                            );
                            $flux.modal('kelas-delete').show();
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

        @endif

    </flux:menu>
@endif
