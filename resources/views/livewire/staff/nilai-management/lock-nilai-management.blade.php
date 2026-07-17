<div>
    <flux:modal name="lock-nilai-modal" wire:model.live="showLockNilaiModal" x-data
        @refresh-data-nilai.window="$store.nilai.reset()" wire:key="lock-nilai-modal"
        class="w-full md:w-[90vw] max-w-3xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            <div class="flex flex-col h-full relative">

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                    <h3 class="text-xl font-semibold">

                        <flux:badge icon="chart-pie" color="blue" size="lg">
                            <span>Pengaturan Kunci Nilai Mahasiswa</span>
                        </flux:badge>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
                    <form x-on:submit.prevent="$wire.updateLockNilai($store.nilai.getDataLockNilai())"
                        enctype="multipart/form-data" id="lockNilaiForm">

                        <div class="form-container">
                            <h4
                                class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
                                Input Nilai yang Tidak Ditampilkan</h4>

                            <div class="relative">
                                <div class="space-y-4">


                                    @include('livewire.global.modal-form.select-form', [
                                        'alpine' => 'nilai',
                                        'isLivewire' => 1,
                                        'modelString' => 'ganjil_genap',
                                        'xOptions' => ['Ganjil', 'Genap'],
                                        'iconString' => 'users',
                                        'placeholder' => 'Ganjil/Genap...',
                                        'message' => $errors->first('ganjil_genap'),
                                    ])
                                    <div class="space-y-4">
                                        <div>
                                            <div class="grid grid-cols-4 gap-2 sm:gap-4 items-end"
                                                x-data="{}" x-init="$watch('$store.nilai.akademik_1', value => {
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
                                        </div>

                                    </div>

                                </div>
                            </div>

                            @include('livewire.global.modal-form.input-form', [
                                'alpine' => 'nilai',
                                'isLivewire' => 1,
                                'nameXString' => 'Tanggal Dibuka',
                                'modelString' => 'tanggal_unlock',
                                'iconString' => 'calendar-days',
                                'isDate' => 1,
                                'message' => $errors->first('tanggal_unlock'),
                            ])

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
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-skeleton')
        @endif
    </flux:modal>
</div>
