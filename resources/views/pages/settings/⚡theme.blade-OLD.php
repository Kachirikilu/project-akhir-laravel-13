<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Models\Auth\Wallpaper;
use App\Livewire\Global\HasToast;
use Illuminate\Support\Facades\Validator;

new #[Title('Theme Settings')] class extends Component {
    use WithFileUploads;
    use HasToast;

    public $wallpaper;

    public function updatedwallpaper()
    {
        $this->resetErrorBag();
        $validator = Validator::make(
            ['wallpaper' => $this->wallpaper],
            [
                'wallpaper' => 'image|max:5120',
            ],
            [
                'wallpaper.image' => 'Wallpaper yang diunggah harus berupa gambar!',
                'wallpaper.max' => 'Ukuran foto maksimal adalah 5 MB!',
            ],
        );

        if ($validator->fails()) {
            $this->toast(text: $validator->errors()->first(), type: 'error', variant: 'danger');
            $this->reset('wallpaper');
            return;
        }

        $path = $this->wallpaper->store('wallpapers/' . auth()->id(), 'public');
        auth()
            ->user()
            ->wallpapers()
            ->create([
                'path' => '/storage/' . $path,
            ]);
        $this->reset('wallpaper');
        $this->dispatch('refresh-wallpaper-list');
        $this->toast(text: 'Wallpaper ditambahkan!');
    }
    public function deleteWallpaper($id)
    {
        $wallpaper = Wallpaper::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        if ($wallpaper) {
            $path = str_replace('/storage/', '', $wallpaper->path);
            Storage::disk('public')->delete($path);
            $wallpaper->delete();
            $this->toast(text: 'Wallpaper dihapus!', type: 'update');
        }
    }
    public function with()
    {
        $defaults = [['id' => 'd1', 'path' => '/images/bg-unsri.png', 'is_custom' => false], ['id' => 'w1', 'path' => '/wallpapers/my-alya.png', 'is_custom' => false], ['id' => 'w2', 'path' => '/wallpapers/my-masha.png', 'is_custom' => false], ['id' => 'w3', 'path' => '/wallpapers/my-waguri.png', 'is_custom' => false]];
        $custom = Wallpaper::where('user_id', auth()->id())
            ->get()
            ->map(
                fn($wp) => [
                    'id' => $wp->id,
                    'path' => $wp->path,
                    'is_custom' => true,
                ],
            );

        return [
            'defaultWallpapers' => $defaults,
            'customWallpapers' => $custom->toArray(),
        ];
    }
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

        <!-- Wallpaper Selector Card -->
        <div
            class="my-4 p-4 bg-[var(--sub-table-color)] rounded-2xl border border-[var(--border-table-color)] w-full max-w-lg">
            <div class="w-full flex justify-between items-center mb-4">
                <h3 class="text-[var(--contrast-main-text)] text-sm font-medium">
                    Pilih Wallpaper
                </h3>
                @error('wallpaper')
                    <span class="text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            <!-- Slider Gambar -->
            <div x-ref="wallpaperSlider" class="flex gap-3 overflow-x-auto scrollbar-medium snap-x pb-2 mb-2">

                <!-- 1. Opsi None -->
                <div class="relative flex-shrink-0 w-[90px] h-[210px] rounded-xl overflow-hidden snap-center border-2 border-dashed border-[var(--border-table-color)] bg-[var(--main-color)] flex items-center justify-center cursor-pointer"
                    @click="$store.theme_manager.setWallpaper(null)">
                    <div class="flex flex-col items-center justify-center text-[var(--border-main-color)]">
                        <flux:icon name="no-symbol" variant="mini" class="w-6 h-6 mb-1" />
                        <span class="text-[10px]">None</span>
                    </div>
                </div>
                <label
                    class="bg-[var(--main-color)]/20 flex-shrink-0 w-[90px] h-[210px] rounded-xl border-2 border-dashed border-[var(--border-table-color)] flex items-center justify-center cursor-pointer hover:border-[var(--main-color)] transition-colors">
                    <input type="file" class="hidden" accept="image/*" wire:model.live="wallpaper">

                    <div wire:loading wire:target="wallpaper">
                        <svg class="animate-spin w-6 h-6 text-[var(--main-color)]" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <div wire:loading.remove wire:target="wallpaper">
                        <flux:icon name="plus" variant="mini"
                            class="w-6 h-6 text-[var(--contrast-main-text)] opacity-50" />
                    </div>
                </label>


                <!-- 2. Loop Wallpaper -->
                @foreach ($defaultWallpapers as $wp)
                    <div class="relative flex-shrink-0 w-[90px] h-[210px] rounded-xl overflow-hidden snap-center border-2 transition-all cursor-pointer"
                        :class="$store.theme_manager.activeWallpaper === '{{ $wp['path'] }}' ? 'border-[var(--main-color)]' :
                            'border-transparent'"
                        @click="$store.theme_manager.setWallpaper('{{ $wp['path'] }}')">

                        <img src="{{ $wp['path'] }}" class="w-full h-full object-cover">

                        @if ($wp['is_custom'])
                            <button @click.stop="$wire.deleteWallpaper({{ $wp['id'] }})"
                                class="cursor-pointer absolute top-2 right-2 bg-black/50 p-1 rounded-full hover:bg-red-600 transition-colors">
                                <flux:icon name="trash" variant="micro" class="w-3 h-3 text-white" />
                            </button>
                        @endif
                    </div>
                @endforeach

            </div>

            @if (!empty($customWallpapers))
                <div x-ref="wallpaperSlider" class="flex gap-3 overflow-x-auto scrollbar-medium snap-x pb-2">

                    <!-- 3. Loop Wallpaper Kustom -->
                    @foreach ($customWallpapers as $wp)
                        <div class="relative flex-shrink-0 w-[90px] h-[210px] rounded-xl overflow-hidden snap-center border-2 transition-all cursor-pointer"
                            :class="$store.theme_manager.activeWallpaper === '{{ $wp['path'] }}' ?
                                'border-[var(--main-color)]' :
                                'border-transparent'"
                            @click="$store.theme_manager.setWallpaper('{{ $wp['path'] }}')">

                            <img src="{{ $wp['path'] }}" class="w-full h-full object-cover">

                            @if ($wp['is_custom'])
                                <button @click.stop="$wire.deleteWallpaper({{ $wp['id'] }})"
                                    class="cursor-pointer absolute top-2 right-2 bg-black/50 p-1 rounded-full hover:bg-red-600 transition-colors">
                                    <flux:icon name="trash" variant="micro" class="w-3 h-3 text-white" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            

            <!-- Kontrol Opacity & Brightness -->
            <div class="pt-4 border-t border-[var(--border-table-color)] space-y-4">
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs text-[var(--contrast-main-text)] opacity-70">
                        <span>Opacity</span>
                        <span x-text="Math.round($store.theme_manager.opacity * 100) + '%'"></span>
                    </div>
                    <input type="range" min="0" max="1" step="0.01"
                        x-model.number="$store.theme_manager.opacity" @input="$store.theme_manager.updateSettings()"
                        :style="`background: linear-gradient(to right, var(--main-color) ${$store.theme_manager.opacity * 100}%, var(--border-table-color) ${$store.theme_manager.opacity * 100}%)`"
                        class="theme-slider">
                </div>

                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-xs text-[var(--contrast-main-text)] opacity-70">
                        <span>Brightness</span>
                        <span x-text="Math.round($store.theme_manager.brightness * 100) + '%'"></span>
                    </div>
                    <input type="range" min="0" max="1" step="0.01"
                        x-model.number="$store.theme_manager.brightness" @input="$store.theme_manager.updateSettings()"
                        :style="`background: linear-gradient(to right, var(--main-color) ${$store.theme_manager.brightness * 100}%, var(--border-table-color) ${$store.theme_manager.brightness * 100}%)`"
                        class="theme-slider">
                </div>
            </div>

        </div>


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
                                    class="cursor-pointer relative flex-shrink-0 w-7 h-7 rounded-lg transition-all duration-300 hover:scale-105 focus:outline-none snap-center"
                                    :class="$store.theme_manager.currentTheme === theme.id ?
                                        'ring-2 ring-[var(--main-color)] ring-offset-1' : 'opacity-70 hover:opacity-100'"
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
                                class="w-4 h-4 text-gray-400 group-hover:text-white" />
                        </button>

                        {{-- 3 TITIK DINAMIS (MODE RAHASIA) --}}
                        <button @click="$store.theme_manager.toggleAutoPlay()" type="button"
                            class="flex items-center gap-1.5 px-4 group cursor-pointer"
                            :title="$store.theme_manager.isAutoPlaying ? 'Stop Auto Mode' : 'Secret Party Mode!'">

                            <div class="w-2 h-2 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(-1)}`"
                                :class="$store.theme_manager.isAutoPlaying && 'animate-bounce translate-y-[1px]'"></div>

                            <div class="w-2.5 h-2.5 rounded-full transition-all duration-500 shadow-[0_0_6px_rgba(255,255,255,0.5)]"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(0)}`"
                                :class="$store.theme_manager.isAutoPlaying && 'animate-pulse scale-125'"></div>

                            <div class="w-2 h-2 rounded-full transition-all duration-500 opacity-50 group-hover:opacity-100"
                                :style="`background-color: ${$store.theme_manager.getThemeColor(1)}`"
                                :class="$store.theme_manager.isAutoPlaying &&
                                    'animate-bounce [animation-delay:0.2s] translate-y-[1px]'">
                            </div>
                        </button>

                        <button @click="$store.theme_manager.scrollThemes('right', 'themeContainerBig')"
                            type="button"
                            class="group flex items-center justify-center w-8 h-4 hover:bg-white/10 active:bg-white/20 transition-all cursor-pointer">
                            <flux:icon name="chevron-right" variant="mini"
                                class="w-4 h-4 text-gray-400 group-hover:text-white" />
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
        <flux:icon name="chevron-left" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
    </button>

    <button @click="toggleAutoPlay()" type="button" class="flex items-center gap-2 px-3 group cursor-pointer">
        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 opacity-50 group-hover:opacity-100"
            :style="`background-color: ${getThemeColor(-1)}`"></div>
        <div class="w-2 h-2 rounded-full transition-all duration-300 shadow-sm"
            :style="`background-color: ${getThemeColor(0)}`"></div>
        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 opacity-50 group-hover:opacity-100"
            :style="`background-color: ${getThemeColor(1)}`"></div>
    </button>

    <button @click="scrollThemes('right')" type="button"
        class="group flex items-center justify-center w-10 h-6 hover:bg-white/10 transition-all cursor-pointer">
        <flux:icon name="chevron-right" variant="mini" class="w-4 h-4 text-gray-400 group-hover:text-white" />
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
