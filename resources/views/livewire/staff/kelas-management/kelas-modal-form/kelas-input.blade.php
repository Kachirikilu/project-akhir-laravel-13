<div 
    x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showKelasModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showKelasModal
    "
>
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Kelas Perkuliahan', 2 => 'Program Studi & RPS'],
        'errorsCount' => $this->getKelasErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.kelas-management.kelas-modal-form.kelas-input-partial.kelas-main-input')
        </div>
        <div x-show="step === 2">

            @include('livewire.staff.kelas-management.kelas-modal-form.kelas-input-partial.kelas-rps-input')
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 2,
    ])
</div>
