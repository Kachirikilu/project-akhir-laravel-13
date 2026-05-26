<div 
    x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showMKModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showMKModal
    "
>
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Mata Kuliah', 2 => 'Deskripsi & Bahan Kajian'],
        'errorsCount' => $this->getMKErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-main-input')
        </div>
        <div x-show="step === 2">

            @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-kajian-input')
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 2,
    ])
</div>
