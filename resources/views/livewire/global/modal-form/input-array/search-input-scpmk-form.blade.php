<div class="relative" wire:key="search-array-associative-{{ $typeXString }}-{{ $selectX }}-{{ $alpine }}"
    x-data="{
        open: false,
        search: @entangle($nameSearchString).live,
        items: @entangle($idString).live,
        itemsAll: @entangle($itemsAllString).live,
        subItems: @entangle($subItemsString).live,
    
        expanded: [],
    
        init() {
            if (!Array.isArray(this.items)) this.items = [];
            if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
        },
    
        init() {
            if (!Array.isArray(this.items)) this.items = [];
            if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
            if (!Array.isArray(this.subItems)) this.subItems = [];
    
            this.$nextTick(() => {
                this.syncToScpmkStore();
            });
    
            this.$watch('subItems', (value) => {
                this.syncToScpmkStore();
            });
        },
    
    
        syncToScpmkStore() {
            if (typeof $store.cpmk !== 'undefined') {
                $store.cpmk.update(this.subItems);
    
                $store.cpmk.setCountSCPMK(this.totalSubCPMK);
                $store.cpmk.total_bobot = this.grandTotalBobot;
            }
        },
    
        get grandTotalBobot() {
            return (this.subItems || []).reduce((total, item) => {
                // Karena subItems[index] berisi { ref: [...], scpmk: [...] }
                const subArray = item?.scpmk || [];
                return total + subArray.reduce((subTotal, sub) => {
                    return subTotal + (parseFloat(sub?.bobot) || 0);
                }, 0);
            }, 0);
        },
    
        parentSelectedId: @entangle($parentIdString ?? null).live,
    
        get isParentReady() {
            return this.parentSelectedId != null && this.parentSelectedId != '';
        },
    
        addItem(id, kode, slot1, slot2, slot3, subData) {
            let normalizedId = Number(id);
            if (!this.items.map(i => Number(i)).includes(normalizedId)) {
                this.items.push(normalizedId);
    
                this.itemsAll.push({
                    kode: kode,
                    slot1: slot1,
                    slot2: slot2,
                    slot3: slot3
                });
    
                this.subItems.push(subData);
                this.syncToScpmkStore();
            }
        },
    
        removeItem(index) {
            this.items.splice(index, 1);
            this.itemsAll.splice(index, 1);
            this.subItems.splice(index, 1);
    
            if (this.expanded === index) this.expanded = null;
            this.syncToScpmkStore();
        },
    
        move(index, direction) {
            let to = index + direction;
            if (to < 0 || to >= this.items.length) return;
            const swap = (arr, a, b) => [arr[a], arr[b]] = [arr[b], arr[a]];
    
            swap(this.items, index, to);
            swap(this.itemsAll, index, to);
            swap(this.subItems, index, to);
    
            if (this.expanded === index) this.expanded = to;
            else if (this.expanded === to) this.expanded = index;
    
            this.syncToScpmkStore();
        },
    
    
        get totalSubCPMK() {
            return (this.subItems || []).reduce((total, item) => {
                return total + (item?.scpmk?.length || 0);
            }, 0);
        },
    
        resetItems() {
            Flux.modal('reset-confirm-modal-{{ $idString }}').show();
        },
    
        clearAllItems() {
            this.items = [];
            this.itemsAll = [];
            this.subItems = [];
        },
    }">

    {{-- 1. INPUT SEARCH --}}
    @include('livewire.global.modal-form.partial.label')
    @include('livewire.global.modal-form.input-array.partial.input-search', ['typeInput' => 'array'])

    {{-- 2. DROPDOWN HASIL --}}
    <div x-show="open && isParentReady" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @click.stop
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl max-h-72 overflow-y-auto">

        <div>
            @forelse ($xResults as $x)
                <div wire:key="res-{{ $typeXString }}-{{ $x['id'] }}-{{ $alpine }}"
                    class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-neutral-700 hover:bg-[var(--hover-pop-up-color)] transition-colors">

                    @include('livewire.global.modal-form.input-array.partial.dropdown-items')

                    <button type="button"
                        x-on:click="
                            if (items.includes({{ $x['id'] }})) {
                                let index = items.indexOf({{ $x['id'] }});
                                if (index !== -1) {
                                    items.splice(index, 1);
                                    itemsAll.splice(index, 1);
                                    subItems.splice(index, 1);
                                }
                            } else {
                                addItem(
                                    {{ $x['id'] }}, 
                                    '{{ $x['kode'] }}', 
                                    '{{ $x[$typeXString] }}', 
                                    @isset($typeX2String) '{{ $x[$typeX2String] ?? '' }}' @else null @endisset, 
                                    @isset($typeX3String) '{{ $x[$typeX3String] ?? '' }}' @else null @endisset,
                                    { 
                                        scpmk: [
                                            {
                                                id: {{ $x['id'] }},
                                                kode: '{{ $x['kode'] }}',
                                                deskripsi: '{{ $x['deskripsi'] }}',
                                                materi: '{{ $x['materi'] }}',
                                                metodologi: '{{ $x['metodologi'] }}',
                                                indikator: '{{ $x['indikator'] }}',
                                                metode: '{{ $x['metode'] }}',
                                                bobot: {{ $x['bobot'] }},
                                                w_tugas: '{{ $x['w_tugas'] }}',
                                                w_mandiri: '{{ $x['w_mandiri'] }}',
                                                tugas: '{{ $x['deskripsi'] }}',
                                                ref: {{ json_encode($x['ref']) }},
                                            }
                                        ]
                                        {{-- ref: {{ json_encode($x['ref']) }} --}}
                                    }
                                );
                            }
                        "
                        x-bind:class="items.includes({{ $x['id'] }}) ? 'bg-green-500 hover:bg-red-500' :
                            'bg-[var(--focus-color)]'"
                        class="p-1.5 rounded-md text-white transition-all shadow-sm group">

                        @include('livewire.global.modal-form.partial.dropdown-select')

                    </button>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                </div>
            @endforelse
        </div>
    </div>

    @error($idString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror

    {{-- 3. AREA OPSI TERPILIH --}}
    <div
        class="mt-4 p-4 border-2 border-dashed table-border rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">

        @include('livewire.global.modal-form.input-array.partial.scpmk-bobot-akumulasi', [
            'nilai1' => 5,
            'nilai2' => 15,
            'nilai3' => 25,
        ])

        {{-- Daftar Item Berjejer ke Bawah (flex-col) --}}
        <div class="space-y-2 max-h-[625px] overflow-y-auto pr-1 scrollbar-medium">
            {{-- <template x-for="(id, index) in items" :key="id"> --}}
            <template x-for="(id, index) in items" :key="id + '-' + index">
                <div
                    class="flex flex-col bg-[var(--second-table-color)] border table-border rounded-xl shadow-sm overflow-hidden transition-all mb-3 hover:border-[var(--focus-color)]">
                    @include('livewire.global.modal-form.input-array.partial.scpmk-header')
                    @include('livewire.global.modal-form.input-array.partial.scpmk-table')
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="academic-cap" variant="outline" class="mb-2 w-8 h-8" />
                <p class="text-xs font-medium italic">Belum ada {{ $nameXString ?? ucfirst($modelString) }} yang
                    dipilih!</p>
            </div>
        </div>

        {{-- Footer Keseluruhan (Total Semua Sub-CPMK dari berbagai CPMK) --}}
        @include('livewire.global.modal-form.input-array.partial.scpmk-bobot-pesan', [
            'nilai1' => 5,
            'nilai2' => 15,
            'nilai3' => 25,
            'pNilai1' => 'Bobot Kecil:',
            'pNilai2' => 'Bobot Sedang:',
            'pNilai3' => 'Bobot Besar:',
            'pNilai4' => 'Bobot Sangat:',
        ])

    </div>

</div>
