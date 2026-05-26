<!-- Container Utama: md:items-end membuat judul sejajar lurus dengan Search di bawah -->
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-5 w-full">
    
    <!-- SISI KIRI (Desktop): Judul Utama -->
    <div class="flex items-center shrink-0 pb-1.5 md:pb-0.5">
        <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2">
            <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
            Jadwal Kelas
        </h3>
    </div>

    <!-- SISI KANAN (Desktop): Tumpukan Kontrol Rata Kanan -->
    <div class="flex flex-col items-stretch md:items-end gap-3 w-full md:w-auto">
        
        <!-- Baris Atas Kontrol: Page Control + Tombol Tambah -->
        <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
            
            <!-- Page Control -->
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [2, 4, 6, 8, 12],
                    'key' => 'page-control-jadwal',
                    'withFull' => 0,
                ])
            </div>
            
            <!-- Tombol Tambah Jadwal -->
            <div class="shrink-0">
                <flux:dropdown>
                    <flux:button variant="primary" icon="plus" size="sm"
                        class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] transition-all duration-200 ease-in-out whitespace-nowrap"
                        wire:target="addJadwal">
                        Tambah Jadwal
                    </flux:button>

                    <flux:menu class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
                        <flux:menu.heading>Tambah Jadwal</flux:menu.heading>
                        <flux:menu.separator />

                        {{-- Program Studi --}}
                        <flux:menu.item
                            @click="
                                $store.jadwal?.setEdit(0);
                                $store.jadwal?.setColor('text-amber-700 dark:text-amber-400');
                                $flux.modal('jadwal-modal').show();
                                $wire.addJadwal();
                            "
                            class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                            <flux:icon name="calendar-days" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                            <div class="flex justify-between items-center w-full">
                                <span class="mr-7">Jadwal Perkuliahan</span>
                                <flux:icon wire:loading wire:target="addJadwal()" name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
                            </div>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

        </div>

        <!-- Baris Bawah Kontrol: Kolom Pencarian -->
        <div class="w-full md:w-72 lg:w-96">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Jadwal Kelas...',
                'isLive' => 1,
                'isBorder' => 2,
            ])
        </div>
        
    </div>
</div>