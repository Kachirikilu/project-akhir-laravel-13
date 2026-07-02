<div>
    <flux:modal name="join-jadwal" wire:model.live="showJadwalJoin" wire:key="join-jadwal-modal" x-data @refresh-data-jadwal.window="$store.jadwal?.reset()"
        class="w-full md:w-lg max-w-lg !bg-[var(--second-pop-up-color)] !text-[var(--contrast-main-text)]">

        <form x-on:submit.prevent="$wire.joinJadwal($store.jadwal.getDataJoinJadwal())" id="jadwalForm">
            <div class="py-4 space-y-4">

                <div class="flex items-center gap-3.5 pb-2">
                    <div
                        class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 shadow-sm flex-shrink-0">
                        <flux:icon name="academic-cap" class="w-5 h-5 text-emerald-500" />
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 ml-1">
                        <h3 class="text-lg font-bold tracking-tight text-[var(--contrast-main-text)]"
                            x-text="'Kelas ' + ($store.jadwal?.label_extra ?? '-')">
                        </h3>

                        <x-label-card>
                            <span x-text="$store.jadwal?.kode"></span>
                        </x-label-card>
                    </div>
                </div>

                {{-- INPUT PASSWORD --}}
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'modelString' => 'password',
                    'iconString' => 'lock-closed',
                    'placeholder' => 'Masukkan Password...',
                    'isRequired' => 0,
                    'message' => $errors->first('password'),
                ])

                <div class="flex justify-end pt-2">

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="joinJadwal"
                        class="cursor-pointer w-full sm:w-auto
                        bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90
                        shadow-sm text-white border-none transition-all duration-200">

                        <span wire:loading.remove wire:target="joinJadwal" class="text-white">
                            Join Kelas
                        </span>
                        <span wire:loading wire:target="joinJadwal" class="text-white">
                            Memproses...
                        </span>
                    </flux:button>
                </div>

            </div>
        </form>

    </flux:modal>

</div>
