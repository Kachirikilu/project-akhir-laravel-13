<div x-data="{ step: 1, isOpen: false }"
    x-effect="
        if ($wire.showCPLModal && !isOpen) {
            step = 1
        }
        isOpen = $wire.showCPLModal
    ">
    {{-- 🔹 HEADER TAB CONTAINER --}}
    <template x-if="$store.cpl.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.tab-form', [
            'tabs' => [1 => 'CPL', 2 => 'RPS Terkait'],
            'errorsCount' => $this->getCPLErrorSections(),
        ])
    </template>

    {{-- 🔹 CONTENT --}}
    <div class="mt-4">
        <div x-show="step === 1">

            <div class="form-container">
                <h4
                    class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
                    Input Capaian Pembelajaran Lulusan</h4>

                <div class="relative" x-data="{}"
                    x-effect="$store.cpl.kode_cpl = ($store.cpl.kode_cpl_1 || '') + ($store.cpl.kode_cpl_2 || '')">
                    @include('livewire.global.modal-form.loading-animation', [
                        'wireLoading' => 'addCPL, editCPL',
                    ])

                    <div class="space-y-4">
                        <div>
                            @include('livewire.global.modal-form.partial.label', [
                                'nameXString' => 'Kode CPL',
                            ])
                            <div class="grid grid-cols-12 gap-1 sm:gap-2 items-end">

                                <div class="col-span-4 sm:col-span-3">
                                    <template x-if="$store.cpl?.typeModal == 1" x-cloak>
                                        @include('livewire.global.modal-form.kode-input', [
                                            'alpine' => 'cpl',
                                            'noLabel' => 1,
                                            'pathString' => 'pr_items',
                                            'modelString' => 'kode',
                                            'placeholder' => '---',
                                            'iconString' => 'academic-cap',
                                        ])
                                    </template>
                                    <template x-if="$store.cpl?.typeModal == 2" x-cloak>
                                        @include('livewire.global.modal-form.kode-input', [
                                            'alpine' => 'cpl',
                                            'noLabel' => 1,
                                            'pathString' => 'dp_items',
                                            'modelString' => 'kode',
                                            'placeholder' => '---',
                                            'iconString' => 'book-open',
                                        ])
                                    </template>
                                    <template x-if="$store.cpl?.typeModal == 3" x-cloak>
                                        @include('livewire.global.modal-form.kode-input', [
                                            'alpine' => 'cpl',
                                            'noLabel' => 1,
                                            'pathString' => 'fk_items',
                                            'modelString' => 'kode',
                                            'placeholder' => '---',
                                            'iconString' => 'building-library',
                                        ])
                                    </template>
                                    <template x-if="$store.cpl?.typeModal == 4" x-cloak>
                                        @include('livewire.global.modal-form.kode-input', [
                                            'alpine' => 'cpl',
                                            'noLabel' => 1,
                                            'modelString' => 'kode',
                                            'valueString' => 'UNI',
                                            'iconString' => 'globe-alt',
                                        ])
                                    </template>
                                </div>
                                <div class="col-span-4">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'cpl',
                                        'nameXString' => 'Kode CPL',
                                        'modelString' => 'kode_cpl_1',
                                        'iconString' => 'document-text',
                                        'placeholder' => 'Kode CPL...',
                                        'isKode' => 4,
                                        'isFocusSelect' => 1,
                                        'noLabel' => 1,
                                    ])
                                </div>
                                <div class="col-span-4 sm:col-span-5">
                                    @include('livewire.global.modal-form.input-form', [
                                        'alpine' => 'cpl',
                                        'noLabel' => 1,
                                        'modelString' => 'kode_cpl_2',
                                        'numberOnly' => 1,
                                        'maxLength' => 6,
                                        'iconString' => 'variable',
                                        'placeholder' => 'Contoh: 121104',
                                        'isFocusSelect' => 1,
                                    ])
                                </div>
                            </div>
                            @error('kode_cpl')
                                <span
                                    class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_cpl') }}</span>
                            @enderror
                        </div>
                        @include('livewire.global.modal-form.textarea-form', [
                            'alpine' => 'cpl',
                            'nameXString' => 'Deskripsi',
                            'modelString' => 'deskripsi',
                            'iconString' => 'document-text',
                            'placeholder' => 'Masukkan deskripsi CPL...',
                            'message' => $errors->first('deskripsi'),
                        ])
                        <template x-if="$store.cpl?.typeModal == 1" x-cloak>
                            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input-partial.cpl-prodi-input')
                        </template>

                        <template x-if="$store.cpl?.typeModal == 2" x-cloak>
                            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input-partial.cpl-departemen-input')
                        </template>

                        <template x-if="$store.cpl?.typeModal == 3" x-cloak>
                            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input-partial.cpl-fakultas-input')
                        </template>

                        <template x-if="$store.cpl?.typeModal == 4" x-cloak>
                            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input-partial.cpl-universitas-input')
                        </template>
                    </div>
                </div>

            </div>

        </div>
        <div x-show="step === 2">
            <template x-if="$store.cpl.isEdit" x-cloak>
                @include('livewire.staff.obe-management.obe-partial.rps-list', [
                    'alpine' => 'cpl',
                    'rps_items_list' => $cpl_rps_items_list,
                    'rps_modal_paginator' => $cpl_rps_modal_paginator,
                    'nameXString' => 'CPL',
                    'parent' => 'cpl',
                    'isFlyout' => true,
                ])
            </template>

        </div>
    </div>

    {{-- 🔹 FOOTER STEPPER --}}
    <template x-if="$store.cpl.isEdit" x-cloak>
        @include('livewire.global.modal-form.paginate.stepper-form', [
            'maxStep' => 2,
        ])
    </template>
</div>
