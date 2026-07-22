<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['identity1'],
        'typeXString' => $data['label_id1'] . ' ' . $data['role'],
    ])
    @if (Auth::user()?->admin || Auth::user()?->dosen)
        @if (!$data['isTrashed'])
            {{-- Tombol RPS --}}
            <flux:menu.separator />
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
                            'NIP',
                            '{{ $data['identity1'] ?? '' }}',
                            '{{ $data['prodi'] ?? '' }}',
                            '{{ $data['count_rps'] ?? '' }}',
                            '{{ $data['total_sks'] ?? '' }}'
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

        @include('livewire.admin.user-management.user-toolbar-table-main-partial', ['withRPS' => 1])
        @include('livewire.admin.user-management.user-toolbar-table-partial')
    @endif
</div>
