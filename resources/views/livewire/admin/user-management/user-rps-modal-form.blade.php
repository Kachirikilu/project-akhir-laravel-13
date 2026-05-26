<flux:modal name="user-rps-modal" wire:model.live="showUserRPSModal" flyout
    class="sm:w-full md:w-3xl max-w-4xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveUser, updateUser">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>


    <div class="flex flex-col h-full">

        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">
                <flux:badge icon="cog-6-tooth" color="lime" size="lg">
                    <span x-text="'Rencana Pembelajaran Semester - Dosen'"></span>
                </flux:badge>
            </h3>
        </div>

        {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            @include('livewire.staff.obe-management.obe-partial.rps-list', [
                'alpine' => 'dosen',
                'rps_items_list' => $dosen_rps_items_list,
                'rps_modal_paginator' => $dosen_rps_modal_paginator,
                'nameXString' => 'Dosen',
                'wireLoading' => 'editUser',
            ])
        </div>

    </div>

</flux:modal>
