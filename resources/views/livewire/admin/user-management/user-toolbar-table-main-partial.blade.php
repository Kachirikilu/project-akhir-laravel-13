@php
    $user = Auth::user();
    $isAdmin = $user?->admin;
    $isSuperUser = ($user->tingkat <= 1);
    $isSameFk     = ($user->tingkat <= 2 && $user->fk_id == ($data['fk_id'] ?? null));
    $isSameDp     = ($user->tingkat <= 3 && $user->dp_id == ($data['dp_id'] ?? null));
    $isSamePr     = ($user->tingkat <= 4 && $user->pr_id == ($data['pr_id'] ?? null));
@endphp

@if ($isAdmin && ($isSuperUser || $isSameFk || $isSameDp || $isSamePr))
    <flux:menu.separator />
    @if (!$data['isTrashed'])
        {{-- Tombol RPS --}}
        @if (Auth::user()?->admin)
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                        $store.user?.resetLite();
                        const type = '{{ strtolower($data['role']) }}';
                        $store.user?.setType(type);
                        $store.user?.setEdit(1);

                        const colors = {
                            admin: 'text-red-700 dark:text-red-400',
                            dosen: 'text-lime-700 dark:text-lime-400',
                            mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                        };
                        $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400');
                        $store.user?.setValueUser(
                            '{{ $data['email'] ?? '' }}',
                            '{{ $data['label_id1'] ?? '' }}',
                            '{{ $data['identity1'] ?? '' }}',
                            '{{ $data['count_rps'] ?? '0' }}',
                            '{{ $data['total_sks'] ?? '0' }}',

                            '{{ $data['rekap_mhs'] ?? '' }}',
                            '{{ $data['ipk_mhs'] ?? '' }}',
                            '{{ $data['mutu_mhs'] ?? '' }}',
                            '{{ $data['angkatan'] ?? 'YYYY' }}',
                        );
                        $flux.modal('user-modal').show();
                        $dispatch('open-edit-user-modal', { id: {{ $data['id'] }}, withRPS: {{ $withRPS ?? 0 }} });
                    "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit {{ $data['role'] }}</span>
                    <flux:icon wire:loading wire:target="open-edit-user-modal" name="arrow-path"
                        class="animate-spin h-4 w-4 ml-2" />
                </div>
            </flux:menu.item>
        @endif
    @endif
@endif
