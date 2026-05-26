<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout
    class="md:w-[95vw] max-w-7xl h-[98vh] !p-8 scrollbar-large">

    @php
        $r = $detailRPSData ?? [];
    @endphp

    <div class="flex items-center justify-between my-9">

        {{-- KIRI: Badge nama & status --}}
        <div class="flex items-center gap-2">
            <flux:button @click="$wire.printPDFRPS($store.{{ $alpineKey ?? 'rps?.rps_id_show' }} ?? null)" icon="printer" size="sm"
                class="!cursor-pointer px-6 !text-rose-600 dark:!text-rose-400 !bg-rose-50 hover:!bg-rose-100 dark:!bg-rose-950/20 dark:hover:!bg-rose-900/30 !border-rose-200/60 dark:!border-rose-800/40 transition-all duration-200">
                <span>Print PDF RPS</span>
                <flux:icon wire:loading wire:target="printPDFRPS" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-3 dark:!text-rose-600" />
            </flux:button>

            @if ($isEdit ?? true)
                @if (!$this->showRPSModal)
                    <flux:button
                        @click="
                    $store.rps?.setEdit(1);
                    $store.rps?.setFlyout(true);
                    $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
                    $store.rps?.setValueRPS(
                        '{{ $r['kode_blok'] ?? null }}',
                        '{{ $r['deskripsi'] ?? null }}',
                        '{{ $r['mk_id'] ?? null }}',
                        '{{ $r['kode_mk'] ?? null }}',
                        '{{ $r['nama_mk'] ?? null }}',
                        '{{ $r['akademik'] ?? null }}',
                        '{{ $r['is_draf'] ?? null }}',
                        '{{ $r['count_scpmk'] ?? null }}',
                        '{{ $r['bobot_uts'] ?? null }}',
                        '{{ $r['bobot_uas'] ?? null }}',
                        '{{ $r['total_bobot'] ?? null }}'
                    );
                    $flux.modal('rps-modal').show();
                    $wire.editRPS($store.rps?.id ?? null)
                "
                        wire:loading.attr="disabled" wire:target="showRPS, editRPS" icon="pencil-square" size="sm"
                       class="!cursor-pointer px-6 !text-yellow-600 dark:!text-yellow-400 !bg-yellow-50 hover:!bg-yellow-100 dark:!bg-yellow-950/20 dark:hover:!bg-yellow-900/30 !border-yellow-200/60 dark:!border-yellow-800/40 transition-all duration-200">
                        <span>Edit RPS</span>
                        <flux:icon wire:loading wire:target="showRPS, editRPS" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-3 dark:!text-yellow-600" />
                    </flux:button>
                @endif
            @endif
        </div>

        {{-- KANAN: Tombol --}}
        <div wire:loading.class="opacity-0" wire:target="showRPS" class="flex items-center gap-2">

            @switch($r['level_mk'] ?? null)
                @case(1)
                    <flux:badge icon="academic-cap" color="emerald" size="lg" class="px-4">{{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @case(2)
                    <flux:badge icon="book-open" color="amber" size="lg" class="px-4">{{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @case(3)
                    <flux:badge icon="building-library" color="indigo" size="lg" class="px-4">
                        {{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @default
                    <flux:badge icon="globe-alt" color="red" size="lg" class="px-4">{{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
            @endswitch
            <flux:badge color="emerald" size="lg" class="px-4">
                {{ $r['nama_rps'] ?? 'Rencana Pembelajaran Semester' }}
            </flux:badge>

            @if (($r['is_draf'] ?? 1) == 0)
                <flux:badge color="green" size="lg" class="px-4">Aktif</flux:badge>
            @else
                <flux:badge color="yellow" size="lg" class="px-4">Draf</flux:badge>
            @endif
        </div>


    </div>


    <div class="p-4 relative bg-white rounded-md border-2">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'showRPS', 'updateRPS'])

        @include('livewire.staff.obe-management.rps-management.rps-show.rps-pdf-table')
    </div>
</flux:modal>
