<flux:modal name="delete-antrian-list" class="min-w-[22rem] space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg" class="text-red-600 flex items-center gap-2">
            <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
            Hapus Histori Antrian?
        </flux:heading>

        <flux:subheading class="text-sm text-gray-600 dark:text-gray-400">
            Tindakan ini akan menghapus secara permanen seluruh histori antrian dari sistem. Data yang sudah dihapus
            tidak dapat
            dikembalikan!
        </flux:subheading>
    </div>

    <div class="flex gap-2 justify-end items-center">
        <flux:modal.close>
            <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
        </flux:modal.close>

        @php
            $userTingkat = Auth::user()->tingkat ?? 4;
        @endphp

        {{-- Dropdown Pilihan Hapus --}}
        <flux:dropdown position="top" align="end">
            <flux:button variant="danger" icon-trailing="chevron-down" class="cursor-pointer duration-300">
                Pilih Aksi Hapus
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                {{-- Tingkat 1 --}}
                @if ($userTingkat == 1)
                    <flux:menu.item class="cursor-pointer duration-300" icon="trash" variant="danger"
                        wire:click="deleteAntrianList('all')" @click="Flux.modal('delete-antrian-list').close()">
                        Hapus Semua Data
                    </flux:menu.item>

                    <flux:menu.separator />
                @endif

                {{-- Tingkat 1 & 2 --}}
                @if ($userTingkat <= 2)
                    <flux:menu.item class="cursor-pointer duration-300" variant="danger"
                        wire:click="deleteAntrianList('fk')" @click="Flux.modal('delete-antrian-list').close()">
                        <flux:icon name="building-library"
                            class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />

                        Hapus {{ Auth::user()->fakultas_fk }}
                    </flux:menu.item>
                @endif

                {{-- Tingkat 1, 2, & 3 --}}
                @if ($userTingkat <= 3)
                    <flux:menu.item class="cursor-pointer duration-300" variant="danger"
                        wire:click="deleteAntrianList('dp')" @click="Flux.modal('delete-antrian-list').close()">
                        <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                        Hapus {{ Auth::user()->departemen_dp }}
                    </flux:menu.item>
                @endif

                <flux:menu.item class="cursor-pointer duration-300" variant="danger"
                    wire:click="deleteAntrianList('pr')" @click="Flux.modal('delete-antrian-list').close()">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />

                    Hapus {{ Auth::user()->prodi }}
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Semua Tingkat --}}
                <flux:menu.item class="cursor-pointer duration-300" icon="user" variant="danger"
                    wire:click="deleteAntrianList('own')" @click="Flux.modal('delete-antrian-list').close()">
                    Hapus Punya Sendiri
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</flux:modal>

<button type="button" x-show="(items?.length || 0) > 0" @click="Flux.modal('delete-antrian-list').show()"
    class="cursor-pointer text-[10px] uppercase font-bold px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition-all flex items-center gap-1 shadow-sm">
    <flux:icon name="trash" variant="micro" />
    Hapus Histori Antrian
</button>
