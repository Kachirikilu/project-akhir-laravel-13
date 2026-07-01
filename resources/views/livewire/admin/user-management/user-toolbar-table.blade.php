@if (!$isTrashed)
    {{-- Tombol RPS --}}
    @if (Auth::user()?->admin)
        @if (Auth::id() != $id)
            <flux:menu.separator />

            <flux:menu.item
                @click="
                    $store.user?.setDeleteUser(
                        '{{ $label_id1 . ' ' . $identity1 }}',
                        '{{ $role }}'
                    );
                    $flux.modal('user-delete').show();
                    $dispatch('open-delete-user-modal', { id: {{ $id }} });
                "
                class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
                <flux:icon name="trash" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Hapus {{ $role }}</span>
                </div>
            </flux:menu.item>
        @endif
    @endif
@else
    @if (Auth::user()?->admin)
        {{-- Tombol Restore --}}
        <flux:menu.item wire:click="$dispatch('restore-user', { id: {{ $id }} })"
            class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
            <flux:icon name="arrow-path" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Restore {{ $role }}</span>
            </div>
        </flux:menu.item>

        <flux:menu.separator />

        {{-- Tombol Delete Permanent --}}
        <flux:menu.item
            @click="
                $store.user?.setDeleteUser(
                    '{{ $label_id1 . ' ' . $identity1 }}',
                    '{{ $role }}',
                    '{{ $isTrashed }}'
                );
                $flux.modal('user-delete').show();
                $dispatch('open-delete-user-modal', { id: {{ $id }}, isTrash: {{ $isTrashed }} });
                    "
            class="!cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900 transition-colors">
            <flux:icon name="trash" class="mr-2 h-4 w-4" />

            <div class="flex justify-between items-center w-full">
                <span>Hapus Permanen {{ $role }}</span>

            </div>
        </flux:menu.item>
    @endif
@endif
