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
                'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Akademik', 4 => 'Akademik', 5 => 'Lainnya', 6 => 'RPS Terkait'],
                'errorsCount' => $this->getUserErrorSections(),
            ])
        </template>
        <template x-if="$store.user.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.paginate.tab-form', [
                'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Akademik', 4 => 'Akademik', 5 => 'Lainnya'],
                'errorsCount' => $this->getUserErrorSections(),
            ])
        </template>
    @else
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Akun', 2 => 'Personal', 3 => 'ID Akademik', 4 => 'Akademik', 5 => 'Lainnya'],
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
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-id-akademik-input')
        </div>
        <div x-show="step === 4">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-akademik-input')
        </div>
        <div x-show="step === 5">
            @include('livewire.admin.user-management.user-modal-form.user-input-partial.user-lainnya-input')
        </div>
        @if ($withRPS ?? false)
        <div x-show="step === 6" 
            x-init="$store.user.count_rps = {{ $user_input['count_rps'] ?? 0 }}; 
                    $store.user.total_sks = {{ $user_input['total_sks'] ?? 0 }};">
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
                'maxStep' => 5,
            ])
        </template>
        <template x-if="$store.user.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.paginate.stepper-form', [
                'maxStep' => 4,
            ])
        </template>
    @else
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 4,
        ])
    @endif
</div>
