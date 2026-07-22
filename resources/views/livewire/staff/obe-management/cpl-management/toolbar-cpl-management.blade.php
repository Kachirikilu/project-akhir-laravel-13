@php
    $user = Auth::user();
    if ($user->admin || $user->dosen) {
        $isFkOwner =
            $user->tingkat <= 2 && ($data['fk_id'] ?? null) == $user->fk_id && ($data['level_cpl'] ?? null) == 3;
        $isDpOwner =
            $user->tingkat <= 3 && ($data['dp_id'] ?? null) == $user->dp_id && ($data['level_cpl'] ?? null) == 2;
        $isPrOwner =
            $user->tingkat <= 4 && ($data['pr_id'] ?? null) == $user->pr_id && ($data['level_cpl'] ?? null) == 1;
        $canAccess = $user->tingkat <= 1 || $isFkOwner || $isDpOwner || $isPrOwner;
    } else {
        $canAccess = false;
    }
@endphp
<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode CPL',
    ])


    @if (!$data['isTrashed'])
    <flux:menu.separator />

        {{-- Tombol Detail --}}
        <flux:menu.item
            @click="
                    $store.cpl?.reset();
                    const type = '{{ $data['level_cpl'] }}';
                    $store.cpl?.setEdit(1);
                    const colors = {
                        '1': 'text-emerald-700 dark:text-emerald-400',
                        '2': 'text-amber-700 dark:text-amber-400',
                        '3': 'text-indigo-700 dark:text-indigo-400',
                        '4': 'text-red-700 dark:text-red-400'
                    };
                    $store.cpl?.setColor(colors[type] ?? 'text-sky-700 dark:text-sky-400');
                        $store.cpl?.setValueCPLRPS (
                            '{{ $data['kode'] ?? '' }}',
                            '{{ $data['rekap_cpl_pr'] ?? 0 }}',
                            '{{ $data['index_cpl_pr'] ?? 0 }}',
                            '{{ $data['mutu_cpl_pr'] ?? 'E' }}',
                        );
                    $flux.modal('cpl-rps-modal').show();
                    $dispatch('open-list-rps-cpl-modal', { id: {{ $data['id'] }}, tingkatan: {{ $data['level_cpl'] }}, isRPS: 1 });
                "
            {{-- wire:click="editCPL({{ $id }}, {{ $level_cpl }}, 1)" --}} color="emerald"
            class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900 transition-colors">
            <flux:icon name="eye" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Show RPS</span>
            </div>
        </flux:menu.item>
        @if ($canAccess)
            <flux:menu.separator />

            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.cpl?.reset();
                    $store.cpl?.setFlyout(false);

                    const type = '{{ $data['level_cpl'] }}';

                    $store.cpl?.setEdit(1);

                    {{-- $store.cpl?.setColor('text-sky-700 dark:text-sky-400'); --}}
                    const colors = {
                        '1': 'text-emerald-700 dark:text-emerald-400',
                        '2': 'text-amber-700 dark:text-amber-400',
                        '3': 'text-indigo-700 dark:text-indigo-400',
                        '4': 'text-red-700 dark:text-red-400'
                    };
                    $store.cpl?.setColor(colors[type] ?? 'text-sky-700 dark:text-sky-400');

                    $store.cpl?.setValueCPL(
                        '{{ $data['level_cpl'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['deskripsi'] ?? '' }}',
                    );

                    $flux.modal('cpl-modal').show();
                    $dispatch('open-edit-cpl-modal', { id: {{ $data['id'] }}, tingkatan: {{ $data['level_cpl'] }} });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit CPL</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item
                @click="
                    $store.cpl?.setDeleteCPL(
                        '{{ $data['mk'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        {{ $data['isTrashed'] ? 1 : 0 }}
                    );
                    $flux.modal('cpl-delete').show();
                    $dispatch('open-delete-cpl-modal', { id: {{ $data['id'] }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus CPL</span>
                </div>
            </flux:menu.item>
        @endif
    @else
        @if ($canAccess)
    <flux:menu.separator />

            {{-- Tombol Restore --}}
            <flux:menu.item wire:click="$dispatch('restore-cpl', { id: {{ $data['id'] }} })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore CPL</span>
                </div>
            </flux:menu.item>

            <flux:menu.separator />

            {{-- Tombol Delete Permanent --}}
            <flux:menu.item
                @click="
                        $store.cpl?.setDeleteCPL(
                            '{{ $data['deskripsi'] ?? '' }}',
                            '{{ $data['kode'] ?? '' }}',
                            {{ $data['isTrashed'] ? 1 : 0 }}
                        );
                        $flux.modal('cpl-delete').show();
                    $dispatch('open-delete-cpl-modal', { id: {{ $data['id'] }}, isTrash: {{ $data['isTrashed'] }} } );
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus Permanen CPL</span>
                </div>
            </flux:menu.item>
        @endif
    @endif

</div>
