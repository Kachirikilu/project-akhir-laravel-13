<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.obe-management.cpmk-management.toolbar-cpmk-management lazy :data="[
            'kode' => $c->kode,
        ]"
            wire:key="toolbar-cpmk-{{ $c->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.cpmk-management.toolbar-cpmk-management lazy :data="[
            'id' => $c->id,
            'kode' => $c->kode,
            'deskripsi_cpl' => $c->deskripsi_cpl,
            'isTrashed' => $c->trashed(),
        ]"
            wire:key="toolbar-cpmk-{{ $c->id }}-{{ $key }}" />
    @endif
</flux:menu>
