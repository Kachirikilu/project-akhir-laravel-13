<flux:sidebar
    class="flux-sidebar-custom overflow-hidden border-e
                    bg-[var(--main-color)] border-[var(--border-main-color)]
                    flex flex-col min-h-0">

    {{-- Header Logo & Toggle --}}
    <div class="flex items-center h-10 mt-2 mx-1">
        <a href="#" @click.prevent="window.location.reload()" class="flex items-center gap-2">
            <x-app-logo />
        </a>
    </div>


    <nav x-data="{
        openProdiMenu: {{ request()->routeIs('program-studi-management', 'capaian-management', 'rps-capaian-management') ? 'true' : 'false' }},
        openOBEMenu: {{ request()->routeIs('obe-management') ? 'true' : 'false' }},
        openJadwalMenu: {{ request()->routeIs('jadwal-kelas', 'sesi-jadwal-kelas') ? 'true' : 'false' }},
        openKelasMenu: {{ request()->routeIs('kelas-management', 'jadwal-management', 'sesi-management') ? 'true' : 'false' }},
    
        init() {
            this.$nextTick(() => {
                const savedScroll = sessionStorage.getItem('sidebar-scroll');
    
                if (savedScroll !== null) {
                    this.$el.scrollTop = parseInt(savedScroll);
                }
            });
    
            this.$el.addEventListener('scroll', () => {
                sessionStorage.setItem(
                    'sidebar-scroll',
                    this.$el.scrollTop
                );
            });

            {{-- window.addEventListener('refresh-layout-sidebar', () => {
                Livewire.navigate(window.location.href);
            }); --}}

            {{-- window.addEventListener('refresh-layout-sidebar', () => {
                if (window.Livewire) {
                    const firstComponent = Object.values(Livewire.components)[0];
                    if (firstComponent) {
                        firstComponent.$wire.$refresh();
                    }
                }
            }); --}}
        }
    }" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden space-y-1 scrollbar-tiny">
        @php
            $user = Auth::user();
            $lastKelas = session('kelas.last');
            $lastSesi = session('kelas.last_sesi');

            // dd($lastKelas, $lastSesi);

            $allNavItems = [
                [
                    'type' => 'link',
                    'icon' => 'home',
                    'route' => 'dashboard',
                    'label' => 'Dashboard',
                    'roles' => ['admin', 'dosen', 'mahasiswa'],
                ],
                [
                    'type' => 'link',
                    'icon' => 'users',
                    'route' => 'user-management',
                    'label' => 'User Management',
                    'roles' => ['admin'],
                ],
                // [
                //     'type' => 'link',
                //     'icon' => 'academic-cap',
                //     'route' => 'program-studi-management',
                //     'label' => 'Study Program',
                //     'roles' => ['admin'],
                // ],
                [
                    'type' => 'dropdown-prodi',
                    'icon' => 'academic-cap',
                    'label' => 'Study Program',
                    'roles' => ['admin'],
                ],
                [
                    'type' => 'link',
                    'icon' => 'chart-pie',
                    'route' => 'nilai-management',
                    'label' => 'Nilai Mahasiswa',
                    'roles' => ['admin', 'dosen'],
                ],
                [
                    'type' => 'link',
                    'icon' => 'rectangle-stack',
                    'route' => 'mata-kuliah-management',
                    'label' => 'Mata Kuliah',
                    'roles' => ['admin', 'dosen'],
                ],
                [
                    'type' => 'dropdown-obe',
                    'icon' => 'clipboard-document-list',
                    'label' => 'OBE Management',
                    'roles' => ['admin', 'dosen'],
                ],
                // [
                //     'type' => 'link',
                //     'icon' => 'calendar-days',
                //     'route' => 'jadwal-kelas',
                //     'label' => 'Jadwal Kelas',
                //     'roles' => ['mahasiswa'],
                // ],
                [
                    'type' => 'dropdown-jadwal',
                    'icon' => 'calendar-days',
                    'route' => 'jadwal-kelas',
                    'label' => 'Jadwal Kelas',
                    'roles' => ['mahasiswa'],
                    'active_routes' => ['jadwal-kelas', 'sesi-jadwal-kelas'],
                ],
                [
                    'type' => 'dropdown-kelas',
                    'icon' => 'rectangle-group',
                    'route' => 'kelas-management',
                    'label' => 'Kelas Management',
                    'roles' => ['admin', 'dosen', 'mahasiswa'],
                    'active_routes' => ['kelas-management', 'jadwal-management', 'sesi-management'],
                ],
                [
                    'type' => 'link',
                    'icon' => 'academic-cap',
                    'route' => 'nilai-mahasiswa',
                    'label' => 'Nilai Kuliah',
                    'roles' => ['mahasiswa'],
                    'active_routes' => ['nilai-mahasiswa'],
                ],
            ];

            $navItems = array_filter($allNavItems, function ($item) use ($user) {
                if ($user->admin) {
                    return in_array('admin', $item['roles']);
                } elseif ($user->dosen) {
                    return in_array('dosen', $item['roles']);
                } elseif ($user->mahasiswa) {
                    return in_array('mahasiswa', $item['roles']);
                }
                return false;
            });
        @endphp

        @foreach ($navItems as $item)
            @if ($item['type'] === 'link')
                @php
                    $isActive = isset($item['active_routes'])
                        ? request()->routeIs(...$item['active_routes'])
                        : request()->routeIs($item['route']);
                @endphp
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="flex items-center text-xs mx-1 p-2 rounded-lg overflow-hidden transition-colors
                            {{ $isActive
                                ? 'bg-white/20 text-[var(--main-text)]'
                                : 'text-[var(--main-text)]/80 hover:bg-white/10 active:bg-white/20 hover:text-[var(--main-text)] active:text-[var(--main-text)]/90' }}"
                    title="{{ $item['label'] }}">
                    <flux:icon :name="$item['icon']" variant="outline" class="w-4 h-4 shrink-0" />
                    <span x-show="expanded" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition-all duration-200 ease-in"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-4"
                        class="ml-3 whitespace-nowrap overflow-hidden text-ellipsis block">{{ $item['label'] }}</span>
                </a>
            @elseif ($item['type'] === 'dropdown-prodi')
                @php
                    $currentTable = null;
                    $isProdiActive =
                        request()->routeIs('program-studi-management') ||
                        request()->routeIs('capaian-management') ||
                        request()->routeIs('rps-capaian-management');

                    $prodiHistory = session('capaian.history', []);
                    $cplHistory = session('cpl.history', []);

                    $groupedCpl = [];

                    foreach ($cplHistory as $cpl) {
                        $kodePr = $cpl['kode_pr'] ?? null;
                        $kodeCpl = $cpl['kode_cpl'] ?? null;

                        if (!$kodePr || !$kodeCpl) {
                            continue;
                        }

                        $groupedCpl[$kodePr][$kodeCpl] = $cpl;
                    }

                    foreach ($groupedCpl as $kodePr => $list) {
                        ksort($groupedCpl[$kodePr]);
                    }

                    $subMenus = [
                        [
                            'label' => 'Program Studi',
                            'url' => route('program-studi-management', [
                                'switchTable' => 'prodi',
                            ]),
                            'param' => 'prodi',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-emerald-600 dark:text-emerald-400',
                            'active' => $isProdiActive && $currentTable === 'prodi',
                            'active-sub' => request()->routeIs('capaian-management', 'rps-capaian-management'),
                        ],
                    ];

                    foreach ($prodiHistory as $prodi) {
                        $kodePr = $prodi['kode_pr'] ?? null;

                        if (!$kodePr) {
                            continue;
                        }

                        $subMenus[] = [
                            'label' => $kodePr,
                            'url' => $prodi['url'],
                            'param' => 'capaian-management',
                            'icon' => 'document-text',
                            'color' => 'text-cyan-600 dark:text-cyan-400',
                            'level' => 1,

                            'active' =>
                                request()->routeIs('capaian-management') && request()->route('kode_pr') === $kodePr,

                            'active-sub' =>
                                request()->routeIs('rps-capaian-management') && request()->route('kode_pr') === $kodePr,
                        ];

                        if (isset($groupedCpl[$kodePr])) {
                            foreach ($groupedCpl[$kodePr] as $cpl) {
                                $subMenus[] = [
                                    'label' => $cpl['kode_cpl'],
                                    'url' => $cpl['url'],
                                    'param' => 'rps-capaian-management',
                                    'icon' => 'academic-cap',
                                    'color' => 'text-indigo-600 dark:text-indigo-400',
                                    'level' => 2,

                                    'active' =>
                                        request()->routeIs('rps-capaian-management') &&
                                        request()->route('kode_pr') === $kodePr &&
                                        request()->route('kode_cpl') === $cpl['kode_cpl'],
                                ];
                            }
                        }
                    }

                    $subMenus[] = [
                        'label' => 'Departemen',
                        'url' => route('program-studi-management', [
                            'switchTable' => 'departemen',
                        ]),
                        'param' => 'departemen',
                        'icon' => 'book-open',
                        'color' => 'text-amber-600 dark:text-amber-400',
                        'active' => $isProdiActive && $currentTable === 'departemen',
                    ];

                    $subMenus[] = [
                        'label' => 'Fakultas',
                        'url' => route('program-studi-management', [
                            'switchTable' => 'fakultas',
                        ]),
                        'param' => 'fakultas',
                        'icon' => 'building-library',
                        'color' => 'text-indigo-600 dark:text-indigo-400',
                        'active' => $isProdiActive && $currentTable === 'fakultas',
                    ];

                    $isSubMenu = ['prodi', 'departemen', 'fakultas'];
                    $openMenuVar = 'openProdiMenu';
                    $isMenuIndukActive = $isProdiActive;
                @endphp

                <div class="relative mr-2">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus"
                        title="Program Studi Management" :isActive="$isProdiActive" :isSubMenu="$isSubMenu"
                        triggerRef="prodiDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openProdiMenu"
                        trigger="prodiDropdownTrigger" :active="$isProdiActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar"
                        :isActive="$isProdiActive" :isSubMenu="$isSubMenu" />
                </div>
            @elseif ($item['type'] === 'dropdown-obe')
                @php
                    $currentTable = null;
                    // $currentTable = request()->route('switchTable');
                    // $currentTable = $this->switchTable ?? 'rps';
                    $isOBEActive = request()->routeIs('obe-management');

                    $subMenus = [
                        [
                            'label' => 'RPS',
                            'url' => route('obe-management', ['switchTable' => 'rps']),
                            'param' => 'rps',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-green-600 dark:text-green-400',
                            'active' => $isOBEActive && $currentTable === 'rps',
                        ],
                        [
                            'label' => 'CPL',
                            'url' => route('obe-management', ['switchTable' => 'cpl']),
                            'param' => 'cpl',
                            'icon' => 'document-text',
                            'color' => 'text-sky-600 dark:text-sky-400',
                            'active' => $isOBEActive && $currentTable === 'cpl',
                        ],
                        [
                            'label' => 'CPMK',
                            'url' => route('obe-management', ['switchTable' => 'cpmk']),
                            'param' => 'cpmk',
                            'icon' => 'academic-cap',
                            'color' => 'text-violet-600 dark:text-violet-400',
                            'active' => $isOBEActive && $currentTable === 'cpmk',
                        ],
                        [
                            'label' => 'Sub-CPMK',
                            'url' => route('obe-management', ['switchTable' => 'sub-cpmk']),
                            'param' => 'sub-cpmk',
                            'icon' => 'academic-cap',
                            'color' => 'text-fuchsia-600 dark:text-fuchsia-400',
                            'active' => $isOBEActive && $currentTable === 'sub-cpmk',
                        ],
                        [
                            'label' => 'Referensi',
                            'url' => route('obe-management', ['switchTable' => 'referensi']),
                            'param' => 'referensi',
                            'icon' => 'book-open',
                            'color' => 'text-orange-600 dark:text-orange-400',
                            'active' => $isOBEActive && $currentTable === 'referensi',
                        ],
                        [
                            'label' => 'Dosen',
                            'url' => route('obe-management', ['switchTable' => 'dosen']),
                            'param' => 'dosen',
                            'icon' => 'briefcase',
                            'color' => 'text-lime-600 dark:text-lime-400',
                            'active' => $isOBEActive && $currentTable === 'dosen',
                        ],
                    ];

                    $isSubMenu = ['rps', 'cpmk', 'sub-cpmk', 'cpl', 'referensi', 'dosen'];
                    $openMenuVar = 'openOBEMenu';
                    $isMenuIndukActive = $isOBEActive;
                @endphp

                <div class="relative mr-2">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="OBE Management"
                        :isActive="$isOBEActive" :isSubMenu="$isSubMenu" triggerRef="obeDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openOBEMenu"
                        trigger="obeDropdownTrigger" :active="$isOBEActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar"
                        :isActive="$isOBEActive" :isSubMenu="$isSubMenu" />
                </div>
            @elseif($item['type'] === 'dropdown-jadwal')
                @php
                    $sesiHistory = session('jadwal_mahasiswa.history', []);

                    $subMenus = [
                        [
                            'label' => 'Daftar Jadwal Kelas',
                            'url' => route('jadwal-kelas'),
                            'param' => 'jadwal-kelas',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'active' => request()->routeIs('jadwal-kelas'),
                            'active-sub' => request()->routeIs('sesi-jadwal-kelas'),
                        ],
                    ];

                    foreach ($sesiHistory as $sesi) {
                        $kodeKelas = $sesi['kode_kelas_url'] ?? ($sesi['kode_kelas'] ?? null);
                        $kodeJadwal = $sesi['kode_jadwal_short_url'] ?? ($sesi['kode_jadwal_short'] ?? null);
                        $jadwalId = $sesi['kj_id'] ?? null;
                        $switchTable = $sesi['switchTable'] ?? null;

                        $subMenus[] = [
                            'label' => $kodeKelas . '-' . $kodeJadwal,
                            'url' => route(
                                'sesi-jadwal-kelas',
                                array_filter([
                                    'kode_kelas' => $kodeKelas,
                                    'kode_jadwal_short' => $kodeJadwal,
                                    'kj_id' => $jadwalId,
                                    'switchTable' => $switchTable,
                                ]),
                            ),
                            'param' => 'sesi-jadwal-kelas',
                            'icon' => 'academic-cap',
                            'color' => 'text-indigo-600 dark:text-indigo-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('sesi-jadwal-kelas') &&
                                request()->route('kode_kelas') === $kodeKelas &&
                                request()->route('kode_jadwal_short') === $kodeJadwal,
                        ];
                    }

                    $openMenuVar = 'openJadwalMenu';
                    $isJadwalActive = request()->routeIs('jadwal-kelas', 'sesi-jadwal-kelas');
                @endphp

                <div class="relative mr-2" @toggle-menu-obe.window="openJadwalMenu = !openJadwalMenu">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="Jadwal Mahasiswa"
                        triggerRef="jadwalDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openJadwalMenu"
                        trigger="jadwalDropdownTrigger" :active="$isJadwalActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar" />
                </div>
            @elseif($item['type'] === 'dropdown-kelas')
                @php
                    $kelasHistory = session('kelas.history', []);
                    $sesiHistory = session('jadwal.history', []);

                    $groupedJadwal = [];
                    foreach ($sesiHistory as $sesi) {
                        $kodeKelas = $sesi['kode_kelas_url'] ?? ($sesi['kode_kelas'] ?? null);
                        $kodeJadwal = $sesi['kode_jadwal_short_url'] ?? ($sesi['kode_jadwal_short'] ?? null);

                        if ($kodeKelas === null || $kodeJadwal === null) {
                            continue;
                        }

                        $groupedJadwal[$kodeKelas][$kodeJadwal] = $sesi;
                    }

                    foreach ($groupedJadwal as $kodeKelas => $daftarJadwal) {
                        ksort($groupedJadwal[$kodeKelas]);
                    }

                    $subMenus = [
                        [
                            'label' => 'Daftar Kelas',
                            'url' => route('kelas-management'),
                            'param' => 'kelas-management',
                            'icon' => 'rectangle-group',
                            'color' => 'text-emerald-600 dark:text-emerald-400',
                            'active' => request()->routeIs('kelas-management'),
                            'active-sub' => request()->routeIs('jadwal-management', 'sesi-management'),
                        ],
                    ];

                    foreach ($kelasHistory as $kelas) {
                        $kodeKelas = $kelas['kode_kelas_url'] ?? ($kelas['kode_kelas'] ?? null);

                        if ($kodeKelas === null) {
                            continue;
                        }

                        $subMenus[] = [
                            'label' => 'Kelas ' . $kodeKelas,
                            'url' => $kelas['url'],
                            'param' => 'jadwal-management',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('jadwal-management') &&
                                request()->route('kode_kelas') === $kodeKelas,
                            'active-sub' =>
                                request()->routeIs('sesi-management') && request()->route('kode_kelas') === $kodeKelas,
                        ];

                        if (isset($groupedJadwal[$kodeKelas])) {
                            foreach ($groupedJadwal[$kodeKelas] as $sesi) {
                                $subMenus[] = [
                                    'label' =>
                                        $sesi['kode_jadwal_short_url'] ?? ($sesi['kode_jadwal_short'] ?? 'Jadwal'),
                                    'url' => $sesi['url'],
                                    'param' => 'sesi-management',
                                    'icon' => 'academic-cap',
                                    'color' => 'text-indigo-600 dark:text-indigo-400',
                                    'level' => 2,
                                    'active' =>
                                        request()->routeIs('sesi-management') &&
                                        request()->route('kode_kelas') ===
                                            ($sesi['kode_kelas_url'] ?? ($sesi['kode_kelas'] ?? null)) &&
                                        request()->route('kode_jadwal_short') ===
                                            ($sesi['kode_jadwal_short_url'] ?? ($sesi['kode_jadwal_short'] ?? null)),
                                ];
                            }
                        }
                    }

                    $openMenuVar = 'openKelasMenu';
                    $isKelasActive = request()->routeIs('kelas-management', 'jadwal-management', 'sesi-management');
                @endphp

                <div class="relative mr-2" @toggle-menu-obe.window="openKelasMenu = !openKelasMenu">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="Kelas Management"
                        triggerRef="kelasDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openKelasMenu"
                        trigger="kelasDropdownTrigger" :active="$isKelasActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar" />

                </div>
            @endif
        @endforeach

        <div x-show="isDesktop" x-cloak x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
            class="mx-1 flex justify-end">
            <button type="button" @click="toggleExpanded()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 active:bg-white/50 text-[var(--main-text)]">
                <span class="transition-all" :class="expanded ? 'rotate-[-180deg]' : ''">
                    <flux:icon name="chevron-double-right" variant="mini" class="w-6 h-6 text-[var(--main-text)]" />
                </span>
            </button>
        </div>
    </nav>




    {{-- <flux:spacer /> --}}



    <div x-data="{ showColorMode: true }" @toggle-color-panel.window="showColorMode = $event.detail.open"
        class="relative h-4 w-full flex items-center transition-all mb-3 lg:mb-4 xl:mb-8"
        :style="`transition-delay: ${showColorMode ? '0ms' : '380ms'}`" :class="showColorMode ? 'mt-15' : 'mt-0'">

        <div class="absolute transition-all duration-400 ease-in-out"
            :style="`transition-delay: ${showColorMode ? (expanded ? '0ms' : '200ms') : '380ms'}`"
            :class="showColorMode ? (expanded ? '-translate-y-15 opacity-100' : 'translate-y-0 opacity-100') : ''">
            <x-livewire::navigation.dark-mode />
        </div>

        <div class="absolute transition-all duration-400 ease-in-out"
            :style="`transition-delay: ${showColorMode ? (expanded ? '200ms' : '0ms') : '250ms'}`"
            :class="showColorMode
                ?
                (expanded ?
                    'translate-x-0 translate-y-4 opacity-100' :
                    'translate-x-32 opacity-0') :
                'opacity-0 pointer-events-none translate-x-32 translate-y-4'">
            <x-livewire::navigation.color-mode />
        </div>
    </div>


    {{-- Profile --}}
    <div class="lg:pb-3 xl:pb-6">
        <livewire:navigation.profile-dropdown />
    </div>
</flux:sidebar>
