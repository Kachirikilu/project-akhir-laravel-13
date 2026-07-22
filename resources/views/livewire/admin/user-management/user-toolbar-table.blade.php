<flux:menu
    class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    @if (Auth::user()->tingkat > 4)
        <livewire:admin.user-management.toolbar-user-management lazy :data="[
            'id' => $user->id,
            'label_id1' => $user->label_id1,
            'identity1' => $user->identity1,
            'role' => $user->role,
            'isTrashed' => $user->trashed(),
        ]"
            wire:key="toolbar-user-{{ $user->id }}-{{ $key }}" />
    @else
        <livewire:admin.user-management.toolbar-user-management lazy :data="[
            'id' => $user->id,
            'label_id1' => $user->label_id1,
            'identity1' => $user->identity1,
            'role' => $user->role,
            'email' => $user->email,
            'pr_id' => $user->pr_id,
            'dp_id' => $user->dp_id,
            'fk_id' => $user->fk_id,
            'isTrashed' => $user->trashed(),
        ]"
            wire:key="toolbar-user-{{ $user->id }}-{{ $key }}" />
    @endif
</flux:menu>
