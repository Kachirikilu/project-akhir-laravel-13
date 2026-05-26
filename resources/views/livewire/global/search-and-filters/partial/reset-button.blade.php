<button type="button" x-show="{{ $xShow }}" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-50"
    @click="
        open = false; 
        {{ $xClick ? $xClick . ';' : '' }} 
        {{ isset($xWire) ? '$wire.' . $xWire . ';' : '' }} 
        {{ isset($xWire2) ? '$wire.' . $xWire2 . ';' : '' }}
        @if ($xAlpine ?? null) $store.{{ $alpine ?? 'config' }}.{{ $xAlpine }} = ''; @endif
        @if ($xAlpine2 ?? null) $store.{{ $alpine ?? 'config' }}.{{ $xAlpine2 }} = '' @endif
    "
    class="cursor-pointer
        @if (!($isRelative ?? false)) absolute inset-y-0 right-0 @endif
         flex items-center pr-{{ $xPr ?? 3 }} {{ $xColor ?? 'text-[var(--contrast-main-text)]' }} hover:text-red-500 dark:hover:text-red-400 transition duration-200"
    @empty($xColor)
        x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon || 'text-[var(--contrast-main-text)]'"
    @endempty
    title="Reset">
    <svg class="h-{{ $xSize ?? 5 }} w-{{ $xSize ?? 5 }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
</button>
