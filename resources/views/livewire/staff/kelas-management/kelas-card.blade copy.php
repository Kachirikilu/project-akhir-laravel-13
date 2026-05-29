{{-- CONTAINER GRID CARD --}}
<div class="max-w-[2560px] grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 min-[1920px]:grid-cols-4 gap-4 items-start"
     wire:loading.class="opacity-50 pointer-events-none transition-opacity"
     wire:target="search, perPage, loadingTable, sortBy">

    @forelse($kelas as $k)
        <div wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
             class="relative flex flex-col justify-between p-4 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">
            
            {{-- HEADER CARD (Kode Kelas, Kode RPS, & Tombol Aksi) --}}
            <div class="flex items-start justify-between gap-2 pb-3 border-b border-[var(--border-table-color)]/60">
                <div class="flex flex-wrap items-center gap-2">
                    {{-- 1. KODE KELAS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @switch($k->rps_rel?->mk_rel?->level_mk)
                                @case(1) <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $k->kode ?? '-' }}</flux:badge> @break
                                @case(2) <flux:badge icon="book-open" color="amber" size="sm">{{ $k->kode ?? '-' }}</flux:badge> @break
                                @case(3) <flux:badge icon="building-library" color="indigo" size="sm">{{ $k->kode ?? '-' }}</flux:badge> @break
                                @default <flux:badge icon="globe-alt" color="red" size="sm">{{ $k->kode ?? '-' }}</flux:badge>
                            @endswitch
                        </button>
                        @include('livewire.staff.kelas-management.kelas-toolbar-table', ['x' => $k, 'editString' => 'editKelas', 'nameXString' => 'Kelas', 'confirmDeleteString' => 'deleteKelas'])
                    </flux:dropdown>

                    {{-- 2. KODE RPS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            <flux:badge color="zinc" variant="outline" size="sm" class="font-mono">
                                RPS: {{ $k->kode_rps ?? '---' }}
                            </flux:badge>
                        </button>
                        @include('livewire.staff.kelas-management.kelas-toolbar-table', ['x' => $k, 'editString' => 'editKelas', 'nameXString' => 'Kelas', 'confirmDeleteString' => 'deleteKelas', 'copyName' => 'Kode RPS', 'copyText' => $k->kode_rps ?? ''])
                    </flux:dropdown>
                </div>

                {{-- TOMBOL AKSI ELLIPSIS --}}
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                    @include('livewire.staff.kelas-management.kelas-toolbar-table', ['x' => $k, 'editString' => 'editKelas', 'nameXString' => 'Kelas', 'confirmDeleteString' => 'deleteKelas'])
                </flux:dropdown>
            </div>

            {{-- BODY CARD (Nama Mata Kuliah & Nama Kelas) --}}
            <div class="flex-1 py-3.5 space-y-1">
                {{-- 3. NAMA MATA KULIAH --}}
                <h3 class="font-semibold text-sm text-[var(--contrast-main-text)] leading-snug tracking-tight">
                    {{ $k->mk ?? '-' }}
                </h3>
                
                {{-- 4. NAMA KELAS --}}
                <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                    {{ $k->kelas ?? '-' }}
                </p>
            </div>

            {{-- FOOTER CARD (Semester, SKS, Program Studi) --}}
            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-[var(--border-table-color)]/40 bg-[var(--second-table-trans)] -mx-4 -mb-4 p-3 rounded-b-xl text-center text-xs">
                {{-- 5. SEMESTER --}}
                <div class="border-r border-[var(--border-table-color)]/60">
                    <span class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Semester</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">T-{{ $k->semester ?? '-' }}</span>
                </div>

                {{-- 6. SKS --}}
                <div class="border-r border-[var(--border-table-color)]/60">
                    <span class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Bobot</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $k->sks ?? '-' }} SKS</span>
                </div>

                {{-- 7. PROGRAM STUDI --}}
                <div class="truncate px-1">
                    <span class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Prodi</span>
                    <span class="font-bold text-[var(--contrast-main-text)] truncate block" title="{{ $k->prodi ?? '-' }}">
                        {{ $k->kode_pr ?? '---' }}
                    </span>
                </div>
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div class="col-span-1 md:col-span-2 xl:col-span-3 min-[1920px]:col-span-4 text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Kelas ditemukan!</p>
        </div>
    @endforelse

</div>

{{-- SINKRONISASI PAGINASI (PENGGANTI FOOTER TABLE) --}}
<div class="mt-4">
    @include('livewire.global.table.footer-table', ['typeXString' => $kelas])
</div>