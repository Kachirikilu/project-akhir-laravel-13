<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showSCPMKModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showSCPMKModal
    ">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    <template x-if="$store.scpmk.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Sub-CPMK', 2 => 'Materi', 3 => 'Metode', 4 => 'Referensi', 5 => 'RPS Terkait'],
            'errorsCount' => $this->getSCPMKErrorSections(),
        ])
    </template>
    <template x-if="$store.scpmk.isEdit == 0" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Sub-CPMK', 2 => 'Materi', 3 => 'Metode', 4 => 'Referensi'],
            'errorsCount' => $this->getSCPMKErrorSections(),
        ])
    </template>

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-partial.scpmk-main-input')
        </div>
        <div x-show="step === 2">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-partial.scpmk-materi-input')
        </div>
        <div x-show="step === 3">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-partial.scpmk-metode-input')
        </div>
        <div x-show="step === 4">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-partial.scpmk-ref-input')
        </div>
        <div x-show="step === 5">
            <template x-if="$store.scpmk.isEdit" x-cloak>
                @include('livewire.staff.obe-management.obe-partial.rps-list', [
                    'alpine' => 'scpmk',
                    'rps_items_list' => $scpmk_rps_items_list,
                    'rps_modal_paginator' => $scpmk_rps_modal_paginator,
                    'nameXString' => 'Sub-CPMK',
                    'wireLoading' => 'SCPMK',
                ])
            </template>
        </div>

    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    <template x-if="$store.scpmk.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 5,
        ])
    </template>
    <template x-if="$store.scpmk.isEdit == 0" x-cloak>
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 4,
        ])
    </template>

</div>
