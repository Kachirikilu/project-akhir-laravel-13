@props([
    'item',
    'menu',     // nama variable alpine, contoh: openOBEMenu
    'trigger',
    'active',
])

<button
    type="button"
    @click="expanded ? {{ $menu }} = !{{ $menu }} : $refs['{{ $trigger }}'].click()"
    class="cursor-pointer flex items-center text-xs mx-1 p-2 rounded-lg transition-colors w-full
        {{ $active
            ? 'bg-white/20 text-[var(--main-text)]'
            : 'text-[var(--main-text)]/80 hover:bg-white/10 hover:text-[var(--main-text)]' }}"
    title="{{ $item['label'] }}"
>
    <div class="flex items-center justify-between overflow-hidden w-full">
        <flux:icon :name="$item['icon']" variant="outline"
            class="w-4 h-4 shrink-0" />

        <div
            x-show="expanded"
            x-cloak
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            class="flex flex-1 items-center justify-between overflow-hidden ml-3"
        >
            <span
                class="whitespace-nowrap overflow-hidden text-ellipsis block text-left flex-1">
                {{ $item['label'] }}
            </span>

            <span
                class="transition-transform duration-200 shrink-0 ml-auto"
                :class="{ 'rotate-180': {{ $menu }} }"
            >
                <flux:icon name="chevron-down" class="w-3 h-3" />
            </span>
        </div>
    </div>
</button>