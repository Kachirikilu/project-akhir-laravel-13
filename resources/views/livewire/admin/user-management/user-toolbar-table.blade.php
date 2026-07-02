<flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
    <livewire:admin.user-management.toolbar-user-management 
        lazy 
        :data="[
            'id'        => $user->id,
            'email'     => $user->email,
            'label_id1' => $user->label_id1,
            'identity1' => $user->identity1,
            'role'      => $user->role,
            'isTrashed' => $user->trashed(),
        ]"
        wire:key="toolbar-user-{{ $user->id }}-{{ $key }}" 
    />
</flux:menu>