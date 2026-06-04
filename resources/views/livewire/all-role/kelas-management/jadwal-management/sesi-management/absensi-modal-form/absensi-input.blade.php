<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showSesiModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showSesiModal
    ">
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Pertemuan 1-4', 2 => 'Pertemuan 5-8', 3 => 'Pertemuan 9-12', 4 => 'Pertemuan 13-16'],
        'errorsCount' => $this->getAbsenErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-mahasiswa')
        <div x-show="step === 1">
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input',
                [
                    'indexStart' => 0,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 2">
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input',
                [
                    'indexStart' => 4,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 3">
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input',
                [
                    'indexStart' => 8,
                    'indexLenght' => 4,
                ]
            )
        </div>
        <div x-show="step === 4">
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input',
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
