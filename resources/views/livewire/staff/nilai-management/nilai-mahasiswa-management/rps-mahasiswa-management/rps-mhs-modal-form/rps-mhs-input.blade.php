<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showEditNilai && !isOpen) {
            step = 1
        }
        isOpen = $wire.showEditNilai
    ">
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Pertemuan 1-4', 2 => 'Pertemuan 5-8', 3 => 'Pertemuan 9-12', 4 => 'Pertemuan 13-16'],
        'errorsCount' => $this->getNilaiErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input-partial.rps-mhs-mahasiswa')
        <div x-show="step === 1">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input-partial.rps-mhs-main-input',
                [
                    'indexStart' => 0,
                    'indexEnd' => 4,
                ]
            )
        </div>
        <div x-show="step === 2">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input-partial.rps-mhs-main-input',
                [
                    'indexStart' => 4,
                    'indexEnd' => 8,
                ]
            )
        </div>
        <div x-show="step === 3">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input-partial.rps-mhs-main-input',
                [
                    'indexStart' => 8,
                    'indexEnd' => 12,
                ]
            )
        </div>
        <div x-show="step === 4">
            @include(
                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input-partial.rps-mhs-main-input',
                [
                    'indexStart' => 12,
                    'indexEnd' => null,
                ]
            )
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 4,
    ])
</div>
