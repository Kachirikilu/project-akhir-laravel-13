@php
    $isCustom = str_contains($wp['path'], 'wallpapers/');
    $thumbPath = $isCustom ? str_replace('wallpapers/', 'wallpapers/thumbs/', $wp['path']) : $wp['path'];
    $finalPath = $isCustom && file_exists(public_path($thumbPath)) ? $thumbPath : $wp['path'];
@endphp

<div wire:key="wallpaper-{{ $wp['id'] }}" class="relative flex-shrink-0 w-[90px] h-[210px] rounded-xl overflow-hidden snap-center border-4 transition-all cursor-pointer border-[var(--border-table-color)]"
    :class="$store.theme_manager.activeWallpaper === '{{ $finalPath }}' ? 'border-[var(--main-color)]' : 'border-transparent'"
    @click="$store.theme_manager.setWallpaper('{{ $wp['path'] }}')">

    <img src="{{ $wp['path'] }}" class="w-full h-full object-cover" loading="lazy">

    <div x-show="$store.theme_manager.activeWallpaper === '{{ $finalPath }}'"
        class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white transition-opacity">
        <flux:icon name="check-circle" variant="mini" class="w-6 h-6 mb-1" />
        <span class="text-[10px] font-bold">Terpilih</span>
    </div>

    {{-- Tombol Delete --}}
    @if (!($noDelete ?? false))
        <button @click.stop="$wire.deleteWallpaper({{ $wp['id'] }})"
            class="cursor-pointer absolute top-2 right-2 bg-black/50 p-1 rounded-full hover:bg-red-600 active:bg-red-700 transition-colors">
            <flux:icon name="trash" variant="micro" class="w-3 h-3 text-white" />
        </button>
    @endif
</div>
