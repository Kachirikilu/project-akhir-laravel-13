<flux:modal name="sesi-absen" wire:model="showSesiAbsen" x-data @refresh-data-sesi.window="$store.sesi?.reset()"
    class="md:w-[90vw] max-w-3xl !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

    <form x-on:submit.prevent="$wire.absenSesi($store.sesi)" id="sesiForm">
        <div class="py-4 space-y-4">

            <div class="flex items-center gap-3.5 pb-2">
                <div
                    class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 shadow-sm flex-shrink-0">
                    <flux:icon name="academic-cap" class="w-5 h-5 text-emerald-500" />
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 ml-1">
                    <h3 class="text-lg font-bold tracking-tight text-[var(--contrast-main-text)]">
                        Absen Kelas {{ $jadwal->kode }}
                    </h3>
                    <x-label-card type="lg">
                        <span x-text="'Pertemuan ' + $store.sesi.pertemuan_ke"></span>
                    </x-label-card>
                </div>
            </div>
            <div class="space-y-2 mt-6 mb-4">

                <label class="flex items-center gap-2 text-sm font-medium mb-3 text-[var(--contrast-main-text)]">
                    <flux:icon name="check-badge" class="w-4 h-4"
                        x-bind:class="$store.sesi.colorIcon ?? 'text-gray-400'" />
                    <div>
                        <span class="font-semibold text-[var(--contrast-main-text)]">
                            Status Kehadiran
                        </span>
                        <span class="text-red-500">*</span>
                    </div>
                </label>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <template x-for="item in $store.sesi.getOpsiStatus()" :key="item.label">
                        <button type="button" @click="$store.sesi.absen = item.label"
                            class="group relative overflow-hidden rounded-2xl border p-4 transition-all duration-300 text-left cursor-pointer"
                            :class="$store.sesi.absen === item.label ?
                                item.bg_active + ' ring-2 shadow-md scale-[1.02]' :
                                'table-border bg-[var(--second-pop-up-color)] hover:border-gray-400 dark:hover:border-gray-500 hover:shadow-sm hover:-translate-y-[1px]'">

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

                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-sm text-[var(--contrast-main-text)]"
                                        x-text="item.label"></div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Pilih status <span x-text="item.label.toLowerCase()"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- FIX: Indikator Aktif (Menggunakan div pembungkus x-bind:class agar tidak bentrok dengan PHP) --}}
                            <div x-show="$store.sesi.absen === item.label" x-transition class="absolute top-2 right-2">
                                <div :class="item.icon_default">
                                    <flux:icon name="check-circle" class="w-5 h-5" />
                                </div>
                            </div>

                        </button>
                    </template>
                </div>

                @error('absen')
                    <p class="text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'sesi',
                'modelString' => 'keterangan',
                'iconString' => 'pencil-square',
                'placeholder' => 'Masukkan Keterangan...',
                'isRequired' => 0,
                'message' => $errors->first('keterangan'),
            ])

            <div class="flex justify-end pt-2">

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="absenSesi"
                    class="cursor-pointer w-full sm:w-auto
                        bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]
                        shadow-sm text-white border-none transition-all duration-200">

                    <span wire:loading.remove wire:target="absenSesi" class="text-white">
                        Absen
                    </span>
                    <span wire:loading wire:target="absenSesi" class="text-white">
                        Memproses...
                    </span>
                </flux:button>
            </div>

        </div>
    </form>

</flux:modal>
