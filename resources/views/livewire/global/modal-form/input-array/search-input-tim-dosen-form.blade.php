<div>
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
                if (!Array.isArray(this.subItems)) this.subItems = [];
        
                this.$nextTick(() => {
                    this.syncToTimDosenStore();
                });
        
                this.$watch('subItems', (value) => {
                    this.syncToTimDosenStore();
                });
        
            },
        
            syncToTimDosenStore() {
                if (typeof $store.tim_dosen !== 'undefined') {
                    {{-- $store.tim_dosen.update(this.subItems || []); --}}
                }
            },
        
            parentSelectedId: @entangle($parentIdString ?? null).live,
        
            get isParentReady() {
                return this.parentSelectedId != null && this.parentSelectedId != '';
            },
        
            addItem(id, kode, slot1, slot2, slot3, slot4, validation, subData) {
                let normalizedId = Number(id);
                if (!this.items.map(i => Number(i)).includes(normalizedId)) {
                    this.items.push(normalizedId);
        
                    this.itemsAll.push({
                        kode: kode,
                        slot1: slot1,
                        slot2: slot2,
                        slot3: slot3,
                        slot4: slot4,
                        validation: validation
                    });
        
                    this.subItems.push(subData);
                    this.syncToTimDosenStore();
                }
            },
        
            removeItem(index) {
                this.items.splice(index, 1);
                this.itemsAll.splice(index, 1);
                this.subItems.splice(index, 1);
        
                if (this.expanded === index) this.expanded = null;
                this.syncToTimDosenStore();
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
        
                this.syncToTimDosenStore();
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
            class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[110] mt-1 rounded-lg shadow-2xl {{ $maxH ?? 'max-h-80' }} overflow-y-auto">

            <div>
                @forelse ($xResults as $x)
                    @include('livewire.global.modal-form.input-array.partial.search-tim-dosen-dropdown')
                @empty
                    <div class="p-8 text-center">
                        <p class="text-xs sm:text-sm text-gray-500 italic">Data tidak ditemukan!</p>
                    </div>
                @endforelse
            </div>
        </div>

        @error($idString)
            <span class="text-red-500 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
        @enderror


        {{-- 3. AREA OPSI TERPILIH --}}
        <div class="mt-4 p-4 border-2 border-dashed table-border rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">

            {{-- Daftar Item Berjejer ke Bawah (flex-col) --}}
            <div class="space-y-2 max-h-[625px] overflow-y-auto pr-1 scrollbar-medium">
                {{-- <template x-for="(id, index) in items" :key="id"> --}}
                <template x-for="(id, index) in items" :key="id + '-' + index">
                    <div
                        class="flex flex-col bg-[var(--second-table-color)] border table-border rounded-xl shadow-sm overflow-hidden transition-all mb-3 hover:border-[var(--focus-color)] active:border-[var(--focus-color)]/90">
                        @include('livewire.global.modal-form.input-array.partial.tim-dosen-header')
                        @include('livewire.global.modal-form.input-array.partial.dosen-table')
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="items.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                    <flux:icon icon="academic-cap" variant="outline" class="mb-2 w-8 h-8" />
                    <p class="text-xs font-medium italic">Belum ada {{ $nameXString }} yang dipilih!</p>
                </div>
            </div>


        </div>

    </div>
</div>
