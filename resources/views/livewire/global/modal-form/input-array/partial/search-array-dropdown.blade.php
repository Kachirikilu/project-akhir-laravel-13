<div wire:key="res-array-{{ $typeXString }}-{{ $itemId }}-{{ $alpine }}"
    class="text-xs sm:text-sm flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90 transition-colors">

    @include('livewire.global.modal-form.input-array.partial.dropdown-items')

    @php
        $param2 = isset($typeX2String) ? "'" . addslashes($itemLabel2) . "'" : 'null';
        $param3 = isset($typeX3String) ? "'" . addslashes($itemLabel3) . "'" : 'null';
        $param4 = isset($typeX4String) ? "'" . addslashes($itemLabel4) . "'" : 'null';
        $param5 = isset($typeX5String) ? "'" . addslashes($itemLabel5) . "'" : 'null';
        $paramLink = isset($typeLinkString) ? "'" . addslashes($itemLink) . "'" : 'null';
    @endphp
    <button type="button"
        @click="
                            if (items.includes({{ $itemId }})) {
                                let index = items.indexOf({{ $itemId }});
                                if (index !== -1) {
                                    items.splice(index, 1);
                                    itemsAll.splice(index, 1);
                                }
                            } else {
                               addItem(
                                    {{ $itemId }}, 
                                    '{{ addslashes($itemKode) }}', 
                                    '{{ addslashes($itemLabel) }}', 
                                    {{ $param2 }}, 
                                    {{ $param3 }},
                                    {{ $param4 }},
                                    {{ $param5 }},
                                    {{ $paramLink }}
                                );
                                @isset($selectX)
                                    $wire.{{ $selectX }}({{ $itemId }}@isset($key), '{{ addslashes($key) }}'@endisset);
                                @endisset
                            }
                            "
        :class="items.includes({{ $itemId }}) ? 'bg-green-500 text-white hover:bg-red-500 active:bg-red-600' :
            'bg-[var(--focus-color)] text-white'"
        class="p-1.5 rounded-md transition-all group">


        @include('livewire.global.modal-form.partial.dropdown-select')

    </button>
</div>
