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
        expanded2: false,
        isDesktop: window.matchMedia('(min-width: 1024px)').matches,
    
        toggleExpanded() {
            this.expanded = !this.expanded;
            if (this.isDesktop) {
                this.expanded2 = this.expanded;
            }
        },
        init() {
            $watch('expanded', value => {
                window.sidebarExpanded = value;
            });
    
            window.sidebarExpanded = this.expanded;
    
            const media = window.matchMedia('(min-width: 1024px)');
    
            this.isDesktop = media.matches;
            this.expanded2 = this.expanded;
    
            media.addEventListener('change', e => {
                this.isDesktop = e.matches;
    
                if (!e.matches) {
                    this.expanded = false;
                } else {
                    this.expanded = this.expanded2;
                }
            });
        }
    }">

    {{-- <div class="fixed inset-y-0 left-0 z-100 transition-all duration-300"
        :class="{
            '-translate-x-full': !isDesktop && !expanded,
            'translate-x-0': isDesktop || expanded,
            'w-[72px]': isDesktop && !expanded,
            'w-[256px]': !isDesktop || expanded,
        }">

        @livewire('navigation.navbar')
    </div>

    <div x-show="isDesktop || (expanded && !isDesktop)" x-cloak
        x-transition:enter="transition transform duration-300 ease-in-out" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200 ease-in-out"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
        class="z-100 fixed inset-y-0 left-0 transition-all duration-300"
        :class="isDesktop && !expanded ? 'w-[72px]' : 'w-[256px]'">

        @livewire('navigation.navbar')

    </div> --}}

   <div x-show="isDesktop || (expanded && !isDesktop)" x-cloak
        {{-- x-transition:enter="transition transform duration-300 ease-in-out" x-transition:enter-start="-translate-x-full" --}}
        {{-- x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200 ease-in-out" --}}
        {{-- x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" --}}
        class="z-100 fixed inset-y-0 left-0 transition-all duration-300"
         :class="{
            '-translate-x-full': !isDesktop && !expanded,
            'translate-x-0': isDesktop || expanded,
            'w-[72px]': isDesktop && !expanded,
            'w-[256px]': !isDesktop || expanded,
        }">

        @livewire('navigation.navbar')

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
