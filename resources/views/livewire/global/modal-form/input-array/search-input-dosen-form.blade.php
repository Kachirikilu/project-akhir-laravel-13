<div class="relative" wire:key="search-array-{{ $typeXString }}-{{ $selectX }}-{{ $alpine }}"
    x-data="{
        open: false,
        search: @entangle($nameSearchString).live,
        items: @entangle($idString),
        itemsAll: @entangle($itemsAllString),
        pertemuan: @entangle($itemsPertemuanString),
        parentSelectedId: @entangle($parentIdString ?? null).live,
    
        init() {
            if (!Array.isArray(this.items)) this.items = [];
            if (!Array.isArray(this.itemsAll)) this.itemsAll = [];
            if (!Array.isArray(this.pertemuan)) {
                this.pertemuan = [];
            }
    
            this.$watch('itemsAll', (val) => {
                let found = false;
                val.forEach(i => {
                    if (i.is_ketua) {
                        if (!found) {
                            found = true;
                        } else {
                            i.is_ketua = false;
                        }
                    }
                });
            });
        },
    
    
        get isParentReady() {
            return this.parentSelectedId != null && this.parentSelectedId != '';
        },
    
        addItem(id, kode, slot1, slot2, slot3, slot4, slot5) {
            let normalizedId = Number(id);
    
            if (!this.items.map(i => Number(i)).includes(normalizedId)) {
                let isFirst = this.items.length === 0;
                this.items.push(normalizedId);
                this.itemsAll.push({
                    id: normalizedId,
                    kode: kode,
                    slot1: slot1,
                    slot2: slot2,
                    slot3: slot3,
                    slot4: slot4,
                    slot5: slot5,
                    peran: isFirst ? 'Koordinator' : 'Pengajar',
                    is_ketua: isFirst
                });
    
                if (isFirst) {
                    this.$nextTick(() => {
                        this.setKetuaById(normalizedId);
                    });
                }
    
                this.pertemuan.push('');
            }
        },
    
        setKetuaById(id) {
            let normalizedId = Number(id);
    
            if (this.items.length !== this.itemsAll.length) return;
    
            let currentItemsAll = JSON.parse(JSON.stringify(this.itemsAll));
            let currentItems = [...this.items];
            let currentPertemuan = [...this.pertemuan];
    
            // Reset semua
            currentItemsAll.forEach(i => {
                i.is_ketua = false;
                if (i.peran === 'Koordinator') {
                    i.peran = 'Pengajar';
                }
            });
    
            let idx = currentItemsAll.findIndex(i => Number(i.id) === normalizedId);
            if (idx === -1) return;
    
            currentItemsAll[idx].is_ketua = true;
            currentItemsAll[idx].peran = 'Koordinator';
    
            let selectedItemAll = currentItemsAll.splice(idx, 1)[0];
            let selectedId = currentItems.splice(idx, 1)[0];
            let selectedPertemuan = currentPertemuan.splice(idx, 1)[0];
    
            currentItemsAll.unshift(selectedItemAll);
            currentItems.unshift(selectedId);
            currentPertemuan.unshift(selectedPertemuan);
    
            this.itemsAll = currentItemsAll;
            this.items = currentItems;
            this.pertemuan = currentPertemuan;
        },
    
        removeItem(index) {
            // 1. Ambil ID sebelum array dimodifikasi
            let idToRemove = this.items[index];
            let isKetua = this.itemsAll[index]?.is_ketua === true;
    
            // 2. Baru hapus dari array items dan itemsAll
            this.items.splice(index, 1);
            this.itemsAll.splice(index, 1);
    
            // 3. Hapus data pertemuan dari objek (agar sinkron ke Livewire)
            this.pertemuan.splice(index, 1);
    
            // 4. Logika penentuan ketua baru jika yang dihapus adalah ketua
            if (isKetua && this.itemsAll.length > 0) {
                let newIndex = index;
                if (newIndex >= this.itemsAll.length) {
                    newIndex = this.itemsAll.length - 1;
                }
    
                let newKetuaId = this.itemsAll[newIndex].id;
    
                this.$nextTick(() => {
                    this.setKetuaById(newKetuaId);
                });
            }
        },
    
        get hasKetua() {
            return this.itemsAll.some(i => i.is_ketua);
        },
    
        move(index, direction) {
            let to = index + direction;

            if (to < 0 || to >= this.items.length) return;

            const swap = (arr, a, b) => {
                [arr[a], arr[b]] = [arr[b], arr[a]];
            };

            swap(this.items, index, to);
            swap(this.itemsAll, index, to);
            swap(this.pertemuan, index, to);
        },
    
        resetItems() {
            Flux.modal('reset-confirm-modal-{{ $idString }}').show();
        },
    
        clearAllItems() {
            this.items = [];
            this.itemsAll = [];
            this.pertemuan = [];
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

        {{-- KONTEN LIST (Akan transparan saat loading) --}}
        <div
            @if ($wireLoadingParent ?? null) wire:target="{{ $wireLoadingParent }}, {{ $wireLoading }}" wire:loading.class="opacity-60 pointer-events-none" @endif">
            @forelse ($xResults as $x)
                @php
                    $itemId = data_get($x, 'id');
                @endphp
                @if ($itemId !== null)
                    @include('livewire.global.modal-form.input-array.partial.search-dosen-dropdown')
                @endif
            @empty
                <div class="p-4 text-center">
                    <div wire:loading @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                        <p class="text-xs sm:text-sm text-[var(--focus-color)] font-medium animate-pulse">
                            Sedang mencari data {{ $nameXString ?? null }}...
                        </p>
                    </div>

                    <div wire:loading.remove @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
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
    @error($itemsAllString)
        <span class="text-red-500 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
    @enderror

    {{-- 3. AREA OPSI TERPILIH (DI DALAM KOTAK) --}}
    <div class="mt-4 p-4 border-2 border-dashed table-border rounded-xl bg-gray-50/30 dark:bg-neutral-800/30">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-gray-400">Daftar Terpilih:</span>
            <div class="flex items-center gap-2">
                @include('livewire.global.modal-form.partial.reset-all-buttons')
                <span x-show="items.length > 0"
                    class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full"
                    x-text="items.length + ' Terpilih'"></span>
            </div>
        </div>

        <div class="space-y-3">
            <template x-for="(id, index) in items" :key="id + '-' + (itemsAll[index]?.is_ketua ? 'ketua' : 'anggota')">
                <div :class="itemsAll[index]?.is_ketua ? 'border-[var(--focus-color)] ring-1 ring-[var(--focus-color)]' :
                    'table-border'"
                    class="relative bg-[var(--second-table-color)] border px-4 py-3 rounded-lg shadow-sm gap-4 transition-all">

                    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <button type="button" @click="setKetuaById(id)"
                                :class="itemsAll[index]?.is_ketua ? 'text-amber-500' : 'text-gray-300 hover:text-amber-400 active:text-amber-500'"
                                class="cursor-pointer mt-1 transition-colors" title="Jadikan Ketua">
                                <flux:icon icon="star" variant="solid" class="size-5" />
                            </button>

                            <div class="flex flex-col flex-1">
                                {{-- Label Ketua --}}
                                <div x-show="itemsAll[index]?.is_ketua == true" class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-bold mb-1.5 px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white">Ketua</span>
                                    <div class="h-px flex-1 mb-1.5 bg-gray-200 dark:bg-neutral-800 opacity-40"></div>
                                </div>

                                {{-- Nama Utama --}}
                                <span class="text-xs sm:text-sm font-bold text-[var(--contrast-main-text)]"
                                    x-text="itemsAll[index]?.slot1"></span>

                                {{-- Container Info (NIP, NIDN, NIDK) Sejajar --}}
                                <div class="mt-1 flex items-center flex-wrap text-xs text-gray-500 gap-y-1">
                                    {{-- NIP --}}
                                    -<span class="ml-1 font-bold text-[var(--hover-focus-color)]"
                                        x-text="'NIP: ' + itemsAll[index]?.kode"></span>

                                    {{-- NIDN --}}
                                    <template x-if="itemsAll[index]?.slot2">
                                        <div class="flex items-center">
                                            <span class="mx-1.5 opacity-50">|</span>
                                            <span x-text="'NIDN: ' + itemsAll[index]?.slot2"></span>
                                        </div>
                                    </template>

                                    {{-- NIDK --}}
                                    <template x-if="itemsAll[index]?.slot3">
                                        <div class="flex items-center">
                                            <span class="mx-1.5 opacity-50">|</span>
                                            <span x-text="'NIDK: ' + itemsAll[index]?.slot3"></span>
                                        </div>
                                    </template>

                                    {{-- Slot 4 --}}
                                    <template x-if="itemsAll[index]?.slot4">
                                        <div class="flex items-center">
                                            <span class="mx-1.5 opacity-50">|</span>
                                            <span x-text="'Status: ' + itemsAll[index]?.slot4"></span>
                                        </div>
                                    </template>

                                    {{-- Slot 5 --}}
                                    <template x-if="itemsAll[index]?.slot5">
                                        <div class="flex items-center">
                                            <span class="mx-1.5 opacity-50">|</span>
                                            <span x-text="itemsAll[index]?.slot5"></span>
                                        </div>
                                    </template>

                                    <div class="flex items-center">
                                        <span class="mx-1.5 opacity-50">|</span>
                                        <span x-text="'ID: ' + itemsAll[index]?.id"></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- PEMILIH PERAN --}}
                        <div class="flex items-center gap-2">
                            <select x-model="itemsAll[index].peran"
                                class="cursor-pointer text-xs border rounded-md bg-[var(--main-pop-up-color)] table-border focus:ring-[var(--focus-color)] p-1.5">
                                <option value="Koordinator">Koordinator</option>
                                <option value="Pengajar">Pengajar</option>
                                <option value="Asisten">Asisten</option>
                            </select>

                            {{-- ACTION BUTTONS --}}
                            <div class="flex items-center gap-1 ml-2">
                                <div class="flex flex-col gap-0.5">
                                    <button @click="move(index, -1)" type="button"
                                        class="cursor-pointer p-0.5 hover:bg-gray-200 dark:hover:bg-gray-700 active:bg-gray-300 dark:active:bg-gray-600 rounded disabled:opacity-10"
                                        :disabled="(hasKetua ? index === 1 : index === 0) || index === 0">
                                        <flux:icon icon="chevron-up" variant="mini" class="size-4" />
                                    </button>
                                    <button @click="move(index, 1)" type="button"
                                        class="cursor-pointer p-0.5 hover:bg-gray-200 dark:hover:bg-gray-700 active:bg-gray-300 dark:active:bg-gray-600 rounded disabled:opacity-10"
                                        :disabled="index === items.length - 1 || index == 0">
                                        <flux:icon icon="chevron-down" variant="mini" class="size-4" />
                                    </button>
                                </div>

                                <button @click="removeItem(index)" type="button"
                                    class="cursor-pointer p-1.5 text-red-500 hover:bg-red-100 active:bg-red-100/90 dark:hover:bg-red-900/30 dark:active:bg-red-900/40 rounded-md transition-colors ml-1">
                                    <flux:icon icon="trash" variant="mini" class="size-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-3 pt-3 border-t border-gray-100 dark:border-neutral-800 flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex-1">
                            <label class="text-[10px] font-bold uppercase text-gray-400 mb-1 block">Tugas Pertemuan
                                (Contoh: 1-4, 7, 9)</label>
                            <div class="relative flex items-center">
                                <flux:icon icon="calendar-days" variant="mini"
                                    class="absolute left-2.5 size-4 text-gray-400" />
                                <input type="text" x-model="pertemuan[index]"
                                    placeholder="Kosongkan jika mengajar di semua pertemuan..."
                                    class="w-full pl-9 pr-3 py-1.5 text-xs border rounded-lg bg-white dark:bg-neutral-900 table-border focus:ring-1 focus:ring-[var(--focus-color)] focus:border-[var(--focus-color)] transition-all">
                            </div>

                            {{-- Real-time Feedback Helper (Opsional) --}}
                            <p x-show="pertemuan[id]" class="text-[10px] mt-1 text-[var(--focus-color)] italic">
                                *Dosen akan ditugaskan ke Sub-CPMK sesuai angka di atas.
                            </p>
                        </div>
                    </div>

                </div>


            </template>
        </div>
        {{-- Empty State --}}
        <div x-show="items.length === 0" class="pt-6 pb-12 flex flex-col items-center justify-center opacity-40">
            <flux:icon icon="list-bullet" variant="outline" class="mb-1" />
            <p class="text-xs italic">Belum ada {{ $nameXString }} yang dipilih!</p>
        </div>
    </div>

</div>
