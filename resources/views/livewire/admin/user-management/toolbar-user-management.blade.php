<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['identity1'],
        'typeXString' => $data['label_id1'] . ' ' . $data['role'],
    ])
    @if (Auth::user()?->admin || Auth::user()?->dosen)

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
        @endif

        @include('livewire.admin.user-management.user-toolbar-table-partial')
    @endif
</div>
