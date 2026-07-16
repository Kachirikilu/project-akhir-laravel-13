<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showJadwalModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showJadwalModal
    ">
    {{-- 🔹 HEADER TAB CONTAINER --}}
    @include('livewire.global.modal-form.paginate.tab-form', [
        'tabs' => [1 => 'Jadwal Kelas', 2 => 'Sesi Kelas', 3 => 'Mahasiswa Kelas'],
        'errorsCount' => $this->getJadwalErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-input-partial.jadwal-main-input')
        </div>
        <div x-show="step === 2">
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-input-partial.jadwal-hari-input')
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-input-partial.jadwal-sesi-input')
        </div>
        <div x-show="step === 3">
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-input-partial.jadwal-mahasiswa-input')
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 3,
    ])
</div>
