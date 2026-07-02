{{-- <flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:admin.prodi-management.toolbar-prodi-management lazy :id="$x->id" :dp_id="$x->dp_id ?? null" :fk_id="$x->fk_id ?? null"
        :strata="$x->strata ?? null" :kode="$x->kode ?? null" :kode_short="$x->kode_short ?? null" :kode_dp="$x->kode_dp ?? null" :kode_fk="$x->kode_fk ?? null" :prodi="$x->prodi ?? null"
        :departemen="$x->departemen ?? null" :fakultas="$x->fakultas ?? null" :departemen_dp="$x->departemen_dp ?? null" :fakultas_fk="$x->fakultas_fk ?? null" :switchTable="$switchTable" :isTrashed="$x->trashed()"
        wire:key="toolbar-prodi-{{ $x->id }}-{{ $key }}" />
</flux:menu> --}}
<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:admin.prodi-management.toolbar-prodi-management lazy 
        :data="[
            'id' => $x->id,
            'dp_id' => $x->dp_id,
            'fk_id' => $x->fk_id,
            'strata' => $x->strata,
            'kode' => $x->kode,
            'kode_short' => $x->kode_short,
            'kode_dp' => $x->kode_dp,
            'kode_fk' => $x->kode_fk,
            'prodi' => $x->prodi,
            'departemen' => $x->departemen,
            'fakultas' => $x->fakultas,
            'departemen_dp' => $x->departemen_dp,
            'fakultas_fk' => $x->fakultas_fk,
            'switchTable' => $switchTable,
            'isTrashed' => $x->trashed(),
        ]"
        wire:key="toolbar-prodi-{{ $x->id }}-{{ $key }}" />
</flux:menu>
