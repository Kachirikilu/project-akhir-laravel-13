<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showModalNilai && !isOpen) {
            step = 1
        }
        isOpen = $wire.showModalNilai
    ">
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Capaian', 2 => 'Pertemuan 1-4', 3 => 'Pertemuan 5-8', 4 => 'Pertemuan 9-12', 5 => 'Pertemuan 13-16'],
        'errorsCount' => $this->getNilaiErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-header')
        <div x-show="step === 1">
            @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-cpmk')
        </div>
        <div x-show="step === 2">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-main-input',
                [
                    'indexStart' => 0,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 3">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-main-input',
                [
                    'indexStart' => 4,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 4">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-main-input',
                [
                    'indexStart' => 8,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 5">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-modal.rps-mahasiswa-modal-partial.rps-mahasiswa-input-partial.rps-mahasiswa-main-input',
                [
                    'indexStart' => 12,
                    'indexLenght' => null,
                ]
            )
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 4,
    ])
</div>
