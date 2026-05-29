<div
    x-data="{
        isAuto: false,

        // default OFF
        colorMenuOpen: false,

        get isDark() {
            if ($flux.appearance === 'system') {
                return window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            return $flux.appearance === 'dark';
        },

        updateAppearance(val) {
            $flux.appearance = val;
        },

        manualToggle() {
            const nextTheme = this.isDark ? 'light' : 'dark';
            this.updateAppearance(nextTheme);
        },

        toggleColorMenu() {
            this.colorMenuOpen = !this.colorMenuOpen;
            localStorage.setItem(
                'colorMenuOpen',
                JSON.stringify(this.colorMenuOpen)
            );
            this.$dispatch('toggle-color-panel', {
                open: this.colorMenuOpen
            });
        }
    }"

    x-init="
        isAuto = ($flux.appearance === 'system');

        const savedColorMenu =
            localStorage.getItem('colorMenuOpen');

        colorMenuOpen =
            savedColorMenu !== null
                ? JSON.parse(savedColorMenu)
                : false;

        $dispatch('toggle-color-panel', {
            open: colorMenuOpen
        });

        const media = window.matchMedia('(prefers-color-scheme: dark)');

        media.addEventListener('change', () => {
            if ($flux.appearance === 'system') {
                isAuto = true;
            }
        });
    "

    x-effect="
        isAuto = ($flux.appearance === 'system')
    "

    class="px-1 pr-4 flex items-center"
>

    {{-- ===================== --}}
    {{-- TOGGLE ICON (SUN / MOON) --}}
    {{-- ===================== --}}
    <div :class="isAuto ? 'opacity-40' : 'opacity-100'" class="transition-opacity duration-300 mr-6">

        <button type="button" @click="manualToggle()"
            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[var(--main-text)] transition-all">

            <span class="flex items-center justify-center w-4 h-4 bg-white rounded-full shadow-sm">
                {{-- ☀️ LIGHT --}}
                <flux:icon x-show="!isDark" name="sun" variant="mini" class="w-4 h-4 text-amber-500" />
                {{-- 🌙 DARK --}}
                <flux:icon x-show="isDark" name="moon" variant="mini" class="w-4 h-4 text-sky-900" />

            </span>

        </button>
    </div>

    {{-- ===================== --}}
    {{-- SYSTEM TOGGLE --}}
    {{-- ===================== --}}
    <div class="cursor-pointer flex items-center space-x-2 whitespace-nowrap" x-show="expanded" x-cloak
        x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">

        {{-- CHECKBOX --}}
        <flux:checkbox x-model="isAuto"
            @change="
                if (isAuto) {
                    updateAppearance('system');
                } else {
                    updateAppearance(
                        window.matchMedia('(prefers-color-scheme: dark)').matches 
                            ? 'dark' 
                            : 'light'
                    );
                }
            " />

        {{-- LABEL --}}
        <span class="text-sm font-medium text-white select-none cursor-pointer"
            @click="
                isAuto = !isAuto;

                if (isAuto) {
                    updateAppearance('system');
                } else {
                    updateAppearance(
                        window.matchMedia('(prefers-color-scheme: dark)').matches 
                            ? 'dark' 
                            : 'light'
                    );
                }
            ">
            System
        </span>

        <div x-show="typeof expanded !== 'undefined' ? expanded : true" x-cloak class="ml-auto flex items-center">
            <button type="button" @click="toggleColorMenu()"
                class="ml-2 cursor-pointer flex items-center justify-center p-1 rounded hover:bg-white/10 text-white/70 hover:text-white transition-colors"
                aria-label="Toggle Color Menu">
                <flux:icon name="chevron-down" variant="mini" class="w-4 h-4 transition-transform duration-300"
                    ::class="!colorMenuOpen ? 'rotate-180' : ''" />
            </button>
        </div>

    </div>



</div>
