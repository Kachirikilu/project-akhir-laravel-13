@props(['subMenus', 'openMenuVar' => 'openKelasMenu'])

<div x-data="{ currentTable: '{{ request()->route('switchTable') ?? 'rps' }}' }"
    @table-switched.window="currentTable = $event.detail.switchTable === '' ? 'rps' : $event.detail.switchTable"
    x-show="expanded && {{ $openMenuVar }}" x-cloak x-transition:enter="transition-all duration-300 ease-out"
    x-transition:enter-start="opacity-0 -translate-y-4 max-h-0 origin-top"
    x-transition:enter-end="opacity-100 translate-y-0 max-h-[500px] origin-top"
    x-transition:leave="transition-all duration-200 ease-in"
    x-transition:leave-start="opacity-100 translate-y-0 max-h-[500px] origin-top"
    x-transition:leave-end="opacity-0 -translate-y-4 max-h-0 origin-top"
    class="mt-1 space-y-1 pl-4 w-full ml-1 overflow-hidden">
    
    @foreach ($subMenus as $sub)
        <a href="{{ $sub['url'] }}" wire:navigate
            style="margin-left: {{ ($sub['level'] ?? false) == 1 ? 18 : (($sub['level'] ?? false) == 2 ? 48 : '') }}px"
            :class="(currentTable === '{{ $sub['param'] }}' || (currentTable === '' && '{{ $sub['param'] }}' === 'rps')) ?
                'bg-white/20 text-white font-semibold border-[var(--main-text)] pl-3 shadow-sm' :
                'text-[var(--main-text)]/70 hover:bg-white/10 active:bg-white/20 hover:text-[var(--main-text)] active:text-[var(--main-text)]/90 border-transparent pl-4'"
            class="block text-[11px] p-2 rounded-md border-l-4 transition-all duration-300 ease-in-out transform active:scale-95">
            <div class="flex items-center">
                <flux:icon :name="$sub['icon']" class="
                    {{-- {{ $sub['color'] ?? '' }} --}}
                    mr-2 h-4 w-4 shrink-0" />
                <span class="inline-block text-ellipsis whitespace-nowrap">{{ $sub['label'] }}</span>
            </div>
        </a>
    @endforeach
</div>