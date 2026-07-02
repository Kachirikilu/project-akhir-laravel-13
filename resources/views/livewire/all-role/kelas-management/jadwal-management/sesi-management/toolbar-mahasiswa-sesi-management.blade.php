<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['nim'] ?? '---',
        'typeXString' => 'NIM Mahasiswa',
    ])

    @if (Auth::user()?->admin || Auth::user()?->dosen)
        <flux:menu.item
            @click="
                $store.sesi?.reset();
                $store.sesi?.setEdit(1);
                $store.sesi?.setColor('text-cyan-700 dark:text-cyan-400');

                $store.sesi?.setValueAbsensi(
                    '{{ $data['name'] ?? '' }}',
                    '{{ $data['nim'] ?? '' }}',
                );
                $flux.modal('nilai-absensi-modal').show();
                $dispatch('open-edit-nilai-absensi-modal', { id: {{ $data['id'] }}, jadwal_id: {{ $data['jadwal_id'] }}, count_sesi: {{ $data['count_sesi'] ?? 16 }} });"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">

            <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Edit Nilai & Absensi</span>
            </div>
        </flux:menu.item>
    @endif
</div>
