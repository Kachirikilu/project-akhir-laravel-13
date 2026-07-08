@php
    $noBar = $noBar ?? false;
@endphp
<div x-data class="flex flex-col items-center gap-1 {{ !$noBar ? 'mb-6' : '' }}">

    {{-- Container Tema --}}
    <div x-show="typeof expanded === 'undefined' ? true : expanded" x-transition:leave.duration.400ms x-cloak
        x-ref="themeContainer"
        class="w-[220px] gap-3 p-1 flex items-center bg-gray-100 dark:bg-white/90 rounded-full border border-gray-200 dark:border-white/10 overflow-x-auto no-scrollbar snap-x">

        <template x-for="theme in $store.theme_manager.allThemes" :key="theme.id">
            <button type="button"
                @click="$store.theme_manager.setTheme(theme.id); if($store.theme_manager.isAutoPlaying) $store.theme_manager.toggleAutoPlay();"
                class="cursor-pointer relative flex-shrink-0 w-5 h-5 rounded-full transition-all duration-500 hover:scale-110 focus:outline-none snap-center"
                :class="$store.theme_manager.currentTheme === theme.id ? 'ring-2 ring-[var(--main-color)] ring-offset-2' :
                    'opacity-60'"
                :style="`background-color: ${theme.color}`">

                <span x-show="$store.theme_manager.currentTheme === theme.id"
                    class="absolute inset-0 flex items-center justify-center">
                    <flux:icon name="check" variant="mini" class="w-3 h-3 text-white" />
                </span>
            </button>
        </template>
    </div>

    {{-- PIL NAVIGASI DENGAN PREVIEW WARNA --}}
    @if (!$noBar)
        <div x-show="typeof expanded === 'undefined' ? true : expanded" x-transition:leave.duration.400ms
            class="w-[220px] flex justify-between items-center bg-gray-900/80 dark:bg-white/10 backdrop-blur-md rounded-full border border-white/20 shadow-lg overflow-hidden">

            <button @click="$store.theme_manager.scrollThemes('left', 'themeContainer')" type="button"
                class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
                <flux:icon name="chevron-left" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
            </button>

            {{-- 3 TITIK DINAMIS (MODE RAHASIA) --}}
            <button @click="$store.theme_manager.toggleAutoPlay()" type="button"
                class="flex items-center gap-1.5 px-4 group cursor-pointer"
                :title="$store.theme_manager.isAutoPlaying ? 'Stop Auto Mode' : 'Secret Party Mode!'">

                <div class="w-1.5 h-1.5 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                    :style="`background-color: ${$store.theme_manager.getThemeColor(-1)}`"
                    :class="$store.theme_manager.isAutoPlaying && 'animate-bounce translate-y-[1px]'"></div>

                <div class="w-2 h-2 rounded-full transition-all duration-500 shadow-[0_0_6px_rgba(255,255,255,0.5)]"
                    :style="`background-color: ${$store.theme_manager.getThemeColor(0)}`"
                    :class="$store.theme_manager.isAutoPlaying && 'animate-pulse scale-125'"></div>

                <div class="w-1.5 h-1.5 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                    :style="`background-color: ${$store.theme_manager.getThemeColor(1)}`"
                    :class="$store.theme_manager.isAutoPlaying && 'animate-bounce [animation-delay:0.2s] translate-y-[1px]'">
                </div>
            </button>

            <button @click="$store.theme_manager.scrollThemes('right', 'themeContainer')" type="button"
                class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
                <flux:icon name="chevron-right" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
            </button>
        </div>
    @endif
</div>
