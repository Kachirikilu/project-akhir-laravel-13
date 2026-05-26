<div wire:key="{{ $key ?? 'page-control-default' }}" 
     class="flex items-center justify-end {{ $withFull ?? true ? ($withPM ?? true ? 'pb-4 ml-4' : '') : '' }}">

    <div x-data="{ open: false, selected: @entangle('perPage').live }" class="relative w-15" @click.away="open = false">
        {{-- Tombol Utama --}}
        <button type="button" @click="open = !open"
            class="cursor-pointer flex items-center justify-between border rounded-md shadow-sm 
                   bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-second-text)] py-1 px-2 text-sm w-full
                   hover:border-[var(--hover-focus-color)] transition-[border-color] duration-200">
            <span x-text="selected">8</span>
            <svg class="h-4 w-4 ml-1 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <ul x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-h-40 overflow-y-auto scrollbar-thin bg-[var(--main-pop-up-color)] ring-[var(--focus-color)] absolute z-100 mt-1 w-full rounded-md shadow-lg ring-1 ring-opacity-5 focus:outline-none overflow-hidden"
            role="menu" aria-orientation="vertical" tabindex="-1">

            @foreach ($perPageOptions as $option)
                <li wire:key="perPage-{{ $option }}" @click="selected = {{ $option }}; open = false"
                    class="block px-3 py-1 text-sm cursor-pointer transition-colors duration-200
                           hover:bg-[var(--hover-main-color)]
                           hover:text-[var(--main-text)]"
                    :class="{
                        'bg-[var(--main-color)] text-[var(--main-text)] dark:text-[var(--hover-focus-color)] font-semibold': selected ==
                            {{ $option }}
                    }">
                    {{ $option }}
                </li>
            @endforeach
        </ul>
    </div>

    @if ($withFull ?? true)
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-2">Baris</span>
    @endif

</div>
