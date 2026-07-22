<div>
    <flux:modal name="user-excel-modal" wire:model.live="showUserExcelModal" flyout
        @refresh-data-user.window="if (!$wire.showUserExcelModal) $store.user.reset()"
        class="modal-flux  md:w-screen-2xl max-w-screen-2xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveUserExcel', 'stream' => 'import-progress'])

        <div class="modal-flux-main scrollbar-large">

            @if ($isReady)
                {{-- Loading Overlay --}}
                <div class="modal-flux-header">

                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="cog-6-tooth" color="green" size="lg">
                            <span>Input Pengguna - Excel</span>
                        </flux:badge>
                    </h3>
                </div>

                <div class="modal-flux-body">

                    <form wire:submit.prevent="saveUserExcel" enctype="multipart/form-data" id="userForm">

                        @include('livewire.admin.user-management.user-modal-form.user-excel-input')

                        {{-- 3. Footer/Tombol --}}
                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.admin.user-management.user-modal-form.user-message-form')

                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => $roleType,
                                    'wireLoading' => 'excel_user_file, parseExcelUserFile, procesImportUserExcel',
                                    'wireLoading2' => 'saveUserExcel',
                                    'targetX' => 'addUser, saveUser, editUser, updateUser',
                                    'isLeft' => 1,
                                ])
                            </div>

                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
