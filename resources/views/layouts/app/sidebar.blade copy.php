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

        <flux:sidebar x-cloak
            class="flux-sidebar-custom overflow-hidden border-e
            bg-[var(--main-color)] border-[var(--border-main-color)]
            flex flex-col">

            {{-- Header Logo & Toggle --}}
            <div class="flex items-center h-10 mt-2 mx-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                    <x-app-logo />
                </a>
            </div>


            <nav class="flex-1 space-y-1 no-scrollbar" x-data="{ openObeMenu: {{ request()->routeIs('rps-management') ? 'true' : 'false' }} }">
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
                        {{-- Render Dropdown Menu OBE --}}
                        @php
                            $subMenus = [
                                ['label' => 'RPS', 'url' => route('rps-management'), 'param' => null],
                                [
                                    'label' => 'CPMK',
                                    'url' => route('rps-management', ['switchTable' => 'cpmk']),
                                    'param' => 'cpmk',
                                ],
                                [
                                    'label' => 'Sub-CPMK',
                                    'url' => route('rps-management', ['switchTable' => 'scpmk']),
                                    'param' => 'scpmk',
                                ],
                                [
                                    'label' => 'CPL',
                                    'url' => route('rps-management', ['switchTable' => 'cpl']),
                                    'param' => 'cpl',
                                ],
                                [
                                    'label' => 'Referensi',
                                    'url' => route('rps-management', ['switchTable' => 'ref']),
                                    'param' => 'ref',
                                ],
                                [
                                    'label' => 'Dosen',
                                    'url' => route('rps-management', ['switchTable' => 'dosen', 'sortField' => 'name']),
                                    'param' => 'dosen',
                                ],
                            ];

                            $isObeActive = request()->routeIs('rps-management');
                        @endphp

                        <div class="relative mx-1">

                            {{-- JIKA SIDEBAR MENGECIL: Bungkus dengan Flux Dropdown asli --}}
                            <template x-if="!expanded">
                                <flux:dropdown position="right" align="start">
                                    <button type="button" ::class="expanded ? 'justify-between' : 'justify-start'"
                                        class="cursor-pointer w-full flex items-center text-xs p-2 rounded-lg transition-all duration-300 {{ $isObeActive ? 'bg-white/20 text-[var(--main-text)]' : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
                                        title="{{ $item['label'] }}">

                                        <div class="flex items-center">
                                            <flux:icon :name="$item['icon']" variant="outline"
                                                class="w-4 h-4 shrink-0" />
                                        </div>
                                    </button>

                                    <flux:menu class="w-44 bg-neutral-800 border border-neutral-700/50 shadow-xl p-1">
                                        <flux:menu.heading
                                            class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase px-2 py-1">
                                            OBE Menu</flux:menu.heading>
                                        <flux:separator class="bg-neutral-700/50 my-1" />

                                        @foreach ($subMenus as $sub)
                                            @php
                                                $isCurrentRouteObe = request()->routeIs('rps-management');
                                                $isActive =
                                                    $isCurrentRouteObe &&
                                                    (request('switchTable') === $sub['param'] ||
                                                        ($sub['param'] === null && !request()->has('switchTable')));
                                            @endphp
                                            <flux:menu.item :href="$sub['url']" wire:navigate
                                                class="text-xs rounded-md {{ $isActive ? 'bg-amber-500/20 text-amber-400 font-semibold border-l-2 border-amber-500 pl-2' : 'text-neutral-300 hover:bg-white/10 hover:text-white' }}">
                                                {{ $sub['label'] }}
                                            </flux:menu.item>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>
                            </template>

                            {{-- JIKA SIDEBAR LEBAR: Tampilkan Button biasa tanpa intervensi Flux --}}
                            <template x-if="expanded">
                                <button type="button" @click="openObeMenu = !openObeMenu"
                                    class="cursor-pointer w-full flex items-center justify-between text-xs p-2 rounded-lg transition-all duration-300 {{ $isObeActive ? 'bg-white/10 text-[var(--main-text)]' : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
                                    title="{{ $item['label'] }}">

                                    <div class="flex items-center overflow-hidden">
                                        <flux:icon :name="$item['icon']" variant="outline" class="w-4 h-4 shrink-0" />

                                        {{-- Perbaikan arah transisi: Bergeser dan menghilang ke kiri (-translate-x-4) --}}
                                        <span x-show="expanded" x-cloak
                                            x-transition:enter="transition-all duration-300 ease-out"
                                            x-transition:enter-start="opacity-0 -translate-x-4"
                                            x-transition:enter-end="opacity-100 translate-x-0"
                                            x-transition:leave="transition-all duration-200 ease-in"
                                            x-transition:leave-start="opacity-100 translate-x-0"
                                            x-transition:leave-end="opacity-0 -translate-x-4"
                                            class="ml-3 whitespace-nowrap overflow-hidden text-ellipsis block">{{ $item['label'] }}</span>
                                    </div>

                                    <span x-show="expanded" x-cloak
                                        x-transition:enter="transition-all duration-300 ease-out"
                                        x-transition:leave="transition-all duration-100 ease-in"
                                        class="transition-transform duration-200 shrink-0"
                                        :class="{ 'rotate-180': openObeMenu }">
                                        <flux:icon name="chevron-down" variant="mini" class="w-3 h-3" />
                                    </span>
                                </button>
                            </template>

                            {{-- COLLAPSIBLE LIST MODE: Hanya merespon jika sidebar sedang lebar --}}
                            <div x-show="expanded && openObeMenu" x-cloak class="mt-1 space-y-1 pl-4 w-full">
                                @foreach ($subMenus as $sub)
                                    @php
                                        $isCurrentRouteObe = request()->routeIs('rps-management');
                                        $isActive =
                                            $isCurrentRouteObe &&
                                            (request('switchTable') === $sub['param'] ||
                                                ($sub['param'] === null && !request()->has('switchTable')));
                                    @endphp
                                    <a href="{{ $sub['url'] }}" wire:navigate
                                        class="block text-[11px] p-2 rounded-md transition-all {{ $isActive ? 'bg-amber-500/20 text-amber-400 font-semibold border-l-2 border-amber-500 pl-3 shadow-sm' : 'text-[var(--main-text)]/70 hover:bg-white/10 hover:text-[var(--main-text)] pl-2' }}">
                                        {{ $sub['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>


            <div x-show="isDesktop" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
                class="mb-4 mx-1 flex justify-end">
                <button type="button" @click="toggleExpanded()"
                    class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[var(--main-text)]">
                    <span class="transition-all" :class="expanded ? 'rotate-[-180deg]' : ''">
                        <flux:icon name="chevron-double-right" variant="mini" class="w-6 h-6 text-[var(--main-text)]" />
                    </span>
                </button>
            </div>

            <flux:spacer />



            <div class="relative h-16 w-full flex items-center">

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
