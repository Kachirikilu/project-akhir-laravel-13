<div class="flex flex-wrap items-center gap-2 mb-4">
    @if ($typeXString == 'all')
        <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Rencana Pembelajaran
            Semester
        </h2>
    @endif
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" :size="($isSmall ?? false) ? 'sm' : null"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] transition-all duration-200 ease-in-out"
                wire:target="addRPS, addCPL, addCPMK, addSCPMK. addRef, addUser">
                Tambah
                @if ($typeXString == 'rps')
                    RPS
                @elseif ($typeXString == 'cpl')
                    CPL
                @elseif ($typeXString == 'cpmk')
                    CPMK
                @elseif ($typeXString == 'scpmk')
                    Sub-CPMK
                @elseif ($typeXString == 'ref')
                    Referensi
                @elseif ($typeXString == 'dosen')
                    Dosen
                @else
                    OBE
                @endif
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

                @if ($typeXString == 'all')
                    <flux:menu.heading>Pilih OBE</flux:menu.heading>
                    <flux:menu.separator />
                @endif

                @if ($typeXString == 'rps' || $typeXString == 'all')
                    {{-- RPS --}}
                    <flux:menu.item
                        @click="
                            $store.rps?.setEdit(0);
                            $store.rps?.setFlyout({{ $isFlyout ?? false }});
                            $store.rps?.setColor('text-green-700 dark:text-green-400');
                            $store.rps?.reset(1);
                            $flux.modal('rps-modal').show();
                            $wire.addRPS();
                        "
                        class="cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30">
                        <flux:icon name="clipboard-document-list"
                            class="!text-green-600 dark:!text-green-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Rencana Pembelajaran Semester</span>
                            <flux:icon wire:loading wire:target="addRPS()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>
                @endif

                @if ($typeXString == 'cpl' || $typeXString == 'all')
                    {{-- CPL --}}



                    <flux:menu.item
                        class="cursor-pointer !text-sky-600 dark:!text-sky-400">
                        <flux:icon name="document-text" class="!text-sky-600 dark:!text-sky-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Capaian Pembelajaran Lulusan</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>

                    <flux:menu.item
                        @click="
                            $store.cpl?.setType(1);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-emerald-700 dark:text-emerald-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $wire.addCPL(1);
                        "
                        class="ml-8 cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30">
                        <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">CPL Program Studi</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>

                    <flux:menu.item
                        @click="
                            $store.cpl?.setType(2);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-amber-700 dark:text-amber-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $wire.addCPL(2);
                        "
                        class="ml-8 cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                        <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">CPL Departemen</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>

                    <flux:menu.item
                        @click="
                            $store.cpl?.setType(3);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-indigo-700 dark:text-indigo-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $wire.addCPL(3);
                        "
                        class="ml-8 cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-100 dark:hover:!bg-indigo-900/30">
                        <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">CPL Fakultas</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>

                    <flux:menu.item
                        @click="
                            $store.cpl?.setType(4);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-red-700 dark:text-red-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $wire.addCPL(4);
                        "
                        class="ml-8 cursor-pointer !text-red-600 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30">
                        <flux:icon name="globe-alt" class="!text-red-600 dark:!text-red-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">CPL Universitas</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>





                    {{-- <flux:menu.item
                        @click="
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-red-700 dark:text-red-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $wire.addCPL();
                        "
                        class="cursor-pointer !text-red-600 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30">
                        <flux:icon name="document-text" class="!text-red-600 dark:!text-red-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Capaian Pembelajaran Lulusan</span>
                            <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item> --}}



                    {{-- <flux:dropdown>
                        <flux:button variant="primary" icon="plus" :size="($isSmall ?? false) ? 'sm' : null"
                            class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] transition-all duration-200 ease-in-out"
                            wire:target="addCPL">
                            Tambah CPL
                        </flux:button>

                        <flux:menu
                            class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">
                            <flux:menu.item
                                @click="
                                    $store.cpl?.setEdit(0);
                                    $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                                    $store.cpl?.setColor('text-red-700 dark:text-red-400');
                                    $store.cpl?.reset(1);
                                    $flux.modal('cpl-modal').show();
                                    $wire.addCPL();
                                "
                                class="cursor-pointer !text-red-600 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30">
                                <flux:icon name="document-text" class="!text-red-600 dark:!text-red-400 mr-2 h-4 w-4" />
                                <div class="flex justify-between items-center w-full">
                                    <span class="mr-7">Capaian Pembelajaran Lulusan</span>
                                    <flux:icon wire:loading wire:target="addCPL()" name="arrow-path"
                                        class="animate-spin h-4 w-4 ml-2" />
                                </div>
                            </flux:menu.item>

                        </flux:menu>
                    </flux:dropdown> --}}
                @endif

                @if ($typeXString == 'cpmk-scpmk' || $typeXString == 'cpmk' || $typeXString == 'all')
                    {{-- CPMK --}}
                    <flux:menu.item
                        @click="
                            $store.cpmk?.setEdit(0);
                            $store.cpmk?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpmk?.setColor('text-violet-700 dark:text-violet-400');
                            $store.cpmk?.reset(1);
                            $flux.modal('cpmk-modal').show();
                            $wire.addCPMK();
                        "
                        class="cursor-pointer !text-violet-600 dark:!text-violet-400 hover:!bg-violet-100 dark:hover:!bg-violet-900/30">
                        <flux:icon name="academic-cap" class="!text-violet-600 dark:!text-violet-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">CPMK</span>
                            <flux:icon wire:loading wire:target="addCPMK()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>
                @endif

                @if ($typeXString == 'cpmk-scpmk' || $typeXString == 'scpmk' || $typeXString == 'all')
                    {{-- SCPMK --}}
                    <flux:menu.item
                        @click="
                            $store.scpmk?.setEdit(0);
                            $store.scpmk?.setFlyout({{ $isFlyout ?? false }});
                            $store.scpmk?.setColor('text-fuchsia-700 dark:text-fuchsia-400');
                            $store.scpmk?.reset(1);
                            $flux.modal('scpmk-modal').show();
                            $wire.addSCPMK();
                        "
                        class="cursor-pointer !text-fuchsia-600 dark:!text-fuchsia-400 hover:!bg-fuchsia-100 dark:hover:!bg-fuchsia-900/30">
                        <flux:icon name="academic-cap" class="!text-fuchsia-600 dark:!text-fuchsia-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Sub-CPMK</span>
                            <flux:icon wire:loading wire:target="addSCPMK()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>
                @endif

                @if ($typeXString == 'ref' || $typeXString == 'all')
                    {{-- Referensi --}}
                    <flux:menu.item
                        @click="
                            $store.ref?.setEdit(0);
                            $store.ref?.setFlyout({{ $isFlyout ?? false }});
                            $store.ref?.setColor('text-orange-700 dark:text-orange-400');
                            $store.ref?.reset(1);
                            $flux.modal('ref-modal').show();
                            $wire.addRef();
                        "
                        class="cursor-pointer !text-orange-600 dark:!text-orange-400 hover:!bg-orange-100 dark:hover:!bg-orange-900/30">
                        <flux:icon name="book-open" class="!text-orange-600 dark:!text-orange-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Referensi</span>
                            <flux:icon wire:loading wire:target="addRef()" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>
                @endif

                @if (Auth::user()->admin && ($typeXString == 'dosen' || $typeXString == 'all'))
                    {{-- Dosen --}}
                    <flux:menu.item
                        @click="
                        $store.user?.setType('dosen');
                        $store.user?.setEdit(0);
                        {{-- $store.user?.resetSelect(); --}}
                        $store.user?.setColor('text-lime-700 dark:text-lime-400');
                        $store.user?.reset(1);
                        $flux.modal('user-modal').show();
                        $wire.addUser('dosen');
                    "
                        class="cursor-pointer !text-lime-600 dark:!text-lime-400 hover:!bg-lime-100 dark:hover:!bg-lime-900/30">
                        <flux:icon name="briefcase" class="!text-lime-600 dark:!text-lime-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7">Dosen</span>
                            <flux:icon wire:loading wire:target="addUser('dosen')" name="arrow-path"
                                class="animate-spin h-4 w-4 ml-2" />
                        </div>
                    </flux:menu.item>
                @endif


            </flux:menu>
        </flux:dropdown>
    </div>
</div>
