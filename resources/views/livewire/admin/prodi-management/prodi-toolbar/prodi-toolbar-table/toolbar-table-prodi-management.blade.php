@php
    $typeXString = empty($data['switchTable']) ? 'prodi' : $data['switchTable'];

    $typeX2String = ucfirst($typeXString);
    if ($typeXString == '' || $typeXString == 'prodi') {
        $typeX2String = 'Program Studi';
    }
    $user = Auth::user();

    // Logika Akses Utama

    if ($user->admin) {
        $isSameFk = $user->tingkat <= 2 && $user->fk_id == ($data['fk_id'] ?? null);
        $isSameDp = $user->tingkat <= 3 && $user->dp_id == ($data['dp_id'] ?? null);
        $isSamePr = $user->tingkat <= 4 && $user->pr_id == ($data['pr_id'] ?? null);

        $canAccess =
            $user->tingkat <= 1 ||
            ($isSameFk && in_array($typeXString, ['fakultas', 'departemen', 'prodi'])) ||
            ($isSameDp && in_array($typeXString, ['departemen', 'prodi'])) ||
            ($isSamePr && $typeXString == 'prodi');

        $isNotMyEntity =
            ($typeXString == 'prodi' && $user->pr_id !== ($data['pr_id'] ?? null)) ||
            ($typeXString == 'departemen' && $user->dp_id !== ($data['dp_id'] ?? null)) ||
            ($typeXString == 'fakultas' && $user->fk_id !== ($data['fk_id'] ?? null));
    } else {
        $canAccess = false;
    }
@endphp

<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'] ?? ($data['kode_dp'] ?? $data['kode_fk']),
        'typeXString' => 'Kode ' . $typeX2String,
    ])
    @if ($canAccess)

        <flux:menu.separator />

        @if (!$data['isTrashed'])
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
                        '{{ $data['prodi'] ?? '' }}',
                        '{{ $data['strata'] ?? '' }}',
                        '{{ $data['dp_id'] ?? '' }}',
                        '{{ $data['departemen_dp'] ?? '' }}',
                        '{{ $data['fk_id'] ?? '' }}',
                        '{{ $data['fakultas_fk'] ?? '' }}',
                        '{{ $data['kode_short'] ?? '' }}',
                        '{{ $data['kode_dp'] ?? '' }}',
                        '{{ $data['kode_fk'] ?? '' }}',
                        '{{ $data['target_sks'] ?? '' }}'
                    );
                    $flux.modal('prodi-modal').show();
                    $dispatch('open-edit-prodi-modal', { id: {{ $data['pr_id'] ?? ($data['dp_id'] ?? $data['fk_id']) }}, type: '{{ $typeXString }}' });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit {{ $typeX2String ?? 'Data' }}</span>
                </div>
            </flux:menu.item>


            {{-- Logika Tombol Hapus --}}
            @if ($isNotMyEntity)
                <flux:menu.separator />
                <flux:menu.item
                    @click="
                    $store.prodi?.setDeleteProdi(
                        '{{ $data['prodi'] ?? '' }}',
                        '{{ $data['departemen'] ?? '' }}',
                        '{{ $data['fakultas'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['typeXString'] ?? '' }}'
                    );
                    $flux.modal('prodi-delete').show();
                    $dispatch('open-delete-prodi-modal', { id: {{ $data['pr_id'] ?? ($data['dp_id'] ?? $data['fk_id']) }}, type: '{{ $typeXString }}' });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus {{ $typeX2String ?? 'Data' }}</span>
                    </div>
                </flux:menu.item>
            @endif
        @else
            {{-- Tombol Restore --}}
            <flux:menu.item
                wire:click="$dispatch('restore-prodi', { id: {{ $data['pr_id'] ?? ($data['dp_id'] ?? $data['fk_id']) }}, type: '{{ $typeXString }}' })"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Restore {{ $typeX2String ?? 'Data' }}</span>
                </div>
            </flux:menu.item>


            {{-- Tombol Delete Permanent --}}
            @if ($isNotMyEntity)
                <flux:menu.separator />
                <flux:menu.item
                    @click="
                    $store.prodi?.setDeleteProdi(
                        '{{ $data['prodi'] ?? '' }}',
                        '{{ $data['departemen'] ?? '' }}',
                        '{{ $data['fakultas'] ?? '' }}',
                        '{{ $data['kode'] ?? '' }}',
                        '{{ $data['typeXString'] ?? '' }}',
                        '{{ $data['isTrashed'] }}'
                    );
                    $flux.modal('prodi-delete').show();
                    $dispatch('open-delete-prodi-modal', { id: {{ $data['id'] }}, type: '{{ $typeXString }}', isTrash: {{ $data['isTrashed'] }} });
                "
                    class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                    <flux:icon name="trash" class="mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span>Hapus Permanen {{ $typeX2String ?? 'Data' }}</span>
                    </div>
                </flux:menu.item>
            @endif
        @endif

    @endif
</div>
