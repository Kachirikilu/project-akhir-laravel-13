<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['identity1'],
        'typeXString' => $data['label_id1'] . ' ' . $data['role'],
    ])
    @if (Auth::user()?->admin || Auth::user()?->dosen)

        @if (!$data['isTrashed'])
            <flux:menu.separator />
          
            <flux:menu.item href="{{ route('nilai-mahasiswa-management', ['nim' => $data['identity1']]) }}" navigate
                class="!cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900 transition-colors">

                <flux:icon name="document-text" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Lihat Nilai</span>
                    <flux:icon wire:loading wire:target="showNilai" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
                </div>
            </flux:menu.item>

            <flux:menu.separator />
        @endif

        @if (!$data['isTrashed'])
            <flux:menu.item
                @click="
                $store.rps?.reset();
                const type = '{{ strtolower($data['role']) }}';
                $store.user?.setType(type);
                $store.user?.setEdit(1);
                $store.user?.setColor('text-lime-700 dark:text-lime-400', 'bg-lime-50 dark:bg-lime-950/40');
                $flux.modal('user-rps-modal').show();
                $store.user?.setValueUserRPS(
                    '{{ $data['name'] ?? '' }}',
                    'NIM',
                    '{{ $data['identity1'] ?? '' }}',
                    '{{ $data['prodi'] ?? '' }}',

                    '{{ $data['count_rps'] ?? '' }}',
                    '{{ $data['total_sks'] ?? '' }}',

                    '{{ $data['rekap_mhs'] ?? '' }}',
                    '{{ $data['ipk_mhs'] ?? '' }}',
                    '{{ $data['mutu_mhs'] ?? '' }}',
                    '{{ $data['angkatan'] ?? 'YYYY' }}',
                );
                $dispatch('open-list-rps-user-modal', { id: {{ $data['id'] }}, withRPS: 1, isRPS: 1 });
            "
                class="!cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 active:!bg-cyan-200 dark:hover:!bg-yellow-900/30 active:!bg-cyan-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="eye" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Show RPS</span>
                </div>
            </flux:menu.item>
        @endif

        @if (Auth::user()?->admin)
            <flux:menu.separator />

            @if (!$data['isTrashed'])
                {{-- Tombol RPS --}}

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
                        $store.user?.setValueUserLite(
                            '{{ $data['email'] ?? '' }}'
                        );
                        $flux.modal('user-modal').show();
                        $dispatch('open-edit-user-modal', { id: {{ $data['id'] }} });
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

            @include('livewire.admin.user-management.user-toolbar-table-partial')
        @endif

    @endif
</div>
