<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout
    class="md:w-[95vw] max-w-7xl h-[98vh] !p-8 scrollbar-large">

    @php
        $r = $detailRPSData ?? [];
    @endphp

    <flux:button @click="$wire.printPDFRPS($store.rps?.id ?? null)"
        class="mr-2 mb-8 cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 border border-emerald-200">

        <flux:icon name="printer" class="mr-2 h-4 w-4" />

        <div class="flex justify-between items-center w-full">
            <span>Print PDF RPS
                 {{-- - <span x-text="$store.rps?.nama_rps"></span> --}}
            </span>
            <flux:icon wire:loading wire:target="printPDFRPS" name="arrow-path"
                class="animate-spin h-4 w-4 ml-3 dark:!text-emerald-600" />
        </div>
    </flux:button>

    
    <flux:button
        @click="
            $store.rps?.setEdit(1);
            $store.rps?.setFlyout(true);
            $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
            
            {{-- $store.rps?.setValueRPS(
                '{{ $r['kode_blok'] ?? null }}' ?? $store.rps?.kode_blok ?? null,
                '{{ $r['deskripsi'] ?? null }}' ?? $store.rps?.deskripsi ?? null,
                '{{ $r['mk_id'] ?? null }}' ?? $store.rps?.mk_id ?? null,
                '{{ $r['kode_mk'] ?? null }}' ?? $store.rps?.kode_mk ?? null,
                '{{ $r['nama_mk'] ?? null }}' ?? $store.rps?.nama_mk_search ?? null,
                '{{ $r['akademik'] ?? null }}' ?? $store.rps?.akademik ?? null,
                '{{ $r['is_draf'] ?? null }}' ?? $store.rps?.is_draf ?? null,
                '{{ $r['count_scpmk'] ?? null }}' ?? $store.rps?.count_scpmk ?? null,
                '{{ $r['bobot_uts'] ?? null }}' ?? $store.rps?.bobot_uts ?? null,
                '{{ $r['bobot_uas'] ?? null }}' ?? $store.rps?.bobot_uas ?? null,
                '{{ $r['total_bobot'] ?? null }}' ?? $store.rps?.total_bobot ?? null
            ); --}}
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
        wire:loading.attr="disabled"
        wire:target="showRPS, editRPS"
        class="mb-8 cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 border border-yellow-200">

        <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

        <div class="flex justify-between items-center w-full">
            <span>Edit RPS</span></span>
            <flux:icon wire:loading wire:target="showRPS, editRPS" name="arrow-path"
                class="animate-spin h-4 w-4 ml-3 dark:!text-yellow-600" />
        </div>
    </flux:button>

    <div class="p-4 relative bg-white rounded-md border-2">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'showRPS', 'updateRPS'])

        @include('livewire.staff.obe-management.rps-management.rps-show.rps-pdf-table')
    </div>
</flux:modal>
