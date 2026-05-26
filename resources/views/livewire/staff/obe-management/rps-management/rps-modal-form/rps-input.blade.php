<div 
    x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showRPSModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showRPSModal
    "
>
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'RPS', 2 => 'CPMK', 3 => 'CPL', 4 => 'Referensi', 5 => 'Dosen'],
        'errorsCount' => $this->getRPSErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-main-input')
        </div>

        <div x-show="step === 2">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-cpmk-input')
        </div>

        <div x-show="step === 3">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-cpl-input')
        </div>

        <div x-show="step === 4">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-ref-input')
        </div>

        <div x-show="step === 5">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-partial.rps-dosen-input')
        </div>

    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 5,
    ])

</div>
