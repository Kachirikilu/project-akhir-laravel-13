<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showUserAntreanModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showUserAntreanModal
    "
    @next-antrean-step.window="step = 2">
    
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Input Antrean', 2 => 'List Antrean'],
        'errorsCount' => $this->getUserAntreanErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input-partial.user-antrean-main-input')
        </div>
        <div x-show="step === 2">
            @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input-partial.user-antrean-list')
            {{-- @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input-partial.user-antrean-modal') --}}
        </div>
    </div>

    <livewire:admin.user-management.antrean-error-user-management />

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 2,
    ])
</div>
