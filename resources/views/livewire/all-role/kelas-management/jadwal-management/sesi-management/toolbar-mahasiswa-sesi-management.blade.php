<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['nim'] ?? '---',
        'typeXString' => 'NIM Mahasiswa',
    ])


    @if (Auth::user()?->admin || Auth::user()?->dosen)
        @if ($data['kj_id'] ?? false)
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
                $dispatch('open-edit-nilai-absensi-modal', { id: {{ $data['id'] }}, kj_id: {{ $data['kj_id'] }}, count_sesi: {{ $data['count_sesi'] ?? 16 }} });"
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">

                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Nilai & Absensi</span>
                </div>
            </flux:menu.item>
        @else
            <flux:menu.separator />
            <flux:menu.item
                @click="
                    $store.nilai?.reset();
                    $store.nilai?.setEdit(1);
                    $store.nilai?.setColor('text-cyan-700 dark:text-cyan-400');
                    $store.nilai?.setValueNilai(
                        '{{ $data['nilai_id'] ?? '' }}',
                        '{{ $data['name'] ?? '' }}',
                        '{{ $data['nim'] ?? '' }}',

                        '{{ $data['kode_rps'] ?? '' }}',
                        '{{ $data['mk'] ?? '' }}',
                        '{{ $data['sks'] ?? '' }}',

                        JSON.parse('{{ json_encode($data['nilai_array'] ?? []) }}'),
                        JSON.parse('{{ json_encode($data['bobot_rps_array'] ?? []) }}'),
                        JSON.parse('{{ json_encode($data['kode_cpmk_array'] ?? []) }}'),
                        JSON.parse('{{ json_encode($data['kode_scpmk_array'] ?? []) }}'),
                        JSON.parse('{{ json_encode($data['metode_array'] ?? []) }}'),
                    );
                    $flux.modal('rps-mahasiswa-modal').show();
                    $dispatch('open-edit-rps-mahasiswa-modal');
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Nilai</span>
                </div>
            </flux:menu.item>
        @endif
    @endif
</div>
