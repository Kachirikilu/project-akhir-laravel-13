<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Theme Settings')] class extends Component {
}; ?>

<section class="w-full pb-48">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Theme Settings') }}</flux:heading>
    <x-pages::settings.layout :heading="__('Theme')" :subheading="__('Atur Tema pada Dashboard Akun Anda')">
        <div class="space-y-4 w-full max-w-lg">
            {{-- Radio Group Appearance --}}
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance"
                class="!bg-[var(--sub-table-color)] !border !border-[var(--border-table-color)] p-1 rounded-lg w-full flex">
                <flux:radio value="light" icon="sun"
                    class="cursor-pointer data-[checked]:!bg-[var(--focus-color)] data-[checked]:!text-white !text-[var(--contrast-main-text)] transition-all [&_svg]:!text-inherit">
                    {{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon"
                    class="cursor-pointer data-[checked]:!bg-[var(--focus-color)] data-[checked]:!text-white !text-[var(--contrast-main-text)] transition-all [&_svg]:!text-inherit">
                    {{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop"
                    class="cursor-pointer data-[checked]:!bg-[var(--focus-color)] data-[checked]:!text-white !text-[var(--contrast-main-text)] transition-all [&_svg]:!text-inherit">
                    {{ __('System') }}</flux:radio>
            </flux:radio.group>
        </div>

        <livewire:all-role.wallpaper-management lazy />

        <div class="space-y-4 w-full max-w-lg">
            {{-- Container Tema (Atas) --}}
            <div class="space-y-2 w-full">
                <div class="w-full flex justify-center">
                    <div x-data
                        class="w-full max-w-sm flex items-center p-1.5 bg-gray-100 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 overflow-x-auto no-scrollbar snap-x"
                        x-ref="themeContainerBig">
                        <div class="flex gap-2 items-center mx-auto w-max px-1.5">
                            <template x-for="theme in $store.theme_manager.allThemes" :key="theme.id">
                                <button type="button" @click="$store.theme_manager.setTheme(theme.id)"
                                    class="cursor-pointer relative flex-shrink-0 w-7 h-7 rounded-lg transition-all duration-300 hover:scale-105 active:scale-105 focus:outline-none snap-center"
                                    :class="$store.theme_manager.currentTheme === theme.id ?
                                        'ring-2 ring-[var(--main-color)] ring-offset-1' : 'opacity-70 hover:opacity-100 active:opacity-100'"
                                    :style="`background-color: ${theme.color}`">
                                    <span x-show="$store.theme_manager.currentTheme === theme.id"
                                        class="absolute inset-0 flex items-center justify-center">
                                        <flux:icon name="check" variant="micro" class="w-4 h-4 text-white" />
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Navigasi (Bawah) --}}
                <div class="w-full flex justify-center">
                    <div
                        class="w-[240px] flex justify-between items-center bg-gray-900/80 dark:bg-white/10 backdrop-blur-md rounded-full border border-white/20 shadow-lg overflow-hidden">

                        <button @click="$store.theme_manager.scrollThemes('left', 'themeContainerBig')" type="button"
                            class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
                            <flux:icon name="chevron-left" variant="mini"
                                class="w-4 h-4 text-gray-400 group-hover:text-white group-active:text-white" />
                        </button>

                        {{-- 3 TITIK DINAMIS (MODE RAHASIA) --}}
                        <button @click="$store.theme_manager.toggleAutoPlay()" type="button"
                            class="flex items-center gap-1.5 px-4 group cursor-pointer"
                            :title="$store.theme_manager.isAutoPlaying ? 'Stop Auto Mode' : 'Secret Party Mode!'">

                            <div class="w-2 h-2 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100 group-active:opacity-100"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(-1)}`"
                                :class="$store.theme_manager.isAutoPlaying && 'animate-bounce translate-y-[1px]'"></div>

                            <div class="w-2.5 h-2.5 rounded-full transition-all duration-500 shadow-[0_0_6px_rgba(255,255,255,0.5)]"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(0)}`"
                                :class="$store.theme_manager.isAutoPlaying && 'animate-pulse scale-125'"></div>

                            <div class="w-2 h-2 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100 group-active:opacity-100"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(1)}`"
                                :class="$store.theme_manager.isAutoPlaying &&
                                    'animate-bounce [animation-delay:0.2s] translate-y-[1px]'">
                            </div>
                        </button>

                        <button @click="$store.theme_manager.scrollThemes('right', 'themeContainerBig')"
                            type="button"
                            class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
                            <flux:icon name="chevron-right" variant="mini"
                                class="w-4 h-4 text-gray-400 group-hover:text-white group-active:text-white" />
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </x-pages::settings.layout>
</section>

{{-- Navigasi (Dikecilkan) --}}
{{-- <div x-show="typeof expanded === 'undefined' ? true : expanded" x-transition
    class="w-full max-w-[200px] flex justify-between items-center bg-gray-900/80 dark:bg-white/5 backdrop-blur-md rounded-full border border-white/10 shadow-md overflow-hidden py-1">

    <button @click="scrollThemes('left')" type="button"
        class="group flex items-center justify-center w-10 h-6 hover:bg-white/10 transition-all cursor-pointer">
        <flux:icon name="chevron-left" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white group-active:text-white" />
    </button>

    <button @click="toggleAutoPlay()" type="button" class="flex items-center gap-2 px-3 group cursor-pointer">
        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 opacity-50 group-hover:opacity-100 group-active:opacity-100"
            :style="`background-color: ${getThemeColor(-1)}`"></div>
        <div class="w-2 h-2 rounded-full transition-all duration-300 shadow-sm"
            :style="`background-color: ${getThemeColor(0)}`"></div>
        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 opacity-50 group-hover:opacity-100 group-active:opacity-100"
            :style="`background-color: ${getThemeColor(1)}`"></div>
    </button>

    <button @click="scrollThemes('right')" type="button"
        class="group flex items-center justify-center w-10 h-6 hover:bg-white/10 transition-all cursor-pointer">
        <flux:icon name="chevron-right" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white group-active:text-white" />
    </button>
</div> --}}


{{-- <div x-data="{
    currentTheme: localStorage.getItem('app-theme') || 'blue',
    isInternalOpen: true,
    isAutoPlaying: localStorage.getItem('auto-play-mode') === 'true',
    get autoPlayInterval() { return window.themeInterval || null },
    set autoPlayInterval(val) { window.themeInterval = val },

    allThemes: [
        { id: 'blue', color: '#075985' },
        { id: 'purple', color: '#7e22ce' },
        { id: 'red', color: '#991b1b' },
        { id: 'green', color: '#059669' },
        { id: 'amber', color: '#b45309' },
        { id: 'pink', color: '#db2777' },
        { id: 'navy', color: '#475569' },
        { id: 'brown', color: '#5d4037' },
        { id: 'black', color: '#000000' },
    ],

    getThemeColor(offset) {
        let currentIndex = this.allThemes.findIndex(t => t.id === this.currentTheme);
        if (currentIndex === -1) currentIndex = 0;
        let targetIndex = (currentIndex + offset + this.allThemes.length) % this.allThemes.length;
        return this.allThemes[targetIndex].color;
    },

    setTheme(id) {
        this.currentTheme = id;
        document.documentElement.setAttribute('data-theme', id);
        localStorage.setItem('app-theme', id);
    },

    startInterval() {
        this.stopInterval();

        this.autoPlayInterval = setInterval(() => {
            let currentIndex = this.allThemes.findIndex(t => t.id === this.currentTheme);
            let nextIndex = (currentIndex + 1) % this.allThemes.length;

            this.setTheme(this.allThemes[nextIndex].id);

            this.$nextTick(() => {
                if (window.sidebarExpanded === false) return;

                const buttons = this.$refs.themeContainer?.querySelectorAll('button');
                if (buttons && buttons[nextIndex]) {
                    buttons[nextIndex].scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        }, 12000);
    },

    stopInterval() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    },

    toggleAutoPlay() {
        if (this.isAutoPlaying) {
            this.isAutoPlaying = false;
            localStorage.setItem('auto-play-mode', 'false');
            this.stopInterval();
        } else {
            this.isAutoPlaying = true;
            localStorage.setItem('auto-play-mode', 'true');
            this.startInterval();
        }
    },

    scrollThemes(direction) {
        const container = this.$refs.themeContainer;
        const scrollAmount = 100;
        if (direction === 'left') {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
}" x-init="document.documentElement.setAttribute('data-theme', currentTheme);
if (isAutoPlaying) { startInterval(); }" class="flex flex-col gap-3">
    <div x-ref="themeContainer"
        class="w-full flex items-center p-1.5 bg-gray-100 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 overflow-x-auto scrollbar-medium snap-x">
        <div class="flex gap-2 items-center mx-auto w-max px-1.5">
            <template x-for="theme in allThemes" :key="theme.id">
                <button type="button" @click="setTheme(theme.id); if(isAutoPlaying) toggleAutoPlay();"
                    class="relative flex-shrink-0 w-7 h-7 rounded-lg transition-all duration-300 hover:scale-105 focus:outline-none snap-center"
                    :class="currentTheme === theme.id ? 'ring-2 ring-[var(--main-color)] ring-offset-1' :
                        'opacity-70 hover:opacity-100'"
                    :style="`background-color: ${theme.color}`">
                    <span x-show="currentTheme === theme.id" class="absolute inset-0 flex items-center justify-center">
                        <flux:icon name="check" variant="micro" class="w-4 h-4 text-white" />
                    </span>
                </button>
            </template>
        </div>
    </div>
</div> --}}
