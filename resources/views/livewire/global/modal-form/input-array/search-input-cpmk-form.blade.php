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
                this.syncToRpsStore();
            });
    
            this.$watch('subItems', (value) => {
                this.syncToRpsStore();
            });
    
            this.$watch('$store.rps.bobot_uts', () => this.syncToRpsStore());
            this.$watch('$store.rps.bobot_uas', () => this.syncToRpsStore());
        },
    
        syncToRpsStore() {
            if (typeof $store.rps !== 'undefined') {
                $store.rps.update(this.subItems || []);
                $store.rps.setCountSCPMK(this.totalSubCPMK);
                $store.rps.total_bobot = this.grandTotalBobot;
            }
        },
    
        get grandTotalBobot() {
            let totalSubCPMK = (this.subItems || []).reduce((total, item) => {
                const subArray = item?.scpmk || [];
                return total + subArray.reduce((subTotal, sub) => {
                    const p = parseInt(sub?.pertemuan);
                    if (p === 8 || p === 16) return subTotal;
    
                    return subTotal + (parseFloat(sub?.bobot) || 0);
                }, 0);
            }, 0);
    
            const customUts = parseFloat($store.rps.bobot_uts) || 0;
            const customUas = parseFloat($store.rps.bobot_uas) || 0;
    
            // 3. Jika kustom kosong, cari nilai default dari Sub-CPMK (pertemuan 8 & 16)
            let defaultUts = 0;
            let defaultUas = 0;
    
            if (!customUts || !customUas) {
                (this.subItems || []).forEach(item => {
                    (item?.scpmk || []).forEach(sub => {
                        if (parseInt(sub.pertemuan) === 8) defaultUts += parseFloat(sub.bobot) || 0;
                        if (parseInt(sub.pertemuan) === 16) defaultUas += parseFloat(sub.bobot) || 0;
                    });
                });
            }
    
            return totalSubCPMK + (customUts || defaultUts) + (customUas || defaultUas);
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
                this.syncToRpsStore();
            }
        },
    
        removeItem(index) {
            this.items.splice(index, 1);
            this.itemsAll.splice(index, 1);
            this.subItems.splice(index, 1);
    
            if (this.expanded === index) this.expanded = null;
            this.syncToRpsStore();
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
    
            this.syncToRpsStore();
        },
    
    
        get totalSubCPMK() {
            if (!this.subItems) return 0;
            return this.subItems.reduce((total, item) => {
                let subArray = item.scpmk || [];
                return total + subArray.length;
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
                @include('livewire.global.modal-form.input-array.partial.search-cpmk-dropdown')
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

    <div class="grid sm:grid-cols-4 gap-3 items-start" x-data="{}"
        x-effect="$store.{{ $alpine ?? 'config' }}.kode_cpl = ($store.{{ $alpine ?? 'config' }}.kode_cpl_1 || '') + ($store.{{ $alpine ?? 'config' }}.kode_cpl_2 || '')">

        <div class="sm:col-span-2 mt-4">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'rps',
                'nameXString' => 'Bobot UTS (Kustom)',
                'modelString' => 'bobot_uts',
                'floatOnly' => 1,
                'maxValue' => 100,
                'iconString' => 'variable',
                'placeholder' => 'Default menggunakan Sub-CPMK...',
                'message' => $errors->first('bobot_uts'),
                'isRequired' => 0,
            ])
        </div>
        <div class="sm:col-span-2 mt-4">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'rps',
                'nameXString' => 'Bobot UAS (Kustom)',
                'modelString' => 'bobot_uas',
                'floatOnly' => 1,
                'maxValue' => 100,
                'iconString' => 'variable',
                'placeholder' => 'Default menggunakan Sub-CPMK...',
                'message' => $errors->first('bobot_uas'),
                'isRequired' => 0,
            ])
        </div>
    </div>

    {{-- 3. AREA OPSI TERPILIH --}}
    <div
        class="mt-4 p-4 border-2 border-dashed table-border rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">
        @include('livewire.global.modal-form.input-array.partial.scpmk-bobot-akumulasi', [
            'nilai1' => 20,
            'nilai2' => 70,
            'nilai3' => 200,
        ])

        {{-- Daftar Item Berjejer ke Bawah (flex-col) --}}
        <div class="space-y-2 max-h-[625px] overflow-y-auto pr-1 scrollbar-medium">
            {{-- <template x-for="(id, index) in items" :key="id"> --}}
            <template x-for="(id, index) in items" :key="id + '-' + index">
                <div
                    class="flex flex-col bg-[var(--second-table-color)] border table-border rounded-xl shadow-sm overflow-hidden transition-all mb-3 hover:border-[var(--focus-color)] active:border-[var(--focus-color)]/90">
                    @include('livewire.global.modal-form.input-array.partial.scpmk-header')
                    @include('livewire.global.modal-form.input-array.partial.scpmk-table')
                </div>
            </template>

            {{-- Empty State --}}
            <div x-show="items.length === 0" class="py-12 flex flex-col items-center justify-center opacity-40">
                <flux:icon icon="academic-cap" variant="outline" class="mb-2 w-8 h-8" />
                <p class="text-xs font-medium italic">Belum ada {{ $nameXString }} yang dipilih!</p>
            </div>
        </div>

        {{-- Footer Keseluruhan (Total Semua Sub-CPMK dari berbagai CPMK) --}}
        @include('livewire.global.modal-form.input-array.partial.scpmk-bobot-pesan', [
            'nilai1' => 20,
            'nilai2' => 70,
            'nilai3' => 200,
            'pNilai1' => 'Bobot sangat kurang dari target:',
            'pNilai2' => 'Bobot masih kurang dari target standar:',
            'pNilai3' => 'Bobot sudah mencukupi (Maksimal):',
            'pNilai4' => 'Bobot melebihi batas 200%, mohon tinjau kembali:',
        ])


        <template x-if="items.length > 0">
            <div
                class="mt-2 px-4 py-3 bg-[var(--focus-color)]/10 border border-[var(--focus-color)]/20 rounded-lg flex justify-between items-center">
                <span class="text-xs font-bold uppercase"
                    x-text="
                            totalSubCPMK >= 14 ? 'Jumlah Sub-CPMK mencapai 14:' : 
                            'Jumlah Sub-CPMK masih kurang dari 14:'
                    "></span>
                <template x-if="totalSubCPMK < 14">
                    <flux:badge color="red" size="sm" variant="pill">
                        <span x-text="totalSubCPMK + ' Pertemuan'"></span>
                    </flux:badge>
                </template>
                <template x-if="totalSubCPMK >= 14">
                    <flux:badge color="green" size="sm" variant="pill">
                        <span x-text="totalSubCPMK + ' Pertemuan'"></span>
                    </flux:badge>
                </template>
            </div>
        </template>
    </div>

</div>
