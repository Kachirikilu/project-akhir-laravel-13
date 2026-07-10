<flux:sidebar
    class="flux-sidebar-custom overflow-hidden border-e
                    bg-[var(--main-color)] border-[var(--border-main-color)]
                    flex flex-col min-h-0">

    {{-- Header Logo & Toggle --}}
    <div class="flex items-center h-10 mt-2 mx-1 z-20">
        <a href="#" @click.prevent="window.location.reload()" class="flex items-center gap-2">
            <x-app-logo />
        </a>
    </div>
    {{-- <div class="absolute inset-0 z-0 bg-black" 
        style="background-image: url('/wallpapers/my-waguri.png'); 
                background-size: cover; 
                background-position: center;
                filter: brightness(0.5);
                opacity: 0.2;">
    </div> --}}

    <div x-show="$store.theme_manager.activeWallpaper !== null"
        class="absolute inset-0 z-0 bg-cover bg-center transition-all duration-300"
        :style="{
            'background-image': 'url(' + $store.theme_manager.activeWallpaper + ')',
            'opacity': $store.theme_manager.opacity,
            'filter': 'brightness(' + $store.theme_manager.brightness + ')'
        }">
    </div>
    <nav x-data="{
        openProdiMenu: {{ request()->routeIs('program-studi-management', 'capaian-management', 'rps-capaian-management') ? 'true' : 'false' }},
        openOBEMenu: {{ request()->routeIs('obe-management') ? 'true' : 'false' }},
        openJadwalMenu: {{ request()->routeIs('jadwal-mahasiswa', 'sesi-mahasiswa') ? 'true' : 'false' }},
        openKelasMenu: {{ request()->routeIs('kelas-management', 'jadwal-management', 'sesi-management') ? 'true' : 'false' }},
        openRpsNilaiMenu: {{ request()->routeIs('nilai-mahasiswa', 'rps-mahasiswa') ? 'true' : 'false' }},
        openNilaiMenu: {{ request()->routeIs('nilai-management', 'nilai-mahasiswa-management', 'rps-mahasiswa-management', 'rps-capaian-mahasiswa-management') ? 'true' : 'false' }},
    
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
        }
    }" class="flex-1 z-20 min-h-0 overflow-y-auto overflow-x-hidden space-y-1 scrollbar-tiny">
        @php
            $user = Auth::user();
            $lastKelas = session('kelas.last');
            $lastSesi = session('kelas.last_sesi');

            // dd($lastKelas, $lastSesi);

            $allNavItems = [
                [
                    'type' => 'link',
                    'icon' => 'squares-2x2',
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
                [
                    'type' => 'dropdown-prodi',
                    'icon' => 'academic-cap',
                    'label' => 'Study Program',
                    'roles' => ['admin'],
                ],
                [
                    'type' => 'link',
                    'icon' => 'academic-cap',
                    'route' => 'program-studi-dosen',
                    'label' => 'Study Program',
                    'roles' => ['dosen'],
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
                [
                    'type' => 'dropdown-jadwal',
                    'icon' => 'calendar-days',
                    'route' => 'jadwal-mahasiswa',
                    'label' => 'Jadwal Kelas',
                    'roles' => ['mahasiswa'],
                    'active_routes' => ['jadwal-mahasiswa', 'sesi-mahasiswa'],
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
                    'type' => 'dropdown-rps-nilai',
                    'icon' => 'chart-pie',
                    'route' => 'nilai-management',
                    'label' => 'Nilai Saya',
                    'roles' => ['mahasiswa'],
                ],
                [
                    'type' => 'dropdown-nilai',
                    'icon' => 'chart-pie',
                    'route' => 'nilai-management',
                    'label' => 'Nilai Mahasiswa',
                    'roles' => ['admin', 'dosen'],
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
                                : 'text-[var(--main-text)]/80 hover:bg-white/10 active:bg-white/20 hover:text-[var(--main-text)] active:text-[var(--main-text)]/90 active:bg-white/20 active:text-[var(--main-text)]/90' }}"
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
                    $currentTable = (string) request()->route('switchTable');
                    $isProdiActive =
                        request()->routeIs('program-studi-management') ||
                        request()->routeIs('capaian-management') ||
                        request()->routeIs('rps-capaian-management');

                    $prodiHistory = session('prodi.history', []);
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

                    // dump($currentTable);

                    $subMenus = [
                        [
                            'label' => 'Program Studi',
                            'url' => route('program-studi-management', [
                                'switchTable' => '',
                            ]),
                            'param' => 'default-null',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-emerald-600 dark:text-emerald-400',
                            'active' => request()->routeIs('program-studi-management') && $currentTable,
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

                    $isSubMenu = ['default-null', 'departemen', 'fakultas'];
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
                            'label' => 'Tim Dosen',
                            'url' => route('obe-management', ['switchTable' => 'tim-dosen']),
                            'param' => 'tim-dosen',
                            'icon' => 'user-group',
                            'color' => 'text-blue-600 dark:text-blue-400',
                            'active' => $isOBEActive && $currentTable === 'tim-dosen',
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

                    $isSubMenu = ['rps', 'cpmk', 'sub-cpmk', 'cpl', 'referensi', 'tim-dosen', 'dosen'];
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
                            'url' => route('jadwal-mahasiswa'),
                            'param' => 'jadwal-mahasiswa',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'active' => request()->routeIs('jadwal-mahasiswa'),
                            'active-sub' => request()->routeIs('sesi-mahasiswa'),
                        ],
                    ];

                    foreach ($sesiHistory as $sesi) {
                        $kodeKelas = $sesi['kode_kelas_url'] ?? ($sesi['kode_kelas'] ?? null);
                        $kodeJadwal = $sesi['kode_jadwal_short_url'] ?? ($sesi['kode_jadwal_short'] ?? null);
                        $jadwalId = $sesi['jadwal_id'] ?? null;
                        $switchTable = $sesi['switchTable'] ?? null;

                        $subMenus[] = [
                            'label' => $kodeKelas . '-' . $kodeJadwal,
                            'url' => route(
                                'sesi-mahasiswa',
                                array_filter([
                                    'kode_kelas' => $kodeKelas,
                                    'kode_jadwal_short' => $kodeJadwal,
                                    'jadwal_id' => $jadwalId,
                                    'switchTable' => $switchTable,
                                ]),
                            ),
                            'param' => 'sesi-mahasiswa',
                            'icon' => 'academic-cap',
                            'color' => 'text-indigo-600 dark:text-indigo-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('sesi-mahasiswa') &&
                                request()->route('kode_kelas') === $kodeKelas &&
                                request()->route('kode_jadwal_short') === $kodeJadwal,
                        ];
                    }

                    $openMenuVar = 'openJadwalMenu';
                    $isJadwalActive = request()->routeIs('jadwal-mahasiswa', 'sesi-mahasiswa');
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
            @elseif($item['type'] === 'dropdown-rps-nilai')
                @php
                    $rpsHistory = session('rps_mahasiswa_history.history', []);

                    $subMenus = [
                        [
                            'label' => 'Daftar Nilai Saya',
                            'url' => route('nilai-mahasiswa'),
                            'param' => 'nilai-mahasiswa',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'active' => request()->routeIs('nilai-mahasiswa') && !request()->route('ganjil_genap'),
                            'active-sub' => request()->routeIs('rps-mahasiswa'),
                        ],
                    ];

                    foreach ($rpsHistory as $rps) {
                        $nim = $rps['nim'] ?? null;
                        $ganjilGenap = $rps['ganjil_genap'] ?? null;
                        $akademik = $rps['tahun_akademik'] ?? null;
                        // Ganti kembali '/' menjadi '-' untuk kebutuhan parameter rute URL
                        $akademikUrl = str_replace('/', '-', $akademik);

                        if (!$ganjilGenap || !$akademik) {
                            continue;
                        }

                        $subMenus[] = [
                            // 'label' => 'RPS ' . ucfirst($ganjilGenap) . ' (' . $akademik . ')',
                            'label' => ucfirst($ganjilGenap) . ' ' . $akademik,
                            'url' => $rps['url'],
                            'param' => 'rps-mahasiswa',
                            'icon' => 'chart-bar',
                            'color' => 'text-indigo-600 dark:text-indigo-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('rps-mahasiswa') &&
                                request()->route('ganjil_genap') === $ganjilGenap &&
                                request()->route('akademik') === $akademikUrl,
                        ];
                    }

                    $openMenuVar = 'openRpsNilaiMenu';
                    $isRpsActive = request()->routeIs('nilai-mahasiswa', 'rps-mahasiswa');
                @endphp

                <div class="relative mr-2" @toggle-menu-obe.window="openRpsNilaiMenu = !openRpsNilaiMenu">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="Nilai Saya"
                        triggerRef="rpsNilaiDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openRpsNilaiMenu"
                        trigger="rpsNilaiDropdownTrigger" :active="$isRpsActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar" />
                </div>
            @elseif($item['type'] === 'dropdown-nilai')
                @php
                    $currentTable = (string) request()->route('switchTable');
                    $isNilaiActive = request()->routeIs(
                        'nilai-management',
                        'nilai-mahasiswa-management',
                        'rps-mahasiswa-management',
                        'rps-capaian-mahasiswa-management',
                    );

                    $nilaiHistory = session('nilai.history', []);
                    $rpsHistory = session('rps_nilai.history', []);
                    $capaianHistory = session('rps_capaian_mahasiswa.history', []);

                    // Kelompokkan RPS per Mahasiswa
                    $groupedRps = [];
                    foreach ($rpsHistory as $rps) {
                        $nim = $rps['nim'] ?? null;
                        if ($nim) {
                            $groupedRps[$nim][] = $rps;
                        }
                    }

                    // --- MENU UTAMA: MAHASISWA ---
                    $subMenus = [
                        [
                            'label' => 'Daftar Mahasiswa',
                            'url' => route('nilai-management'),
                            'param' => '',
                            'icon' => 'user-group',
                            'color' => 'text-emerald-600 dark:text-emerald-400',
                            'active' => request()->routeIs('nilai-management') && ($currentTable == 'mahasiswa' || $currentTable !== 'rps'),

                            'active-sub' => request()->routeIs(
                                'nilai-mahasiswa-management',
                                'rps-mahasiswa-management',
                            ),
                        ],
                    ];

                    // Level 1 & 2: Data Mahasiswa
                    foreach ($nilaiHistory as $mhs) {
                        $nim = $mhs['nim'] ?? null;
                        if (!$nim) {
                            continue;
                        }

                        $subMenus[] = [
                            'label' => $nim,
                            'url' => $mhs['url'] ?? '#',
                            'param' => 'nilai-mahasiswa-management',
                            'icon' => 'user',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('nilai-mahasiswa-management') && request()->route('nim') == $nim,
                            'active-sub' =>
                                request()->routeIs('rps-mahasiswa-management') && request()->route('nim') == $nim,
                        ];

                        if (isset($groupedRps[$nim])) {
                            foreach ($groupedRps[$nim] as $rps) {
                                $subMenus[] = [
                                    'label' =>
                                        ucfirst($rps['ganjil_genap'] ?? '') . ' ' . ($rps['tahun_akademik'] ?? ''),
                                    'url' => $rps['url'] ?? '#',
                                    'param' => 'rps-mahasiswa-management',
                                    'icon' => 'chart-bar',
                                    'color' => 'text-indigo-600 dark:text-indigo-400',
                                    'level' => 2,
                                    'active' =>
                                        request()->routeIs('rps-mahasiswa-management') &&
                                        request()->route('nim') == $nim &&
                                        request()->route('akademik') ==
                                            str_replace('/', '-', $rps['tahun_akademik'] ?? ''),
                                ];
                            }
                        }
                    }

                    // --- MENU UTAMA: RPS ---
                    $subMenus[] = [
                        'label' => 'Daftar RPS',
                        'url' => route('nilai-management', ['switchTable' => 'rps']),
                        'param' => 'rps',
                        'icon' => 'book-open',
                        'color' => 'text-blue-600 dark:text-blue-400',
                        // Deteksi cerdas: Aktif jika di route rps ATAU jika route saat ini adalah capaian-mahasiswa-management
                        'active' =>
                            (request()->routeIs('nilai-management') && $currentTable === 'rps') ||
                            request()->routeIs('rps-capaian-mahasiswa-management'),
                        'active-sub' =>
                            request()->routeIs('rps-capaian-mahasiswa-management'),
                    ];

                    foreach ($capaianHistory as $kodeRps => $dataRps) {
                        $subMenus[] = [
                            'label' => $kodeRps,
                            'url' => $dataRps['url'] ?? '#',
                            'param' => 'rps-capaian-mahasiswa-management',
                            'icon' => 'document-text',
                            'color' => 'text-blue-500 dark:text-blue-300',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('rps-capaian-mahasiswa-management') &&
                                request()->route('kode_rps') === $kodeRps,
                        ];
                    }

                    $isSubMenu = ['mahasiswa', 'rps'];
                    $openMenuVar = 'openNilaiMenu';
                @endphp

                <div class="relative mr-2">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="Nilai Management"
                        :isActive="$isNilaiActive" :isSubMenu="$isSubMenu" triggerRef="nilaiDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openNilaiMenu"
                        trigger="nilaiDropdownTrigger" :active="$isNilaiActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar"
                        :isActive="$isNilaiActive" :isSubMenu="$isSubMenu" />
                </div>
            @endif
        @endforeach

        <div x-show="isDesktop" x-cloak x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
            class="mx-1 flex justify-end">
            <button type="button" @click="toggleExpanded()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 active:bg-white/50 active:bg-white/10 text-[var(--main-text)]">
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
