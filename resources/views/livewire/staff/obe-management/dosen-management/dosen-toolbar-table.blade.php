<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:staff.obe-management.dosen-management.toolbar-dosen-management lazy :data="[
            'id' => $user->id,
            'label_id1' => 'NIP',
            'identity1' => $user->dosen->nip,
            'role' => 'dosen',
            'isTrashed' => false,
        ]"
            wire:key="toolbar-dosen-{{ $user->id }}-{{ $key }}" />
    @else
        <livewire:staff.obe-management.dosen-management.toolbar-dosen-management lazy :data="[
            'id' => $user->id,
            'label_id1' => 'NIP',
            'identity1' => $user->dosen->nip,
            'role' => 'dosen',
            'email' => $user->email,
            'name' => $user->dosen->name,
            'prodi' => $user->dosen->pr_rel->prodi ?? '',
            'pr_id' => $user->dosen->pr_id,
            'dp_id' => $user->dosen->dp_id,
            'fk_id' => $user->dosen->fk_id,
            'count_rps' => $user->count_rps,
            'total_sks' => $user->total_sks,
            'isTrashed' => $user->trashed(),
        ]"
            wire:key="toolbar-dosen-{{ $user->id }}-{{ $key }}" />
    @endif
</flux:menu>
