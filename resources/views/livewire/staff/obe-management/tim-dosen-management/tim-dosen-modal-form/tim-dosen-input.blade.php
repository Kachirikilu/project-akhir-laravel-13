<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showTimDosenModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showTimDosenModal
    ">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    <template x-if="$store.tim_dosen.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Edit Tim Dosen', 2 => 'RPS Terkait'],
            'errorsCount' => $this->getTimDosenErrorSections(),
        ])
    </template>

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-input-partial.tim-dosen-main-input')
            @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-input-partial.tim-dosen-pengajar-input')

        </div>
        <div x-show="step === 2">
            <template x-if="$store.tim_dosen.isEdit" x-cloak>
                @include('livewire.staff.obe-management.obe-partial.rps-list', [
                    'alpine' => 'tim_dosen',
                    'rps_items_list' => $tim_dosen_rps_items_list,
                    'rps_modal_paginator' => $tim_dosen_rps_modal_paginator,
                    'nameXString' => 'Tim Dosen',
                    'wireLoading' => 'editTimDosen',
                    'parent' => 'tim-dosen',
                    'isFlyout' => true,
                ])
            </template>
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    <template x-if="$store.tim_dosen.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 2,
        ])
    </template>
</div>
