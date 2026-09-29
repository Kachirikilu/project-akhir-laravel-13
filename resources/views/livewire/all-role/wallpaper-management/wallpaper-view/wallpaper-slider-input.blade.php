<div class="flex flex-col gap-1">
    <div class="flex justify-between text-xs text-[var(--contrast-main-text)] opacity-70">
        <span>{{ ucfirst($type) }}</span>
        <span x-text="Math.round($store.theme_manager.{{ $type }} * 100) + '%'"></span>
    </div>
    <input type="range" min="0" max="1" step="0.01"
        x-model.number="$store.theme_manager.{{ $type }}" @input="$store.theme_manager.updateSettings()"
        :style="`background: linear-gradient(to right, var(--main-color) ${$store.theme_manager.{{ $type }} * 100}%, var(--border-table-color) ${$store.theme_manager.{{ $type }} * 100}%)`"
        class="theme-slider">
</div>
