<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showUserAntrianModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showUserAntrianModal
    "
    @next-antrian-step.window="step = 2">
    
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Input Antrian', 2 => 'List Antrian'],
        'errorsCount' => $this->getUserAntrianErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrian-input-partial.user-antrian-main-input')
        </div>
        <div x-show="step === 2">
            @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrian-input-partial.user-antrian-list')
            {{-- @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrian-input-partial.user-antrian-modal') --}}
        </div>
    </div>

    <livewire:admin.user-management.antrian-error-user-management />

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 2,
    ])
</div>
