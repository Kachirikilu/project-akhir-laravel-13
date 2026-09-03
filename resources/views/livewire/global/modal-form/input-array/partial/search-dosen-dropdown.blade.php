<div>
    <div wire:key="res-dosen-{{ $typeXString }}-{{ $x['id'] }}"
        class="text-xs sm:text-sm flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90 transition-colors">

        @include('livewire.global.modal-form.input-array.partial.dropdown-items')

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
            :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white hover:bg-red-500 active:bg-red-600' :
                'bg-[var(--focus-color)] text-white'"
            class="p-1.5 rounded-md transition-all group">

            @include('livewire.global.modal-form.partial.dropdown-select')
        </button>
    </div>
</div>
