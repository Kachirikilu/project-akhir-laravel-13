<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showProdiModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showProdiModal
    ">

    {{-- 🔹 HEADER TAB CONTAINER --}}
    <template x-if="$store.user.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [
                1 => 'Fakultas',
                2 => 'Dekan & Wakil Dekan',
            ],
            'errorsCount' => $this->getProdiErrorSections(),
        ])
    </template>
    <template x-if="$store.user.isEdit == 0" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'Fakultas', 2 => 'Dekan & Wakil Dekan'],
            'errorsCount' => $this->getProdiErrorSections(),
        ])
    </template>

    {{-- ****************************************************** --}}
    {{-- 3. INPUT FAKULTAS --}}
    {{-- ****************************************************** --}}
    <div class="mt-4">

        <div x-show="step === 1">
            <div class="form-container">
                <h4
                    class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
                    Input Fakultas</h4>

                {{-- 📧 Fakultas Input --}}
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'prodi',
                    'nameXString' => 'Nama Fakultas',
                    'modelString' => 'nama_fk',
                    'iconString' => 'building-library',
                    'placeholder' => 'Masukkan nama Fakultas',
                    'message' => $errors->first('nama_fk'),
                ])

                {{-- 📧 Kode Fakultas Input --}}
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'prodi',
                    'nameXString' => 'Kode Fakultas',
                    'modelString' => 'kode_fk',
                    'iconString' => 'hashtag',
                    'placeholder' => 'Masukkan 3 huruf Kode Fakultas',
                    'message' => $errors->first('kode_fk'),
                    'isKode' => 3,
                    'isFocusSelect' => 1,
                ])

            </div>
        </div>

        <div x-show="step === 2">
            @include('livewire.admin.prodi-management.prodi-modal-form.prodi-input-partial.fakultas-dekan-input')
        </div>



    </div>
</div>
