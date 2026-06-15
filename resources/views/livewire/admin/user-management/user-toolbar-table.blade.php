@if (Auth::user()?->admin)
    <flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

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
                    class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-yellow-900/30 transition-colors">
                    <flux:icon name="eye" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Show RPS</span>
                        <flux:icon wire:loading wire:target="{{ $editRPSCall }}" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />
            @endif

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
                        '{{ $user->mahasiswa->rekap_mhs ?? 0.0 }}',
                        '{{ $user->mahasiswa->index_mhs ?? 0.0 }}',
                        '{{ $user->mahasiswa->akreditas_mhs ?? 'E' }}',
                    );
                    $flux.modal('user-modal').show();
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

            {{-- Logika Tombol Hapus --}}
            @if (Auth::id() != $x->id || ($nameXString ?? '') != 'Pengguna')
                <flux:menu.separator />

                <flux:menu.item
                    @click="
                    {{-- const type = '{{ $x->role ? strtolower($x->role) : $typeXString }}'; --}}
                        $store.user?.setDeleteUser(
                            '{{ $x->email ?? '' }}',
                            '{{ $x->role }}'
                        );
                        $flux.modal('user-delete').show();
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
            @endif {{-- Ini penutup @if logika hapus --}}
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
                        $store.user?.setDeleteUser(
                            '{{ $x->email ?? '' }}',
                            '{{ $x->role }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('user-delete').show();
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
