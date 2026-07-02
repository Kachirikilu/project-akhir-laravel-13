<flux:modal name="absensi-sesi-modal" wire:model="showSesiAbsen" x-data @refresh-data-sesi.window="$store.sesi?.reset()"
    class="max-w-lg w-full">

    <form x-on:submit.prevent="$wire.absensiSesi($store.sesi)" id="sesiForm">
        <div class="flex flex-col gap-5">

            {{-- Header Modal --}}
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-500/15">
                        <flux:icon name="academic-cap" class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <flux:heading size="lg" x-data>
                            Absen Kelas <span x-text="$store.sesi.jadwal_kode"></span>
                        </flux:heading>
                        <flux:text class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
                            <span
                                x-text="'Pertemuan ' + $store.sesi.pertemuan_ke + ' — ' +  $store.sesi.kode_scpmk"></span>
                        </flux:text>
                    </div>
                </div>
            </div>

            {{-- Status Kehadiran: pilihan kartu --}}
            <div class="flex flex-col gap-2">
                <label
                    class="flex items-center gap-2 text-xs sm:text-sm font-medium mb-3 text-[var(--contrast-main-text)]">
                    <flux:icon name="check-badge" class="w-4 h-4"
                        x-bind:class="$store.sesi.colorIcon ?? 'text-gray-400'" />
                    <div>
                        <span class="font-semibold text-[var(--contrast-main-text)]">
                            Status Kehadiran
                        </span>
                        <span class="text-red-500">*</span>
                    </div>
                </label>


                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <template x-for="item in $store.sesi.getOpsiStatus()" :key="item.label">
                        <button type="button" @click="$store.sesi.absen = item.label"
                            class="group relative flex flex-col items-center gap-1.5 rounded-[12px] border px-2 py-3 text-center transition-all duration-200 cursor-pointer"
                            :class="$store.sesi.absen === item.label ?
                                item.bg_active +
                                ' ring-2 shadow-md scale-[1.02] border-[var(--focus-color)] bg-[var(--focus-color)]/10 ring-1 ring-[var(--focus-color)]' :
                                'border-[var(--border-table-color)] bg-[var(--second-table-color)] hover:bg-[var(--sub-table-color)]'">

                            {{-- Indikator centang aktif --}}
                            <div x-show="$store.sesi.absen === item.label" x-transition
                                class="absolute top-1.5 right-1.5 text-[var(--focus-color)]">
                                <flux:icon name="check-circle" class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                    :class="$store.sesi.absen === item.label ?
                                        item.icon_active :
                                        'bg-black/5 dark:bg-white/5 ' + item.icon_default">

                                    <div x-show="item.icon === 'check-circle'">
                                        <flux:icon name="check-circle" class="w-5 h-5 variant-current" />
                                    </div>
                                    <div x-show="item.icon === 'clock'">
                                        <flux:icon name="clock" class="w-5 h-5 variant-current" />
                                    </div>
                                    <div x-show="item.icon === 'shield-check'">
                                        <flux:icon name="shield-check" class="w-5 h-5 variant-current" />
                                    </div>
                                    <div x-show="item.icon === 'document-text'">
                                        <flux:icon name="document-text" class="w-5 h-5 variant-current" />
                                    </div>
                                    <div x-show="item.icon === 'heart'">
                                        <flux:icon name="heart" class="w-5 h-5 variant-current" />
                                    </div>
                                    <div x-show="item.icon === 'x-circle'">
                                        <flux:icon name="x-circle" class="w-5 h-5 variant-current" />
                                    </div>
                                </div>

                                {{-- <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-xs sm:text-sm text-[var(--contrast-main-text)]"
                                        x-text="item.label"></div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Pilih status <span x-text="item.label.toLowerCase()"></span>
                                    </div>
                                </div> --}}
                            </div>

                            <span class="text-[11px] sm:text-xs font-semibold text-[var(--contrast-main-text)]"
                                x-text="item.label"></span>
                        </button>
                    </template>
                </div>

                @error('absen')
                    <p class="text-[11px] sm:text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3 mt-4">
                <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                <span class="text-[10px] font-bold uppercase tracking-wide text-[var(--contrast-third-text)]">
                    Keterangan Tambahan
                </span>
                <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
            </div>

            {{-- Keterangan --}}
            <div
                class="mt-3 rounded-[12px] border border-[var(--border-table-color)] bg-[var(--sub-table-color)] px-3 py-2.5">
                <div x-show="$store.sesi.absen == 'Hadir'">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'sesi',
                        'modelString' => 'keterangan',
                        'iconString' => 'pencil-square',
                        'placeholder' => 'Masukkan Keterangan...',
                        'isRequired' => 0,
                        'message' => $errors->first('keterangan'),
                    ])
                </div>

                <div x-show="$store.sesi.absen !== 'Hadir'">
                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'sesi',
                        'modelString' => 'keterangan',
                        'iconString' => 'pencil-square',
                        'placeholder' => 'Masukkan Keterangan...',
                        'message' => $errors->first('keterangan'),
                    ])
                </div>
            </div>


            {{-- Footer Aksi --}}
            <div class="flex items-center gap-2 pt-1">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer flex-1 justify-center transition-all">Batal
                    </flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check-circle" wire:loading.attr="disabled"
                    wire:target="absensiSesi"
                    class="cursor-pointer flex-1 justify-center bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 text-white border-none transition-all ">
                    <span wire:loading.remove wire:target="absensiSesi">Absen</span>
                    <span wire:loading wire:target="absensiSesi">Memproses...</span>
                </flux:button>
            </div>
        </div>
    </form>

</flux:modal>
