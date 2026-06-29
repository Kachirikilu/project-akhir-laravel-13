@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        @php
            $isTrashed = $user->trashed();

            // $editCall = "editUser($user->id)";
            // $deleteCall = "deleteUser($user->id, $isTrashed)";
            // $restoreCall = "restoreUser($user->id)";
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $user->identity1,
            'typeXString' => $user->label_id1 . ' ' . $user->role,
        ])


        @if (!$isTrashed)
            {{-- Tombol RPS --}}
            @if (Auth::user()?->admin)
                {{-- Tombol Edit --}}
                <flux:menu.item
                    @click="
                        $store.user?.resetLite();
                        const type = '{{ strtolower($user->role) }}';
                        $store.user?.setType(type);
                        $store.user?.setEdit(1);

                        const colors = {
                            admin: 'text-red-700 dark:text-red-400',
                            dosen: 'text-lime-700 dark:text-lime-400',
                            mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                        };
                        $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');

                        {{-- $store.user?.setValueUser(
                            '{{ addslashes($user->email ?? '') }}',
                            '',
                            '{{ addslashes($user->name ?? '') }}',
                            '{{ addslashes($detail->nip ?? '') }}',
                            '{{ addslashes($detail->nitk ?? '') }}',
                            '{{ addslashes($detail->nidn ?? '') }}',
                            '{{ addslashes($detail->nidk ?? '') }}',
                            '{{ addslashes($detail->nim ?? '') }}',
                            '{{ addslashes($user->nik ?? '') }}',
                            '{{ addslashes($detail->angkatan ?? '') }}',
                            '{{ addslashes($user->status ?? '') }}',
                            '{{ addslashes($user->pr_id ?? '') }}',
                            '{{ addslashes($user->kode_pr ?? '') }}',
                            '{{ addslashes($user->prodi ?? '') }}',
                            '{{ addslashes($detail->pr_rel?->departemen_dp ?? '') }}',
                            '{{ addslashes($detail->pr_rel?->fakultas_fk ?? '') }}',
                            '{{ addslashes($detail->kode_wilayah ?? '') }}',

                            '{{ $user->mahasiswa->count_rps ?? 0 }}',
                            '{{ $user->mahasiswa->total_sks ?? 0 }}',
                            '{{ $user->mahasiswa->rekap_mhs ?? 0.0 }}',
                            '{{ $user->mahasiswa->index_mhs ?? 0.0 }}',
                            '{{ $user->mahasiswa->mutu_mhs ?? 'E' }}',

                            '{{ addslashes($user->gender ?? '') }}',
                            '{{ addslashes($user->agama ?? '') }}',
                            '{{ addslashes($user->tmt_lahir ?? '') }}',
                            '{{ addslashes($user->tanggal_lahir ?? '') }}',

                            '{{ addslashes($user->no_hp_back ?? '') }}',
                        ); --}}
                        $store.user?.setValueUserLite(
                            '{{ $user->email ?? '' }}'
                        );
                        $flux.modal('user-modal').show();
                        $dispatch('open-edit-user-modal', { id: {{ $user->id }} });
                    "
                    {{-- wire:click="$dispatch('open-edit-user-modal', { id: {{ $user->id }} })" --}}
                    {{-- wire:click="userModalActive({{ $user->id }})" --}}
                    {{-- wire:click="$set('userIdModal', {{ $user->id }})" --}} {{-- x-on:click="
                        $dispatch('open-edit-user-modal', { id: {{ $user->id }} });
                        $dispatch('open-modal-alpine'); 
                    " --}} {{-- wire:key="edit-btn-{{ $user->id }}" --}} {{-- x-on:click="$dispatch('open-edit-user-modal', { id: {{ $user->id }} })" --}}
                    {{-- wire:click="$dispatch('open-edit-user-modal', { id: {{ $user->id }} })" --}} {{-- wire:click="{{ $editCall }}" --}}
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Edit {{ $nameXString ?? 'Data' }}</span>
                        <flux:icon wire:loading wire:target="open-edit-user-modal" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                {{-- Logika Tombol Hapus --}}
                @if (Auth::id() != $user->id)
                    <flux:menu.separator />

                    <flux:menu.item
                        @click="
                            $store.user?.setDeleteUser(
                                '{{ $user->label_id1 . ' ' . $user->identity1 }}',
                                '{{ $user->role }}'
                            );
                            $flux.modal('user-delete').show();
                            $dispatch('open-delete-user-modal', { id: {{ $user->id }} });
                        "
                        class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                        <flux:icon name="trash" class="mr-2 h-4 w-4" />

                        <div class="flex justify-between items-center w-full">
                            <span>Hapus {{ $nameXString ?? 'Data' }}</span>
                        </div>
                    </flux:menu.item>
                @endif
            @endif
        @else
            @if (Auth::user()?->admin)
                {{-- Tombol Restore --}}
                <flux:menu.item wire:click="restoreUser({{ $user->id  }})"
                    class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                    <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Restore {{ $nameXString ?? 'Data' }}</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Tombol Delete Permanent --}}
                <flux:menu.item
                    @click="
                        $store.user?.setDeleteUser(
                            '{{ $user->label_id1 . ' ' . $user->identity1 }}',
                            '{{ $user->role }}',
                            '{{ $isTrashed }}'
                        );
                        $flux.modal('user-delete').show();
                        $dispatch('open-delete-user-modal', { id: {{ $user->id }}, isTrash: {{ $isTrashed }} });
                    "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen {{ $nameXString ?? 'Data' }}</span>

                    </div>
                </flux:menu.item>
            @endif
        @endif


    </flux:menu>
@endif
