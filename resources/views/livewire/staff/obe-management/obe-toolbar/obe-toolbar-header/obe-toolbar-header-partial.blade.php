@if (Auth::user()->tingkat < 5)

    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" :size="($isSmall ?? false) ? 'xs' : 'sm'"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out"
                wire:target="addRPS, addCPL, addCPMK, addSCPMK. addRef, addUser">
                Tambah
                @if ($typeXString == 'rps')
                    RPS
                @elseif ($typeXString == 'cpl')
                    CPL
                @elseif ($typeXString == 'cpmk' || $typeXString == 'cpmk-scpmk')
                    CPMK
                @elseif ($typeXString == 'scpmk')
                    Sub-CPMK
                @elseif ($typeXString == 'ref')
                    Referensi
                @elseif ($typeXString == 'tim_dosen')
                    Tim Dosen
                @elseif ($typeXString == 'dosen')
                    Dosen
                @else
                    OBE
                @endif
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">

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
                            $dispatch('open-add-rps-modal', { parent: '{{ $parent ?? '' }}' });
                        "
                        class="text-xs sm:text-sm cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900">
                        <flux:icon name="clipboard-document-list"
                            class="!text-green-600 dark:!text-green-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7 whitespace-nowrap">Rencana Pembelajaran Semester</span>
                        </div>
                    </flux:menu.item>
                @endif


                @if ($typeXString == 'cpl' || $typeXString == 'all')
                    {{-- CPL --}}
                    <flux:menu.item
                        @click="
                            $store.cpl?.setType(1);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-emerald-700 dark:text-emerald-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $dispatch('open-add-cpl-modal', { tingkatan: 1, parent: '{{ $parent ?? '' }}' });
                        "
                        class="text-xs sm:text-sm cursor-pointer !text-sky-600 dark:!text-sky-400 hover:!bg-sky-100 dark:hover:!bg-sky-900/30 active:!bg-sky-200 dark:active:!bg-sky-900">
                        <flux:icon name="document-text" class="!text-sky-600 dark:!text-sky-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7 whitespace-nowrap">Capaian Pembelajaran Lulusan</span>
                        </div>
                    </flux:menu.item>

                    @if (Auth::user()->tingkat < 4)

                        <flux:menu.item
                            @click="
                            $store.cpl?.setType(1);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-emerald-700 dark:text-emerald-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $dispatch('open-add-cpl-modal', { tingkatan: 1, parent: '{{ $parent ?? '' }}' });
                        "
                            class="text-xs sm:text-sm ml-8 cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 active:!bg-emerald-200 dark:active:!bg-emerald-900">
                            <flux:icon name="academic-cap"
                                class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7 whitespace-nowrap">CPL Program Studi</span>
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
                            $dispatch('open-add-cpl-modal', { tingkatan: 2, parent: '{{ $parent ?? '' }}' });
                        "
                            class="text-xs sm:text-sm ml-8 cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30 active:!bg-amber-200 dark:active:!bg-amber-900">
                            <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7 whitespace-nowrap">CPL Departemen</span>
                            </div>
                        </flux:menu.item>

                        @if (Auth::user()->tingkat < 3)

                            <flux:menu.item
                                @click="
                            $store.cpl?.setType(3);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-indigo-700 dark:text-indigo-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $dispatch('open-add-cpl-modal', { tingkatan: 3, parent: '{{ $parent ?? '' }}' });
                        "
                                class="text-xs sm:text-sm ml-8 cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-100 dark:hover:!bg-indigo-900/30 active:!bg-indigo-200 dark:active:!bg-indigo-900">
                                <flux:icon name="building-library"
                                    class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                                <div class="flex justify-between items-center w-full">
                                    <span class="mr-7 whitespace-nowrap">CPL Fakultas</span>
                                </div>
                            </flux:menu.item>

                            @if (Auth::user()->tingkat < 2)
                                <flux:menu.item
                                    @click="
                            $store.cpl?.setType(4);
                            $store.cpl?.setEdit(0);
                            $store.cpl?.setFlyout({{ $isFlyout ?? false }});
                            $store.cpl?.setColor('text-red-700 dark:text-red-400');
                            $store.cpl?.reset(1);
                            $flux.modal('cpl-modal').show();
                            $dispatch('open-add-cpl-modal', { tingkatan: 4, parent: '{{ $parent ?? '' }}' });
                        "
                                    class="text-xs sm:text-sm ml-8 cursor-pointer !text-red-600 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900">
                                    <flux:icon name="globe-alt" class="!text-red-600 dark:!text-red-400 mr-2 h-4 w-4" />
                                    <div class="flex justify-between items-center w-full">
                                        <span class="mr-7 whitespace-nowrap">CPL Universitas</span>
                                    </div>
                                </flux:menu.item>
                            @endif
                        @endif
                    @endif

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
                            $dispatch('open-add-cpmk-modal', { parent: '{{ $parent ?? '' }}' });
                        "
                        class="text-xs sm:text-sm cursor-pointer !text-violet-600 dark:!text-violet-400 hover:!bg-violet-100 dark:hover:!bg-violet-900/30 active:!bg-violet-200 dark:active!bg-violet-900">
                        <flux:icon name="academic-cap" class="!text-violet-600 dark:!text-violet-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7 whitespace-nowrap">CPMK</span>
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
                            $dispatch('open-add-scpmk-modal', { parent: '{{ $parent ?? '' }}' });
                        "
                        class="text-xs sm:text-sm cursor-pointer !text-fuchsia-600 dark:!text-fuchsia-400 hover:!bg-fuchsia-100 dark:hover:!bg-fuchsia-900/30 active:!bg-fuchsia-200 dark:active:!bg-fuchsia-900">
                        <flux:icon name="academic-cap" class="!text-fuchsia-600 dark:!text-fuchsia-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7 whitespace-nowrap">Sub-CPMK</span>
                        </div>
                    </flux:menu.item>
                @endif

                @if (!($withCapaian ?? false))
                    @if ($typeXString == 'ref' || $typeXString == 'all')
                        {{-- Referensi --}}
                        <flux:menu.item
                            @click="
                                $store.ref?.setEdit(0);
                                $store.ref?.setFlyout({{ $isFlyout ?? false }});
                                $store.ref?.setColor('text-orange-700 dark:text-orange-400');
                                $store.ref?.reset(1);
                                $flux.modal('ref-modal').show();
                                $dispatch('open-add-ref-modal', { parent: '{{ $parent ?? '' }}' });
                            "
                            class="text-xs sm:text-sm cursor-pointer !text-orange-600 dark:!text-orange-400 hover:!bg-orange-100 dark:hover:!bg-orange-900/30 active:!bg-orange-200 dark:active:!bg-orange-900">
                            <flux:icon name="book-open" class="!text-orange-600 dark:!text-orange-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7 whitespace-nowrap">Referensi</span>
                            </div>
                        </flux:menu.item>
                    @endif

                    @if ($typeXString == 'tim_dosen' || $typeXString == 'all')
                        <flux:menu.item
                            @click="
                                $store.tim_dosen?.setEdit(0);
                                $store.tim_dosen?.setFlyout({{ $isFlyout ?? false }});
                                $store.tim_dosen?.setColor('text-blue-700 dark:text-blue-400');
                                $store.tim_dosen?.reset(1);
                                $flux.modal('tim-dosen-modal').show();
                                $dispatch('open-add-tim-dosen-modal', { parent: '{{ $parent ?? '' }}' });
                            "
                            class="text-xs sm:text-sm cursor-pointer !text-blue-600 dark:!text-blue-400 hover:!bg-blue-100 dark:hover:!bg-blue-900/30 active:!bg-blue-200 dark:active:!bg-blue-900">
                            <flux:icon name="user-group" class="!text-blue-600 dark:!text-blue-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7 whitespace-nowrap">Tim Dosen</span>
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
                            $dispatch('open-add-user-modal', { role: 'Dosen', parent: '{{ $parent ?? '' }}' });
                        "
                            class="text-xs sm:text-sm cursor-pointer !text-lime-600 dark:!text-lime-400 hover:!bg-lime-100 dark:hover:!bg-lime-900/30 active:!bg-lime-200 dark:active:!bg-lime-900">
                            <flux:icon name="briefcase" class="!text-lime-600 dark:!text-lime-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7 whitespace-nowrap">Dosen</span>
                            </div>
                        </flux:menu.item>
                    @endif
                @endif

            </flux:menu>
        </flux:dropdown>
    </div>
@endif
