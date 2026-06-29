@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        @php
            $isTrashed = $x->trashed();

            $rpsCall = $withRPS ?? false;

            $editCall = "editUser($x->id, $rpsCall)";
            $editRPSCall = "editUser($x->id, $rpsCall, 1)";
            $deleteCall = "deleteUser($x->id, $isTrashed)";
            $restoreCall = "restoreUser($x->id)";

            $typeXString = '';
            if ($x->role == 'Mahasiswa') {
                $typeXString = 'NIM';
            } else {
                $typeXString = 'NIP';
            }
        @endphp


        @include('livewire.global.table.text-copy', [
            'xType' => $x->identity1,
            'typeXString' => $typeXString . ' ' . $x->role,
        ])


        @if (!$isTrashed)
            {{-- Tombol RPS --}}
            @if ($x->role == 'Dosen' && ($withRPS ?? false))
                <flux:menu.item
                    @click="
                            $store.user?.reset();

                            const type = '{{ strtolower($x->role) }}';

                            {{-- $store.user?.setType(type); --}}
                            {{-- $store.user?.setEdit(1); --}}

                            $store.user?.setColor('text-lime-700 dark:text-lime-400');
                            $flux.modal('user-rps-modal').show();
                        "
                    wire:click="{{ $editRPSCall }}"
                    class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 active:!bg-cyan-200 dark:hover:!bg-yellow-900/30 active:!bg-cyan-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="eye" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Show RPS</span>
                        <flux:icon wire:loading wire:target="{{ $editRPSCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />
            @endif

            @if (Auth::user()?->admin)
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                $store.user?.reset();

                const type = '{{ strtolower($x->role) }}';

                $store.user?.setType(type);
                $store.user?.setEdit(1);

                const colors = {
                    admin: 'text-red-700 dark:text-red-400',
                    dosen: 'text-lime-700 dark:text-lime-400',
                    mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                };
                $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                $store.user?.setValueUser(
                    '{{ $x->email ?? '' }}',
                    '',
                    '{{ $x->name ?? '' }}',
                    '{{ $detail->nip ?? '' }}',
                    '{{ $detail->nitk ?? '' }}',
                    '{{ $detail->nidn ?? '' }}',
                    '{{ $detail->nidk ?? '' }}',
                    '{{ $detail->nim ?? '' }}',
                    '{{ $x->nik ?? '' }}',
                    '{{ $detail->angkatan ?? '' }}',
                    '{{ $x->status ?? '' }}',
                    '{{ $x->pr_id ?? '' }}',
                    '{{ $x->kode_pr ?? '' }}',
                    '{{ $x->prodi ?? '' }}',
                    '{{ $detail->pr_rel?->departemen_dp ?? '' }}',
                    '{{ $detail->pr_rel?->fakultas_fk ?? '' }}',
                    '{{ $detail->kode_wilayah ?? '' }}',

                    '{{ $x->mahasiswa->count_rps ?? 0 }}',
                    '{{ $x->mahasiswa->total_sks ?? 0 }}',
                    '{{ $x->mahasiswa->rekap_mhs ?? 0.00 }}',
                    '{{ $x->mahasiswa->index_mhs ?? 0.00 }}',
                    '{{ $x->mahasiswa->mutu_mhs ?? 'E' }}',

                    '{{ $x->gender ?? '' }}',
                    '{{ $x->agama ?? '' }}',
                    '{{ $x->tmt_lahir ?? '' }}',
                    '{{ $x->tanggal_lahir ?? '' }}',

                    '{{ $x->no_hp_back ?? '' }}',
                );
                $flux.modal('user-modal').show();
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

                {{-- Logika Tombol Hapus --}}
                @if (Auth::id() != $x->id)
                    <flux:menu.separator />

                    <flux:menu.item
                        @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                        $store.user?.setDeleteUser(
                            '{{ $x->label_id1.' '.$x->identity1 }}',
                            '{{ $x->role }}'
                        );
                        $flux.modal('user-delete').show();
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
            @endif
        @else
            @if (Auth::user()?->admin)
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
                        $store.user?.setDeleteUser(
                            '{{ $x->label_id1.' '.$x->identity1 }}',
                            '{{ $x->role }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('user-delete').show();
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
        @endif


    </flux:menu>
@endif
