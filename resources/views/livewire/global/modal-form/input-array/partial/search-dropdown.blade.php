<div>
    <div wire:key="res-{{ $x[$typeXString] }}-{{ $x['id'] }}"
        @click="
            let itemId = {{ $x['id'] }};
            let newSearch = '{{ $x[$typeXString] }}';
            let newKode = '{{ filled($x['kode']) ? $x['kode'] : 'UNI' }}';

            isManual = true;
            search = newSearch;
            items = itemId;
            itemsAll = { 
                id: itemId,
                kode: newKode,
                slot1: '{{ $x[$typeXString] ?? '' }}',
                slot2: '{{ isset($typeX2String) ? $x[$typeX2String] ?? '' : '' }}',
                slot3: '{{ isset($typeX3String) ? $x[$typeX3String] ?? '' : '' }}',
                slot4: '{{ isset($typeX4String) ? $x[$typeX4String] ?? '' : '' }}'
            };

            $store.{{ $alpine ?? 'config' }}['{{ $idString }}'] = items;
            $store.{{ $alpine ?? 'config' }}['{{ $itemsAllString }}'] = itemsAll;
            $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = newSearch;

            open = false;
            
            $wire.{{ $selectX }}(itemId, newSearch).then(() => {
                isManual = false;
            });
        "
        class="px-4 py-2 cursor-pointer transition-colors duration-200
               bg-[var(--main-pop-up-color)] border-[var(--focus-color)]
               hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90 text-sm">

        <div class="flex flex-wrap items-start gap-x-4 gap-y-1">
            
            <div class="flex-1 min-w-[200px] text-xs sm:text-sm">
                @include('livewire.global.modal-form.input-array.partial.dropdown-items')
            </div>

            <div class="shrink-0">
                <span class="my-2 inline-block bg-[var(--focus-color)] text-[var(--main-text)] text-xs px-2 py-1 rounded-md">
                    {{ filled($x['kode']) ? $x['kode'] : 'UNI' }}
                </span>
            </div>

        </div>
    </div>
</div>