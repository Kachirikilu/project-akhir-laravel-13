<div wire:key="res-dosen-{{ $typeXString }}-{{ $x['id'] }}"
    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">

    <div class="flex flex-col mr-4">
        <span class="text-sm font-medium text-[var(--contrast-main-text)]">{{ $x[$typeXString] }}</span>
        <div class="text-[var(--contrast-main-text) font-medium text-xs flex items-center mt-1">
            <span>- <span class="text-[var(--hover-focus-color)] font-bold">ID:
                    {{ $x['id'] }}</span></span>
            <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
            <span>NIP: {{ $x['kode'] }}</span>
            @if (filled($x[$typeX2String] ?? null))
                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                <span>NIDN: {{ $x[$typeX2String] }}</span>
            @endif

            @if (filled($x[$typeX3String] ?? null))
                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                <span>NIDK: {{ $x[$typeX3String] }}</span>
            @endif

            @if (filled($x[$typeX4String] ?? null))
                <span class="mx-2 text-[var(--contrast-second-text)]">|</span>
                <span>Status: {{ $x[$typeX4String] }}</span>
            @endif
        </div>
    </div>
    <button type="button"
        @click="
                        if (items.includes({{ $x['id'] }})) {
                            let index = items.indexOf({{ $x['id'] }});
                            if (index !== -1) {
                                items.splice(index, 1);
                                itemsAll.splice(index, 1);
                            }
                        } else {
                           addItem(
                                {{ $x['id'] }}, 
                                '{{ $x['kode'] }}', 
                                '{{ $x[$typeXString] }}', 
                                @isset($typeX2String) '{{ $x[$typeX2String] ?? '' }}' @else null @endisset, 
                                @isset($typeX3String) '{{ $x[$typeX3String] ?? '' }}' @else null @endisset,
                                @isset($typeX4String) '{{ $x[$typeX4String] ?? '' }}' @else null @endisset,
                                @isset($typeX5String) '{{ $x[$typeX5String] ?? '' }}' @else null @endisset
                            );
                        }
                        "
        :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white hover:bg-red-500' :
            'bg-[var(--focus-color)] text-white'"
        class="p-1.5 rounded-md transition-all group">

        @include('livewire.global.modal-form.partial.dropdown-select')
    </button>
</div>
