<div>
    <flux:modal name="lock-nilai-modal" wire:model.live="showLockNilaiModal" x-data
        @refresh-data-nilai.window="$store.nilai.reset()" wire:key="lock-nilai-modal"
        class="modal-flux md:w-[90vw] max-w-3xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'updateLockNilai'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="chart-pie" color="blue" size="lg">
                            <span>Pengaturan Kunci Nilai Mahasiswa</span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form x-on:submit.prevent="$wire.updateLockNilai($store.nilai.getDataLockNilai())"
                        enctype="multipart/form-data" id="lockNilaiForm">

                        <div class="form-container">
                            <h4
                                class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
                                Input Pengaturan Kunci Nilai</h4>

                            <div class="space-y-4">

                                <div class="space-y-2">

                                    @include('livewire.global.modal-form.partial.label', [
                                        'nameXString' => 'Tanggal Akademik Ganjil',
                                    ])

                                    <div class="grid grid-cols-5 sm:grid-cols-4 gap-2 items-end">
                                        <div class="col-span-2 sm:col-span-1">
                                            @include('livewire.global.modal-form.input-form', [
                                                'alpine' => 'nilai',
                                                'isLivewire' => 1,
                                                'isXModal' => 1,
                                                'modelString' => 'tanggal_ganjil',
                                                'numberOnly' => 1,
                                                'maxMonth' => 'bulan_ganjil',
                                                'iconString' => 'calendar-days',
                                                'placeholder' => 'Tanggal...',
                                                'isFocusSelect' => 1,
                                                'noLabel' => 1,
                                            ])
                                        </div>
                                        <div class="col-span-3">
                                            @include('livewire.global.modal-form.select-form', [
                                                'alpine' => 'nilai',
                                                'isLivewire' => 1,
                                                'modelString' => 'bulan_ganjil',
                                                'xOptions' => [
                                                    'Juli',
                                                    'Agustus',
                                                    'September',
                                                    'Oktober',
                                                    'November',
                                                    'Desember',
                                                    'Januari',
                                                ],
                                                'xValues' => ['07', '08', '09', '10', '11', '12', '01'],
                                                'iconString' => 'calendar',
                                                'placeholder' => 'Bulan Akademik Ganjil...',
                                                'noLabel' => 1,
                                                'maxH' => 'max-h-40'
                                            ])
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('ganjil_unlock') }}</span>
                                </div>

                                <div class="space-y-2">

                                    @include('livewire.global.modal-form.partial.label', [
                                        'nameXString' => 'Tanggal Akademik Genap',
                                    ])

                                    <div class="grid grid-cols-5 sm:grid-cols-4 gap-2 items-end">
                                        <div class="col-span-2 sm:col-span-1">
                                            @include('livewire.global.modal-form.input-form', [
                                                'alpine' => 'nilai',
                                                'isLivewire' => 1,
                                                'isXModal' => 1,
                                                'modelString' => 'tanggal_genap',
                                                'numberOnly' => 1,
                                                'maxMonth' => 'bulan_genap',
                                                'iconString' => 'calendar-days',
                                                'placeholder' => 'Tanggal...',
                                                'isFocusSelect' => 1,
                                                'noLabel' => 1,
                                            ])
                                        </div>
                                        <div class="col-span-3">
                                            @include('livewire.global.modal-form.select-form', [
                                                'alpine' => 'nilai',
                                                'isLivewire' => 1,
                                                'modelString' => 'bulan_genap',
                                                'xOptions' => [
                                                    'Februari',
                                                    'Maret',
                                                    'April',
                                                    'Mei',
                                                    'Juni',
                                                    'Juli',
                                                ],
                                                'xValues' => ['02', '03', '04', '05', '06', '07'],
                                                'iconString' => 'calendar',
                                                'placeholder' => 'Bulan Akademik Genap...',
                                                'noLabel' => 1,
                                                'maxH' => 'max-h-40'
                                            ])
                                        </div>
                                    </div>
                                    @error('genap_unlock')
                                        <span
                                            class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('genap_unlock') }}</span>
                                    @enderror
                                </div>

                                {{-- @include('livewire.global.modal-form.select-form', [
                                        'alpine' => 'nilai',
                                        'isLivewire' => 1,
                                        'modelString' => 'ganjil_genap',
                                        'xOptions' => ['Ganjil', 'Genap'],
                                        'iconString' => 'users',
                                        'placeholder' => 'Ganjil/Genap...',
                                        'message' => $errors->first('ganjil_genap'),
                                    ])
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-4 gap-2 sm:gap-4 items-end" x-data="{}"
                                            x-init="$watch('$store.nilai.akademik_1', value => {
                                                let year = parseInt(value);
                                                if (year && year >= 0) {
                                                    $store.nilai.akademik_2 = year + 1;
                                                }
                                            });
                                            $watch('$store.nilai.akademik_2', value => {
                                                let year = parseInt(value);
                                                if (year && year >= 0) {
                                                    $store.nilai.akademik_1 = year - 1;
                                                }
                                            });"
                                            x-effect="
                                                    if ($store.nilai.akademik_1 && $store.nilai.akademik_2) {
                                                        $store.nilai.akademik = $store.nilai.akademik_1 + '/' + $store.nilai.akademik_2;
                                                    } else {
                                                        $store.nilai.akademik = '';
                                                    }
                                                ">
                                            <div class="col-span-2">
                                                @include('livewire.global.modal-form.input-form', [
                                                    'alpine' => 'nilai',
                                                    'isLivewire' => 1,
                                                    'isXModal' => 1,
                                                    'nameXString' => 'Tahun Akademik',
                                                    'modelString' => 'akademik_1',
                                                    'numberOnly' => 1,
                                                    'maxLength' => 4,
                                                    'iconString' => 'calendar-days',
                                                    'placeholder' => 'Contoh: 2025',
                                                    'isFocusSelect' => 1,
                                                ])
                                            </div>
                                            <div class="col-span-2">
                                                @include('livewire.global.modal-form.input-form', [
                                                    'alpine' => 'nilai',
                                                    'isLivewire' => 1,
                                                    'isXModal' => 1,
                                                    'nameXString' => 'Tahun Akademik',
                                                    'modelString' => 'akademik_2',
                                                    'numberOnly' => 1,
                                                    'maxLength' => 4,
                                                    'iconString' => 'calendar-days',
                                                    'placeholder' => 'Contoh: 2026',
                                                    'isFocusSelect' => 1,
                                                    'noLabel' => 1,
                                                ])
                                            </div>
                                        </div>

                                        @error('akademik')
                                            <span
                                                class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('akademik') }}</span>
                                        @enderror
                                    </div> --}}
                            </div>

                            {{-- @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'nilai',
                                'isLivewire' => 1,
                                'nameXString' => 'Tanggal Dibuka',
                                'modelString' => 'tanggal_unlock',
                                'iconString' => 'calendar-days',
                                'isDate' => 1,
                                'message' => $errors->first('tanggal_unlock'),
                            ]) --}}

                        </div>

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'editLockNilai, updateLockNilai',
                                    'isLeft' => 0,
                                    'mt' => '',
                                ])
                            </div>
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-skeleton')
            @endif
        </div>
    </flux:modal>
</div>
