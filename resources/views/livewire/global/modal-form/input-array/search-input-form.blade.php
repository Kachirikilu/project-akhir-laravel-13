<div>
    <div class="relative"
        wire:key="input-array.search-input-form-{{ $typeXString }}-{{ $selectX }}-{{ $alpine }}"
        x-data="{
            open: false,
            search: @entangle($nameSearchString).live,
            items: @entangle($idString).live,
            itemsAll: @entangle($itemsAllString).live,
            isManual: false,
        
            /* Logika Parent */
            hasParent: {{ isset($parentIdString) ? 'true' : 'false' }},
            parentSelectedId: @isset($parentIdString) @entangle($parentIdString).live @else null @endisset,
        
            get isParentReady() {
                if (!this.hasParent) return true;
                if (Array.isArray(this.parentSelectedId)) return this.parentSelectedId.length > 0;
                return this.parentSelectedId != null && this.parentSelectedId !== '';
            }
        }"
        x-effect="
        const config = $store.{{ $alpine ?? 'config' }};
        
        if (config?.isEdit === 0) {
            search = '';
            items = null;
            itemsAll = null;
        } else {
            let currentId = config?.['{{ $idString }}'];

            if (!currentId) {
                // Hanya reset jika tidak sedang dalam proses input manual
                if (!isManual) {
                    search = '';
                    items = null;
                    itemsAll = null;
                }
            } else {
                // Sinkronisasi dari Global Store ke Entangle (State Lokal)
                search = config?.['{{ $modelString }}'];
                items = config?.['{{ $idString }}'];
                itemsAll = config?.['{{ $itemsAllString }}'];
            }
        }
    ">

        @include('livewire.global.modal-form.partial.label')
        @include('livewire.global.modal-form.input-array.partial.input-search', [
            'typeInput' => 'single',
            'searchKey' => $key ?? 'default',
            'nameSearchString' => $nameSearchString,
        ])


        {{-- Info Terpilih --}}
        <div x-show="itemsAll && search" x-cloak>
            <p class="text-[var(--focus-color)] text-xs sm:text-sm mt-1 font-medium italic">
                Terpilih:
                <span x-text="itemsAll?.slot1" class="ml-1"></span>
                <span class="mx-1">|</span>
                Kode: <span x-text="itemsAll?.kode"></span>

                @if ($typeX2String ?? null)
                    <span class="mx-1">|</span>
                    <span x-text="itemsAll?.slot2"></span>
                @endif
                @if ($typeX3String ?? null)
                    <span class="mx-1">|</span>
                    <span x-text="itemsAll?.slot3"></span>
                @endif
                @if ($typeX4String ?? null)
                    <span class="mx-1">|</span>
                    <span x-text="itemsAll?.slot4"></span>
                @endif
                <span class="mx-1">|</span>
                ID: <span x-text="itemsAll?.id"></span>
            </p>
        </div>

        {{-- DROPDOWN HASIL --}}
        <div x-show="open && isParentReady" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl {{ $maxH ?? 'max-h-80' }} overflow-y-auto custom-scrollbar">

            <div
                @if ($wireLoadingParent ?? null) wire:target="{{ $wireLoadingParent }}" wire:loading.class="opacity-60 pointer-events-none" @endif>

                @forelse ($xResults as $x)
                    @php
                        $itemId = data_get($x, 'id');
                    @endphp
                    @if ($itemId !== null)
                        @include('livewire.global.modal-form.input-array.partial.search-dropdown')
                    @endif
                @empty
                    <div class="p-4 text-center">
                        <div wire:loading @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                            <p class="text-xs sm:text-sm text-[var(--focus-color)] font-medium animate-pulse">
                                Sedang mencari data {{ $nameXString ?? null }}...
                            </p>
                        </div>

                        <div wire:loading.remove
                            @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 italic">
                                Data {{ $nameXString ?? null }} tidak ditemukan!
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
        @error($idString)
            <span class="text-red-500 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
        @enderror
    </div>
</div>
