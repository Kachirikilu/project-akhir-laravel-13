<div class="relative"
    wire:key="search-array-{{ $typeXString }}-{{ $selectX }}-{{ $key ?? 'default' }}-{{ str_replace('.', '-', $idString) }}-{{ $alpine }}"
    x-data="{
        open: false,
        search: @isset($nameSearchString) @entangle($nameSearchString).live @else null @endisset,
        items: @isset($idString) @entangle($idString).live @else [] @endisset,
        itemsAll: @isset($itemsAllString) @entangle($itemsAllString).live @else [] @endisset,
        hasParent: {{ isset($parentIdString) ? 'true' : 'false' }},
        parentSelectedId: @isset($parentIdString) @entangle($parentIdString).live @else null @endisset,
    
        init() {
            if (!Array.isArray(this.items)) this.items = [];
            if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
        },
    
        get isParentReady() {
            if (!this.hasParent) {
                return true;
            }
    
            if (Array.isArray(this.parentSelectedId)) {
                return this.parentSelectedId.length > 0;
            }
    
            return this.parentSelectedId != null && this.parentSelectedId !== '';
        },
    
        addItem(id, kode, slot1, slot2, slot3, slot4, slot5, link) {
            let normalizedId = Number(id);
            if (!this.items.map(i => Number(i)).includes(normalizedId)) {
                this.items.push(normalizedId);
    
                this.itemsAll.push({
                    kode: kode,
                    slot1: slot1,
                    slot2: slot2,
                    slot3: slot3,
                    slot4: slot4,
                    slot5: slot5,
                    link: link
                });
            }
        },
    
        removeItem(index) {
            this.items.splice(index, 1);
            this.itemsAll.splice(index, 1);
        },
    
        move(index, direction) {
            let to = index + direction;
            if (to < 0 || to >= this.items.length) return;
            const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];
            swap(this.items, index, to);
            swap(this.itemsAll, index, to);
        },
    
        resetItems() {
            Flux.modal('reset-confirm-modal-{{ $idString }}').show();
        },
    
        clearAllItems() {
            this.items = [];
            this.itemsAll = [];
        },
    }">

    {{-- 1. INPUT SEARCH --}}
    @include('livewire.global.modal-form.partial.label')
    @include('livewire.global.modal-form.input-array.partial.input-search', [
        'typeInput' => 'array',
        'searchKey' => $key ?? 'default',
        'nameSearchString' => $nameSearchString,
    ])

    {{-- 2. DROPDOWN HASIL --}}
    <div x-show="open && isParentReady" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @click.stop
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-72 overflow-y-auto">

        {{-- KONTEN LIST (Akan transparan saat loading) --}}
        <div
            @if ($wireLoadingParent ?? null) wire:target="{{ $wireLoadingParent }}, {{ $wireLoading }}" wire:loading.class="opacity-60 pointer-events-none" @endif">
            @forelse ($xResults as $x)
                @php
                    $itemId = data_get($x, 'id');
                    $itemKode = data_get($x, 'kode', '');
                    $itemLabel = data_get($x, $typeXString, '');
                    $itemLabel2 = isset($typeX2String) ? data_get($x, $typeX2String, '') : null;
                    $itemLabel3 = isset($typeX3String) ? data_get($x, $typeX3String, '') : null;
                    $itemLabel4 = isset($typeX4String) ? data_get($x, $typeX4String, '') : null;
                    $itemLabel5 = isset($typeX5String) ? data_get($x, $typeX5String, '') : null;
                    $itemLink = isset($typeLinkString) ? data_get($x, $typeLinkString, '') : null;
                @endphp

                @if ($itemId !== null)
                        @include('livewire.global.modal-form.input-array.partial.search-array-dropdown')

                   
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

    @error($id2String ?? $idString)
        <span class="text-red-500 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
    @enderror

    {{-- 3. AREA OPSI TERPILIH (DI DALAM KOTAK) --}}
    <div
        class="mt-4 p-4 border-2 border-dashed table-border rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">

        <div class="flex items-center justify-between mb-4">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-gray-400">Daftar Terpilih:</span>
            <div class="flex items-center gap-2">

                @include('livewire.global.modal-form.partial.reset-all-buttons')

                <span x-show="(items?.length || 0) > 0"
                    class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full"
                    x-text="(items?.length || 0) + ' Terpilih'"></span>
            </div>
        </div>

        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1 scrollbar-medium">
            {{-- <template x-for="(id, index) in items" :key="id"> --}}
            <template x-for="(id, index) in items" :key="id + '-' + index">
                <div
                    class="group relative flex items-start justify-between bg-[var(--second-table-color)] border table-border px-3 py-3 rounded-lg shadow-sm transition-all hover:border-[var(--focus-color)] active:border-[var(--focus-color)]/90">
                    <div class="flex items-start gap-3 flex-1">

                        <span class="text-xs font-black text-[var(--hover-focus-color)] w-4 mt-0.5"
                            x-text="index + 1"></span>

                        <div class="flex flex-col gap-1 flex-1">
                            <div class="flex items-center gap-2">
                                @if ($idString == 'mahasiswa_id_array')
                                    <span
                                        class="text-xs font-bold mb-1.5 px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white"
                                        x-text="'NIM: ' + itemsAll[index]?.kode"></span>
                                @else
                                    <span
                                        class="text-xs font-bold mb-1.5 px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white"
                                        x-text="itemsAll[index]?.kode"></span>
                                @endif
                                <div class="h-px flex-1 mb-1.5 bg-gray-200 dark:bg-neutral-800 opacity-40"></div>
                            </div>

                            <span class="text-xs sm:text-sm mb-1 font-semibold text-[var(--contrast-main-text)] leading-tight"
                                x-text="itemsAll[index]?.slot1"></span>

                            <div class="flex items-center flex-wrap text-xs text-[var(--contrast-second-text)] gap-y-1">
                                -<span class="ml-1 font-bold text-[var(--hover-focus-color)]"
                                    x-text="'ID: ' + id"></span>

                                @if ($typeX2String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot2"></span>
                                @endif

                                @if ($typeX3String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot3"></span>
                                @endif

                                @if ($typeX4String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot4"></span>
                                @endif

                                @if ($typeX4String ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <span x-text="itemsAll[index]?.slot5"></span>
                                @endif

                                @if ($typeLinkString ?? null)
                                    <span class="mx-1.5 opacity-50">|</span>
                                    <template x-if="itemsAll[index]?.link && itemsAll[index]?.link.trim() !== ''">
                                        <a :href="itemsAll[index]?.link" target="_blank"
                                            class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 hover:underline active:underline text-xs font-bold">
                                            <flux:icon.link variant="micro" /> 
                                            <span x-text="itemsAll[index]?.link"></span>
                                        </a>
                                    </template>

                                    <template x-if="!itemsAll[index]?.link || itemsAll[index]?.link.trim() === ''">
                                        <div class="flex items-center gap-1 text-gray-400 dark:text-gray-600 text-xs font-medium cursor-not-allowed select-none">
                                            <flux:icon.link variant="micro" class="opacity-50" /> 
                                            <span>Tidak ada tautan</span>
                                        </div>
                                    </template>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    @include('livewire.global.modal-form.partial.action-buttons')

                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="(items?.length || 0) === 0"
                class="pt-6 pb-12 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="list-bullet" variant="outline" class="mb-1" />
                <p class="text-xs italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>
    </div>
</div>
