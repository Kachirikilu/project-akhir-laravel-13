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
        'tabs' => [1 => 'Sesi Kelas', 2 => 'Sub-CPMK'],
        'errorsCount' => $this->getSesiErrorSections(),
    ])

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-input-partial.sesi-main-input')
        </div>
        <div x-show="step === 2">
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-input-partial.sesi-scpmk-input')
        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    @include('livewire.global.modal-form.paginate.stepper-form', [
        'maxStep' => 3
    ])
</div>
