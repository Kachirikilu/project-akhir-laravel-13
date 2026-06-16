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
        openOBEMenu: {{ request()->routeIs('rps-management') ? 'true' : 'false' }},
        openJadwalMenu: {{ request()->routeIs('jadwal-mahasiswa', 'sesi-mahasiswa') ? 'true' : 'false' }},
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
                [
                    'type' => 'link',
                    'icon' => 'academic-cap',
                    'route' => 'program-studi-management',
                    'label' => 'Study Program',
                    'roles' => ['admin'],
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
                //     'route' => 'jadwal-mahasiswa',
                //     'label' => 'Jadwal Kelas',
                //     'roles' => ['mahasiswa'],
                // ],
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
                                : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
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
            @elseif ($item['type'] === 'dropdown-obe')
                @php
                    $currentTable = null;
                    // $currentTable = request()->route('switchTable');
                    // $currentTable = $this->switchTable ?? 'rps';
                    $isOBEActive = request()->routeIs('rps-management');

                    $subMenus = [
                        [
                            'label' => 'RPS',
                            'url' => route('rps-management', ['switchTable' => 'rps']),
                            'param' => 'rps',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-green-600 dark:text-green-400',
                            'active' => $isOBEActive && $currentTable === 'rps',
                        ],
                        [
                            'label' => 'CPL',
                            'url' => route('rps-management', ['switchTable' => 'cpl']),
                            'param' => 'cpl',
                            'icon' => 'document-text',
                            'color' => 'text-sky-600 dark:text-sky-400',
                            'active' => $isOBEActive && $currentTable === 'cpl',
                        ],
                        [
                            'label' => 'CPMK',
                            'url' => route('rps-management', ['switchTable' => 'cpmk']),
                            'param' => 'cpmk',
                            'icon' => 'academic-cap',
                            'color' => 'text-violet-600 dark:text-violet-400',
                            'active' => $isOBEActive && $currentTable === 'cpmk',
                        ],
                        [
                            'label' => 'Sub-CPMK',
                            'url' => route('rps-management', ['switchTable' => 'sub-cpmk']),
                            'param' => 'sub-cpmk',
                            'icon' => 'academic-cap',
                            'color' => 'text-fuchsia-600 dark:text-fuchsia-400',
                            'active' => $isOBEActive && $currentTable === 'sub-cpmk',
                        ],
                        [
                            'label' => 'Referensi',
                            'url' => route('rps-management', ['switchTable' => 'referensi']),
                            'param' => 'referensi',
                            'icon' => 'book-open',
                            'color' => 'text-orange-600 dark:text-orange-400',
                            'active' => $isOBEActive && $currentTable === 'referensi',
                        ],
                        [
                            'label' => 'Dosen',
                            'url' => route('rps-management', ['switchTable' => 'dosen']),
                            'param' => 'dosen',
                            'icon' => 'briefcase',
                            'color' => 'text-lime-600 dark:text-lime-400',
                            'active' => $isOBEActive && $currentTable === 'dosen',
                        ],
                    ];

                    $openMenuVar = 'openOBEMenu';
                    $isMenuIndukActive = $isOBEActive;
                @endphp

                <div class="relative mr-2">
                    <x-livewire::navigation.partial.dropdown-level-button :subMenus="$subMenus" title="OBE Management"
                        triggerRef="obeDropdownTrigger" />
                    <x-livewire::navigation.partial.main-button :item="$item" menu="openOBEMenu"
                        trigger="obeDropdownTrigger" :active="$isOBEActive" />
                    <x-livewire::navigation.partial.navbar-level-button :subMenus="$subMenus" :openMenuVar="$openMenuVar" />
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
                        $kodeKelas = $sesi['kode_kelas'];
                        $kodeJadwal = $sesi['kode_jadwal_url'];
                        $jadwalId = $sesi['jadwal_id'] ?? null;
                        $switchTable = $sesi['switchTable'] ?? null;

                        $subMenus[] = [
                            'label' => $kodeKelas . '-' . $kodeJadwal,
                            'url' => route(
                                'sesi-mahasiswa',
                                array_filter([
                                    'kode_kelas' => $kodeKelas,
                                    'kode_jadwal_url' => $kodeJadwal,
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
                                request()->route('kode_jadwal_url') === $kodeJadwal,
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
                        $kodeKelas = $sesi['kode_kelas'];
                        $kodeJadwal = $sesi['kode_jadwal_url'];
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
                        $kodeKelas = $kelas['kode_kelas'];

                        $subMenus[] = [
                            'label' => 'Kelas ' . $kodeKelas,
                            'url' => $kelas['url'],
                            'param' => 'jadwal-management',
                            'icon' => 'clipboard-document-list',
                            'color' => 'text-amber-600 dark:text-amber-400',
                            'level' => 1,
                            'active' =>
                                request()->routeIs('jadwal-management') && request()->route('kode_kelas') === $kodeKelas,
                            'active-sub' =>
                                request()->routeIs('sesi-management') && request()->route('kode_kelas') === $kodeKelas,
                        ];

                        if (isset($groupedJadwal[$kodeKelas])) {
                            foreach ($groupedJadwal[$kodeKelas] as $sesi) {
                                $subMenus[] = [
                                    'label' => $sesi['kode_jadwal_url'],
                                    'url' => $sesi['url'],
                                    'param' => 'sesi-management',
                                    'icon' => 'academic-cap',
                                    'color' => 'text-indigo-600 dark:text-indigo-400',
                                    'level' => 2,
                                    'active' =>
                                        request()->routeIs('sesi-management') &&
                                        request()->route('kode_kelas') === $sesi['kode_kelas'] &&
                                        request()->route('kode_jadwal_url') === $sesi['kode_jadwal_url'],
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
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[var(--main-text)]">
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
