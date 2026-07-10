<div class="my-4 p-4 bg-[var(--sub-table-color)] rounded-2xl border border-[var(--border-table-color)] w-full max-w-lg">
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
    <div wire:target="loadingDefaultWallpaper" wire:loading.class="opacity-50 pointer-events-none" x-ref="wallpaperSlider"
        class="flex gap-3 overflow-x-auto scrollbar-medium snap-x pb-2 mb-2">
        <!-- 1. Opsi None -->
        <div class="relative flex-shrink-0 w-[90px] h-[210px] rounded-xl overflow-hidden snap-center border-2 border-dashed bg-[var(--main-color)] flex items-center justify-center cursor-pointer border-[var(--border-table-color)]"
            :class="$store.theme_manager.activeWallpaper === null ? '!border-[var(--border-table-color)]/50' : ''"
            @click="$store.theme_manager.setWallpaper(null)">

            <div class="flex flex-col items-center justify-center text-[var(--border-main-color)]">
                <div x-show="$store.theme_manager.activeWallpaper !== null" class="flex flex-col items-center">
                    <flux:icon name="no-symbol" variant="mini" class="w-6 h-6 mb-1" />
                    <span class="text-[10px]">None</span>
                </div>
                <div x-show="$store.theme_manager.activeWallpaper == null" class="flex flex-col items-center">
                    <flux:icon name="check-circle" variant="mini" class="w-6 h-6 mb-1" />
                    <span class="text-[10px] font-bold">Terpilih</span>
                </div>
            </div>
        </div>

        @if (!$customWallpapers->isNotEmpty())
            @include('livewire.all-role.wallpaper-manager.wallpaper-input')
        @endif


        <!-- 2. Loop Wallpaper -->
        @foreach ($defaultWallpapers as $wp)
            @include('livewire.all-role.wallpaper-manager.wallpaper-items', ['noDelete' => 1])
        @endforeach

    </div>

    
    @if ($defaultWallpapers->hasPages())
        <div class="pb-2 overflow-auto scrollbar-medium" id="pagination-links-container" wire:target="{{ $defaultWallpapers->getPageName() }}">
            {{ $defaultWallpapers->links('vendor.pagination.tailwind', ['isSmall' => 1, 'typeXLoading' => 'loadingDefaultWallpaper', 'withNowrap' => 1]) }}
        </div>
    @endif

    @if ($customWallpapers->isNotEmpty())
        <h3 class="text-sm font-medium mt-6 mb-2">Wallpaper Saya</h3>

        <div wire:target="loadingCustomWallpaper, deleteWallpaper" wire:loading.class="opacity-50 pointer-events-none"
            class="flex gap-3 overflow-x-auto scrollbar-medium snap-x pb-2 mb-2">
            @include('livewire.all-role.wallpaper-manager.wallpaper-input')
            @foreach ($customWallpapers as $wp)
                @include('livewire.all-role.wallpaper-manager.wallpaper-items')
            @endforeach
        </div>

        {{-- Navigasi Halaman --}}
        @if ($customWallpapers->hasPages())
            <div class="pb-2 overflow-auto scrollbar-medium" id="pagination-links-container" wire:target="{{ $customWallpapers->getPageName() }}">
                {{ $customWallpapers->links('vendor.pagination.tailwind', ['isSmall' => 1, 'typeXLoading' => 'loadingCustomWallpaper', 'withNowrap' => 1]) }}
            </div>
        @endif
    @endif

    <!-- Kontrol Opacity & Brightness -->
    <div class="pt-4 border-t border-[var(--border-table-color)] space-y-4">
        @include('livewire.all-role.wallpaper-manager.wallpaper-slider-input', ['type' => 'opacity'])
        @include('livewire.all-role.wallpaper-manager.wallpaper-slider-input', ['type' => 'brightness'])
    </div>

</div>
