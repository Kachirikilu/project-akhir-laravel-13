<div>
    <div wire:key="res-dosen-{{ $typeXString }}-{{ $x['id'] }}"
        class="text-xs sm:text-sm flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90 transition-colors">

        <div class="flex flex-col mr-4 min-w-0 flex-1">
            {{-- Nama/Judul Utama agar otomatis patah kata jika terlalu panjang --}}
            <span class="font-medium text-[var(--contrast-main-text)] break-words">{{ $x[$typeXString] }}</span>

            {{-- Container Info: Ditambahkan flex-wrap, gap-y-1, dan perbaikan penutup kurung ] pada variabel warna --}}
            <div class="text-[var(--contrast-main-text)] font-medium text-xs flex flex-wrap items-center mt-1 min-w-0">
                <span class="flex items-center shrink-0">
                    - <span class="text-[var(--hover-focus-color)] font-bold">ID: {{ $x['id'] }}</span>
                </span>

                <span class="mx-2 text-[var(--contrast-second-text)] opacity-50 shrink-0">|</span>
                <span class="shrink-0">NIP: {{ $x['kode'] }}</span>

                @if (filled($x[$typeX2String] ?? null))
                    <span class="mx-2 text-[var(--contrast-second-text)] opacity-50">|</span>
                    <span>NIDN: {{ $x[$typeX2String] }}</span>
                @endif

                @if (filled($x[$typeX3String] ?? null))
                    <span class="mx-2 text-[var(--contrast-second-text)] opacity-50">|</span>
                    <span>NIDK: {{ $x[$typeX3String] }}</span>
                @endif

                @if (filled($x[$typeX4String] ?? null))
                    <span class="mx-2 text-[var(--contrast-second-text)] opacity-50">|</span>
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
            :class="items.includes({{ $x['id'] }}) ? 'bg-green-500 text-white hover:bg-red-500 active:bg-red-600' :
                'bg-[var(--focus-color)] text-white'"
            class="p-1.5 rounded-md transition-all group">

            @include('livewire.global.modal-form.partial.dropdown-select')
        </button>
    </div>
</div>
