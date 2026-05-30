<div 
    x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showSesiModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showSesiModal
    "
>
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Pertemuan 1-8', 2 => 'Pertemuan 9-16'],
        'errorsCount' => $this->getAbsensiErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-mahasiswa')
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input', [
                'indexStart' => 0,
                'indexEnd' => 8
            ])
        </div>
        <div x-show="step === 2">
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input-partial.absensi-main-input', [
                'indexStart' => 8,
                'indexEnd' => null
            ])
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 3
    ])
</div>
