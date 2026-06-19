<button @click="activeTab = '{{ $tabString }}'; $wire.{{ $xString }}('{{ $tabString }}')"
    class="text-xs sm:text-sm relative cursor-pointer flex items-center justify-center pt-2.5 pb-3 pl-3 pr-8 font-medium transition-all duration-300 whitespace-nowrap outline-none bg-transparent group"
    :class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}' 
        ? 'text-[var(--focus-color)] font-semibold' 
        : 'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90'">

    <!-- Wrapper Konten -->
    <div class="flex items-center gap-2">
        
        <!-- Icon Flux -->
        @if(isset($icon))
            <flux:icon :name="$icon" class="h-4 w-4 transition-colors duration-300" 
                ::class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}' ? 'text-[var(--focus-color)]' : 'text-[var(--contrast-second-text)] group-hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90'" />
        @endif
        
        <!-- Label Text -->
        <span class="tracking-wider text-[9px] sm:text-xs uppercase">{{ $tabNameString ?? str($tabString)->replace(['-', '_'], ' ')->ucfirst() }}</span>
        
        <!-- Badge Jumlah Minimalis -->
        @if (!is_null($tabFilter ?? null))
            <span class="text-[9px] sm:text-xs px-1.5 py-0.5 font-bold rounded-sm transition-colors duration-300 border"
                :class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}'
                    ? 'bg-[var(--focus-color)] text-white border-transparent' 
                    : 'bg-transparent text-[var(--contrast-second-text)] table-border group-hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90 group-hover:border-[var(--focus-color)] active:border-[var(--focus-color)]/90/40'">
                {{ $tabFilter }}
            </span>
        @endif
    </div>

    <!-- Garis Dasar Default (Tipis & Statis di Bagian Bawah) -->
    <span class="absolute bottom-0 left-0 w-full h-[1px] bg-[var(--border-table-color)]"></span>

    <!-- Garis Indikator Aktif/Hover (Lebih Tebal & Animasi Meluncur dari Kiri) -->
    <span 
        x-cloak
        class="bg-[var(--focus-color)] absolute bottom-0 left-0 h-[3px] transition-transform duration-300 ease-out origin-left w-full z-10"
        :class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}' 
            ? 'scale-x-100' 
            : 'scale-x-0 group-hover:scale-x-100 group-active:scale-x-100'"
    ></span>
</button>