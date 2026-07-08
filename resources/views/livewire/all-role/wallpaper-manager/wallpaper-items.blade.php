@php
    $isCustom = str_contains($wp['path'], 'wallpapers/');
    $thumbPath = $isCustom ? str_replace('wallpapers/', 'wallpapers/thumbs/', $wp['path']) : $wp['path'];
    $finalPath = ($isCustom && file_exists(public_path($thumbPath))) ? $thumbPath : $wp['path'];
@endphp

<div class="relative flex-shrink-0 w-[108px] h-[216px] rounded-xl overflow-hidden snap-center border-4 transition-all cursor-pointer"
    :class="$store.theme_manager.activeWallpaper === '{{ $wp['path'] }}' ?
        'border-[var(--main-color)]' : 'border-transparent'"
    @click="$store.theme_manager.setWallpaper('{{ $wp['path'] }}')">
    <img src="{{ $wp['path'] }}" class="w-full h-full object-cover" loading="lazy">
    @if (!($noDelete ?? false))
        <button @click.stop="$wire.deleteWallpaper({{ $wp['id'] }})"
            class="cursor-pointer absolute top-2 right-2 bg-black/50 p-1 rounded-full hover:bg-red-600 transition-colors">
            <flux:icon name="trash" variant="micro" class="w-3 h-3 text-white" />
        </button>
    @endif
</div>
