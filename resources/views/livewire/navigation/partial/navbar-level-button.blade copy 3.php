@props(['subMenus', 'openMenuVar' => 'openKelasMenu'])

<div x-data="{ 
        // Mengambil switchTable dari query string URL, jika tidak ada cari di route param, default ke 'rps'
        currentTable: (new URL(window.location.href)).searchParams.get('switchTable') || '{{ request()->route('switchTable') ?? 'rps' }}'
    }"
    x-on:table-switched.window="currentTable = $event.detail.switchTable === '' ? 'rps' : $event.detail.switchTable"
    x-on:livewire:navigated.window="currentTable = (new URL(window.location.href)).searchParams.get('switchTable') || '{{ request()->route('switchTable') ?? 'rps' }}'"
    x-show="expanded && {{ $openMenuVar }}" x-cloak x-transition:enter="transition-all duration-300 ease-out"
    x-transition:enter-start="opacity-0 -translate-y-4 max-h-0 origin-top"
    x-transition:enter-end="opacity-100 translate-y-0 max-h-[500px] origin-top"
    x-transition:leave="transition-all duration-200 ease-in"
    x-transition:leave-start="opacity-100 translate-y-0 max-h-[500px] origin-top"
    x-transition:leave-end="opacity-0 -translate-y-4 max-h-0 origin-top"
    class="mt-1 space-y-1 pl-4 w-full ml-1 overflow-hidden">
    
    @foreach ($subMenus as $sub)
        @php
            $param = $sub['param'] ?? '';
            $isOBEMenu = in_array($param, ['rps', 'cpmk', 'sub-cpmk', 'cpl', 'referensi', 'dosen']);
            $isRouteOBE = request()->routeIs('rps-management');
            $isBtnActive = $sub['active'] ?? false;
            $isBtnActiveSub = isset($sub['active-sub']) && $sub['active-sub'];
        @endphp

        <a href="{{ $sub['url'] }}" wire:navigate
            style="margin-left: {{ ($sub['level'] ?? false) == 1 ? 18 : (($sub['level'] ?? false) == 2 ? 48 : '') }}px"
            :class="(
                    ({{ $isOBEMenu ? 'true' : 'false' }} && {{ $isRouteOBE ? 'true' : 'false' }} && currentTable === '{{ $param }}') ||
                    (!{{ $isOBEMenu ? 'true' : 'false' }} && {{ $isBtnActive ? 'true' : 'false' }})
                )
                ? 'bg-white/20 text-white font-semibold border-[var(--main-text)] pl-3 shadow-sm' 
                : (
                    {{ $isBtnActiveSub ? 'true' : 'false' }}
                    ? 'border-[var(--main-text)] bg-white/5 text-[var(--main-text)] pl-3' 
                    : 'text-[var(--main-text)]/70 hover:bg-white/10 hover:text-[var(--main-text)] border-transparent pl-4'
                )"
            class="block text-[11px] p-2 rounded-md border-l-4 transition-all duration-300 ease-in-out transform active:scale-95">
            
            <div class="flex items-center">
                <flux:icon :name="$sub['icon']" class="mr-2 h-4 w-4 shrink-0" />
                <span class="inline-block text-ellipsis whitespace-nowrap">{{ $sub['label'] }}</span>
            </div>
        </a>
    @endforeach
</div>