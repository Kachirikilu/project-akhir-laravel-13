<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showUserModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showUserModal
    ">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    @if ($withRPS ?? false)
        <template x-if="$store.user.isEdit" x-cloak>
            @include('livewire.global.modal-form.paginate.tab-form', [
                'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Identitas', 4 => 'Lainnya', 5 => 'RPS Terkait'],
                'errorsCount' => $this->getUserErrorSections(),
            ])
        </template>
        <template x-if="$store.user.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.paginate.tab-form', [
                'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Identitas', 4 => 'Lainnya'],
                'errorsCount' => $this->getUserErrorSections(),
            ])
        </template>
    @else
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Identitas', 4 => 'Lainnya'],
            'errorsCount' => $this->getUserErrorSections(),
        ])
    @endif

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-main-input')

        </div>
        <div x-show="step === 2">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-personal-input')
        </div>
        <div x-show="step === 3">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-identitas-input')
        </div>
        <div x-show="step === 4">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-lainnya-input')
        </div>
        @if ($withRPS ?? false)
            <div x-show="step === 5">
                @include('livewire.admin.user-management.user-modal-form.user-rps')
                <template x-if="$store.user.isEdit && ($store.user?.typeModal == 'dosen' || $store.user?.typeModal == 'mahasiswa')" x-cloak>
                    @include('livewire.staff.obe-management.obe-partial.rps-list', [
                        'alpine' => 'user',
                        'rps_items_list' => $user_rps_items_list,
                        'rps_modal_paginator' => $user_rps_modal_paginator,
                        'nameXString' => 'Dosen',
                        'wireLoading' => 'editUser',
                    ])
                </template>
            </div>
        @endif
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @if ($withRPS ?? false)
        <template x-if="$store.user.isEdit" x-cloak>
            @include('livewire.global.modal-form.paginate.stepper-form', [
                'maxStep' => 4,
            ])
        </template>
        <template x-if="$store.user.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.paginate.stepper-form', [
                'maxStep' => 3,
            ])
        </template>
    @else
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 3,
        ])
    @endif
</div>
