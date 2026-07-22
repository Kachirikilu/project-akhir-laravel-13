<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:admin.prodi-management.toolbar-prodi-management lazy :data="[
            'id' => $x->id,
            'kode' => $x->kode,
        ]"
            wire:key="toolbar-prodi-{{ $x->id }}-{{ $key }}-{{ $switchTable }}" />
    @else
        @if ($switchTable == '' || $switchTable == 'prodi')
            <livewire:admin.prodi-management.toolbar-prodi-management lazy :data="[
                'pr_id' => $x->id,
                'dp_id' => $x->dp_id,
                'fk_id' => $x->fk_id,
                'strata' => $x->strata,
                'kode' => $x->kode,
                'kode_short' => $x->kode_short,
                'kode_dp' => $x->kode_dp,
                'prodi' => $x->prodi,
                'departemen_dp' => $x->departemen_dp,
                'fakultas_fk' => $x->fakultas_fk,
                'target_sks' => $x->target_sks,
                'switchTable' => '',
                'isTrashed' => $x->trashed(),
            ]"
                wire:key="toolbar-prodi-{{ $x->id }}-{{ $key }}" />
        @elseif ($switchTable == 'departemen')
            <livewire:admin.prodi-management.toolbar-prodi-management lazy :data="[
                'dp_id' => $x->id,
                'fk_id' => $x->fk_id,
                'kode_dp' => $x->kode_dp,
                'kode_fk' => $x->kode_fk,
                'departemen_dp' => $x->departemen_dp,
                'fakultas_fk' => $x->fakultas_fk,
                'switchTable' => 'departemen',
                'isTrashed' => $x->trashed(),
            ]"
                wire:key="toolbar-departemen-{{ $x->id }}-{{ $key }}" />
        @elseif ($switchTable == 'fakultas')
            <livewire:admin.prodi-management.toolbar-prodi-management lazy :data="[
                'fk_id' => $x->id,
                'kode_fk' => $x->kode_fk,
                'fakultas_fk' => $x->fakultas_fk,
                'switchTable' => 'fakultas',
                'isTrashed' => $x->trashed(),
            ]"
                wire:key="toolbar-fakultas-{{ $x->id }}-{{ $key }}" />
        @endif
    @endif
</flux:menu>
