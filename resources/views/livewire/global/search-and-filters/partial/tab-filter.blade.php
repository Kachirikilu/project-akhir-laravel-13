{{-- <button wire:click="{{ $xString }}('{{ $tabString }}')"
    class="cursor-pointer {{ isset($xFilter) && $xFilter == $tabString ? 'border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:text-indigo-700' }} 
                    tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-150 border-b-2 whitespace-nowrap">
    <i class="fas fa-users mr-2"></i> {{ $tabNameString }} (<span
        id="count-all">{{ $tabFilter }}</span>)
</button> --}}
<button @click="activeTab = '{{ $tabString }}'; $wire.{{ $xString }}('{{ $tabString }}')"
    class="relative cursor-pointer tab-mode px-2 py-2 text-sm font-medium rounded-t-lg transition duration-200 whitespace-nowrap group focus:outline-none"
    :class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}' 
        ? 'text-[var(--focus-color)]' 
        : 'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)]'">

    <div class="flex items-center">
        <i class="fas fa-users mr-2"></i>
        {{ $tabNameString ?? str($tabString)->replace(['-', '_'], ' ')->ucfirst() }}
        @if (!is_null($tabFilter))
            <span class="ml-1">({{ $tabFilter }})</span>
        @endif
    </div>

    {{-- Garis Indikator Bawah --}}
    <span 
        x-cloak
        class="bg-[var(--focus-color)] absolute bottom-0 left-0 h-0.5 transition-all duration-300 ease-in-out transform origin-left"
        :class="activeTab == '{{ $tabString }}' || activeTab == '{{ $tabHiddenString ?? $tabString }}'  ? 'w-full scale-x-100' : 'w-full scale-x-0 group-hover:scale-x-100'"
    ></span>
</button>
