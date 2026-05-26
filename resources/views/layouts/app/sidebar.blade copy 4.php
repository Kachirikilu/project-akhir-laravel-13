<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --sidebar-width: 72px;
        }

        .sidebar-expanded {
            --sidebar-width: 256px;
        }

        .flux-sidebar-custom {
            width: var(--sidebar-width) !important;
            transition: width 0.3s ease !important;
            position: fixed !important;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 50;
            overflow-x: hidden !important;
        }

        .main-content {
            transition: padding-left 0.3s ease;
            width: 100%;
        }
    </style>
</head>

<body class="scrollbar-large min-h-screen bg-white dark:bg-zinc-900" :class="{ 'sidebar-expanded': expanded }"
    x-data="{
        expanded: $persist(false).as('sidebar_expanded'),
        init() {
            $watch('expanded', value => window.sidebarExpanded = value);
            window.sidebarExpanded = this.expanded;
        },
        expanded2: false,
        isDesktop: window.matchMedia('(min-width: 1024px)').matches,
    
        toggleExpanded() {
            this.expanded = !this.expanded;
            if (this.isDesktop) {
                this.expanded2 = this.expanded;
            }
        },
    
        init() {
            const media = window.matchMedia('(min-width: 1024px)');
            this.isDesktop = media.matches;
    
            this.expanded2 = this.expanded;
    
            media.addEventListener('change', (e) => {
                this.isDesktop = e.matches;
    
                if (!e.matches) {
                    this.expanded = false;
                } else {
                    this.expanded = this.expanded2;
                }
            });
        }
    }">



    <div x-show="isDesktop || (expanded && !isDesktop)" x-cloak
        x-transition:enter="transition transform duration-300 ease-in-out" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200 ease-in-out"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 transition-all duration-300"
        :class="isDesktop && !expanded ? 'w-[72px]' : 'w-[256px]'">

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


            <nav class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden space-y-1 scrollbar-thin"
                x-data="{
                    openObeMenu: {{ request()->routeIs('rps-management') ? 'true' : 'false' }}
                }">
                @php
                    $user = Auth::user();

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
                            'type' => 'dropdown',
                            'icon' => 'clipboard-document-list',
                            'label' => 'OBE Management',
                            'roles' => ['admin', 'dosen'],
                        ],
                        [
                            'type' => 'link',
                            'icon' => 'rectangle-group',
                            'route' => 'kelas-management',
                            'label' => 'Kelas Management',
                            'roles' => ['admin', 'dosen'],
                        ],
                    ];

                    $navItems = array_filter($allNavItems, function ($item) use ($user) {
                        if ($user->admin) {
                            return in_array('admin', $item['roles']);
                        } elseif ($user->dosen) {
                            return in_array('dosen', $item['roles']);
                        }
                        return false;
                    });
                @endphp

                {{-- Loop Semua Navigasi Berdasarkan Urutan Array --}}
                @foreach ($navItems as $item)
                    @if ($item['type'] === 'link')
                        {{-- Render Link Biasa --}}
                        <a href="{{ route($item['route']) }}" wire:navigate
                            class="flex items-center text-xs mx-1 p-2 rounded-lg transition-colors {{ request()->routeIs($item['route']) ? 'bg-white/20 text-[var(--main-text)]' : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
                            title="{{ !$item['label'] ? $item['label'] : '' }}">
                            <flux:icon :name="$item['icon']" variant="outline" class="w-4 h-4 shrink-0" />
                            <span x-show="expanded" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                                x-transition:enter-start="opacity-0 translate-x-4"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="transition-all duration-200 ease-in"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 translate-x-4"
                                class="ml-3 whitespace-nowrap overflow-hidden text-ellipsis block">{{ $item['label'] }}</span>
                        </a>
                    @elseif ($item['type'] === 'dropdown')
                        @php
                            $subMenus = [
                                [
                                    'label' => 'RPS',
                                    'url' => route('rps-management', ['switchTable' => 'rps']),
                                    'param' => 'rps',
                                ],
                                [
                                    'label' => 'CPMK',
                                    'url' => route('rps-management', ['switchTable' => 'cpmk']),
                                    'param' => 'cpmk',
                                ],
                                [
                                    'label' => 'Sub-CPMK',
                                    'url' => route('rps-management', ['switchTable' => 'sub-cpmk']),
                                    'param' => 'sub-cpmk',
                                ],
                                [
                                    'label' => 'CPL',
                                    'url' => route('rps-management', ['switchTable' => 'cpl']),
                                    'param' => 'cpl',
                                ],
                                [
                                    'label' => 'Referensi',
                                    'url' => route('rps-management', ['switchTable' => 'referensi']),
                                    'param' => 'referensi',
                                ],
                                [
                                    'label' => 'Dosen',
                                    'url' => route('rps-management', ['switchTable' => 'dosen']),
                                    'param' => 'dosen',
                                ],
                            ];

                            $isObeActive = request()->routeIs('rps-management');
                        @endphp

                        <div class="relative mr-2">

                            {{-- Flux dropdown hidden trigger --}}
                            <flux:dropdown position="right" align="start">
                                <button x-ref="obeDropdownTrigger" type="button" tabindex="-1" aria-hidden="true"
                                    class="absolute inset-0 opacity-0 pointer-events-none"></button>

                                {{-- 🚀 REAKTIVITAS 1: Flux Menu mendengarkan Event Global --}}
                                <flux:menu x-data="{ currentDropdownTable: '{{ request()->route('switchTable') ?? 'rps' }}' }"
                                    @table-switched.window="currentDropdownTable = $event.detail.switchTable === '' ? 'rps' : $event.detail.switchTable"
                                    class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

                                    <flux:menu.heading>OBE Management</flux:menu.heading>

                                    <flux:separator class="my-1" />

                                    @foreach ($subMenus as $sub)
                                        @php
                                            $iconName = 'academic-cap';
                                            $colorClasses = 'text-amber-600 dark:text-amber-400';

                                            if ($sub['param'] === 'rps') {
                                                $iconName = 'clipboard-document-list';
                                                $colorClasses = 'text-emerald-600 dark:text-emerald-400';
                                            } elseif ($sub['param'] === 'sub-cpmk') {
                                                $iconName = 'academic-cap';
                                                $colorClasses = 'text-indigo-600 dark:text-indigo-400';
                                            } elseif ($sub['param'] === 'cpl') {
                                                $iconName = 'document-text';
                                                $colorClasses = 'text-red-600 dark:text-red-400';
                                            } elseif ($sub['param'] === 'referensi') {
                                                $iconName = 'book-open';
                                                $colorClasses = 'text-fuchsia-600 dark:text-fuchsia-400';
                                            } elseif ($sub['param'] === 'dosen') {
                                                $iconName = 'briefcase';
                                                $colorClasses = 'text-lime-600 dark:text-lime-400';
                                            }
                                        @endphp

                                        <flux:menu.item :href="$sub['url']" wire:navigate
                                            class="group overflow-hidden rounded-md cursor-pointer !shadow-none !border-none hover:!bg-transparent focus:!bg-transparent active:!bg-transparent">

                                            {{-- KUNCI PERBAIKAN 1: Pengecekan aktif murni dijalankan di sisi browser oleh Alpine --}}
                                            <span
                                                :class="((currentDropdownTable === '{{ $sub['param'] }}' || (
                                                    currentDropdownTable === '' && '{{ $sub['param'] }}'
                                                    === 'rps')) && {{ $isObeActive ? 'true' : 'false' }}) ?
                                                'bg-white/20 text-[var(--main-text)] font-semibold border-l-2 border-[var(--border-main-color)]' :
                                                'text-[var(--contrast-main-text)] hover:bg-white/10'"
                                                class="flex items-center rounded-md w-full h-full text-xs px-3 py-1.5 transition-colors">

                                                <flux:icon :name="$iconName"
                                                    class="{{ $colorClasses }} mr-2 h-4 w-4 shrink-0" />

                                                <span>{{ $sub['label'] }}</span>
                                            </span>

                                        </flux:menu.item>
                                    @endforeach
                                </flux:menu>
                            </flux:dropdown>

                            {{-- SINGLE BUTTON (seperti link) --}}
                            <button type="button"
                                @click="expanded ? openObeMenu = !openObeMenu : $refs.obeDropdownTrigger.click()"
                                class="cursor-pointer flex items-center text-xs mx-1 p-2 rounded-lg transition-colors w-full
                                {{ $isObeActive
                                    ? 'bg-white/20 text-[var(--main-text)]'
                                    : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
                                title="{{ $item['label'] }}">

                                <div class="flex items-center justify-between overflow-hidden w-full">
                                    <flux:icon :name="$item['icon']" variant="outline" class="w-4 h-4 shrink-0" />

                                    <div x-show="expanded" x-cloak
                                        x-transition:enter="transition-all duration-300 ease-out"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0"
                                        x-transition:leave="transition-all duration-200 ease-in"
                                        x-transition:leave-start="opacity-100 translate-x-0"
                                        x-transition:leave-end="opacity-0 translate-x-4"
                                        class="flex flex-1 items-center justify-between overflow-hidden ml-3">

                                        <span
                                            class="whitespace-nowrap overflow-hidden text-ellipsis block text-left flex-1">
                                            {{ $item['label'] }}
                                        </span>

                                        <span class="transition-transform duration-200 shrink-0 ml-auto"
                                            :class="{ 'rotate-180': openObeMenu }">
                                            <flux:icon name="chevron-down" class="w-3 h-3" />
                                        </span>
                                    </div>
                                </div>
                            </button>

                            {{-- Submenu Biasa (Saat Sidebar Terbuka Lebar) --}}
                            <div x-data="{ currentTable: '{{ request()->route('switchTable') ?? 'rps' }}' }"
                                @table-switched.window="currentTable = $event.detail.switchTable === '' ? 'rps' : $event.detail.switchTable"
                                x-show="expanded && openObeMenu" x-cloak
                                x-transition:enter="transition-all duration-300 ease-out"
                                x-transition:enter-start="opacity-0 -translate-y-4 max-h-0 origin-top"
                                x-transition:enter-end="opacity-100 translate-y-0 max-h-[500px] origin-top"
                                x-transition:leave="transition-all duration-200 ease-in"
                                x-transition:leave-start="opacity-100 translate-y-0 max-h-[500px] origin-top"
                                x-transition:leave-end="opacity-0 -translate-y-4 max-h-0 origin-top"
                                class="mt-1 space-y-1 pl-4 w-full ml-1 overflow-hidden">

                                @foreach ($subMenus as $sub)
                                    <a href="{{ $sub['url'] }}" wire:navigate
                                        :class="((currentTable === '{{ $sub['param'] }}' || (currentTable === '' &&
                                            '{{ $sub['param'] }}'
                                            === 'rps')) && {{ $isObeActive ? 'true' : 'false' }}) ?
                                        'bg-white/20 text-white font-semibold border-[var(--main-text)] pl-3 shadow-sm' :
                                        'text-[var(--main-text)]/70 hover:bg-white/10 hover:text-[var(--main-text)] border-transparent pl-4'"
                                        class="block text-[11px] p-2 rounded-md border-l-4 transition-all duration-300 ease-in-out transform active:scale-95">
                                        {{ $sub['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <div x-show="isDesktop" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition-all duration-200 ease-in"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4" class="mx-1 flex justify-end">
                    <button type="button" @click="toggleExpanded()"
                        class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[var(--main-text)]">
                        <span class="transition-all" :class="expanded ? 'rotate-[-180deg]' : ''">
                            <flux:icon name="chevron-double-right" variant="mini"
                                class="w-6 h-6 text-[var(--main-text)]" />
                        </span>
                    </button>
                </div>
            </nav>




            {{-- <flux:spacer /> --}}



            <div class="relative h-16 w-full flex items-center mt-10">

                <div class="absolute transition-all duration-400 ease-in-out"
                    :style="expanded ? 'transition-delay: 0ms' : 'transition-delay: 200ms'"
                    :class="expanded ? '-translate-y-15 opacity-100' : 'translate-y-0 opacity-100'">
                    <x-livewire::navigation.dark-mode />
                </div>

                <div class="absolute transition-all duration-400 ease-in-out"
                    :style="expanded ? 'transition-delay: 200ms' : 'transition-delay: 0ms'"
                    :class="expanded ? 'translate-y-4 translate-x-0 opacity-100' :
                        'translate-x-12 opacity-0 pointer-events-none'">
                    <x-livewire::navigation.color-mode />
                </div>
            </div>


            {{-- Profile --}}
            <div class="pb-6">
                <livewire:navigation.profile-dropdown />
            </div>
        </flux:sidebar>
    </div>

    <livewire:navigation.mobile-profile-dropdown />


    <main x-cloak class="min-h-screen transition-all duration-300 w-full"
        :style="isDesktop ? `padding-left: var(--sidebar-width)` : ''">
        <div class="py-2 lg:py-6 px-0 2xl:px-6 transition-all duration-300"
            :class="expanded ? 'md:px-0 xl:px-2' : 'md:px-2 lg:px-4 xl:px-4'">
            {{ $slot }}
        </div>
    </main>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
