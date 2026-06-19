<div x-data="{
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
        { id: 'black', color: '#000000' }
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
}" 
x-init="
    document.documentElement.setAttribute('data-theme', currentTheme);
    if (isAutoPlaying) { startInterval(); }
" 
class="flex flex-col items-center gap-1 mb-6">

    {{-- Container Scrollable --}}
    <div x-show="typeof expanded === 'undefined' ? true : expanded" x-transition:leave.duration.400ms x-cloak x-ref="themeContainer"
        class="w-[220px] gap-3 p-1 flex items-center bg-gray-100 dark:bg-white/90 rounded-full border border-gray-200 dark:border-white/10 overflow-x-auto no-scrollbar snap-x"
        :style="!(typeof expanded === 'undefined' ? true : expanded) ? 'scroll-behavior: auto !important' : ''">
        <template x-for="theme in allThemes" :key="theme.id">
            <button type="button" @click="setTheme(theme.id); if(isAutoPlaying) toggleAutoPlay();"
                class="relative flex-shrink-0 w-5 h-5 rounded-full transition-all duration-500 hover:scale-110 focus:outline-none snap-center"
                :class="currentTheme === theme.id ? 'ring-2 ring-[var(--main-color)] ring-offset-2' : 'opacity-60'"
                :style="`background-color: ${theme.color}`">
                <span x-show="currentTheme === theme.id" class="absolute inset-0 flex items-center justify-center">
                    <flux:icon name="check" variant="mini" class="w-3 h-3 text-white" />
                </span>
            </button>
        </template>
    </div>

    {{-- PIL NAVIGASI DENGAN PREVIEW WARNA --}}
    <div x-show="typeof expanded === 'undefined' ? true : expanded" x-transition:leave.duration.400ms
        class="w-[220px] flex justify-between items-center bg-gray-900/80 dark:bg-white/10 backdrop-blur-md rounded-full border border-white/20 shadow-lg overflow-hidden">

        <button @click="scrollThemes('left')" type="button"
            class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
            <flux:icon name="chevron-left" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
        </button>

        {{-- 3 TITIK DINAMIS (MODE RAHASIA) --}}
        <button @click="toggleAutoPlay()" type="button" class="flex items-center gap-1.5 px-4 group cursor-pointer"
            :title="isAutoPlaying ? 'Stop Auto Mode' : 'Secret Party Mode!'">

            <div class="w-1.5 h-1.5 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                :style="`background-color: ${getThemeColor(-1)}`"
                :class="isAutoPlaying && 'animate-bounce translate-y-[1px]'"></div>

            <div class="w-2 h-2 rounded-full transition-all duration-500 shadow-[0_0_6px_rgba(255,255,255,0.5)]"
                :style="`background-color: ${getThemeColor(0)}`" :class="isAutoPlaying && 'animate-pulse scale-125'">
            </div>

            <div class="w-1.5 h-1.5 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                :style="`background-color: ${getThemeColor(1)}`"
                :class="isAutoPlaying && 'animate-bounce [animation-delay:0.2s] translate-y-[1px]'"></div>
        </button>

        {{-- Tombol Kanan --}}
        <button @click="scrollThemes('right')" type="button"
            class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
            <flux:icon name="chevron-right" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
        </button>
    </div>
</div>
