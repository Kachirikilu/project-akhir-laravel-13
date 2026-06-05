<x-global.main-layout-card :paginator="$nilai">

    {{-- 1. Isi bagian Sortir --}}
    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'kode',
            'headString' => 'Kode Kelas',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'kode_rps',
            'headString' => 'Kode RPS',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'nilai',
            'headString' => 'Nama Kelas',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'mk',
            'headString' => 'Mata Kuliah',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'semester',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'sks',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'sks_text',
            'headString' => 'Pembelajaran',
        ])
    </x-slot:sortir>

    {{-- 2. Isi Utama (Looping Card) masuk ke Default Slot --}}
    @forelse($nilai as $n)
        <div wire:key="nilai-{{ $n->id }}" data-nilai-id="{{ $n->id }}"
            class="relative flex flex-col justify-between p-4 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

            {{-- HEADER CARD (Kode Kelas, Kode RPS, & Tombol Aksi) --}}
            <div class="flex items-start justify-between gap-2 pb-3 border-b border-[var(--border-table-color)]/60">
                <div class="flex flex-wrap items-center gap-2">
                    {{-- 1. KODE KELAS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.level-mk-badge', [
                                'xValue' => $n->kode,
                                'sortir' => $n->rps_rel?->mk_rel?->level_mk,
                            ])
                        </button>
                        {{-- @include('livewire.all-role.nilai-management.nilai-toolbar-table', [
                            'x' => $n,
                            'editString' => 'editKelas',
                            'nameXString' => 'Kelas',
                            'confirmDeleteString' => 'deleteKelas',
                        ]) --}}
                    </flux:dropdown>

                    {{-- 2. KODE RPS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.semester-badge', [
                                'xValue' => $n->kode_rps,
                                'textString' => 'RPS:',
                                'sortir' => $n->semester,
                            ])
                        </button>
                        {{-- @include('livewire.all-role.nilai-management.nilai-toolbar-table', [
                            'x' => $n,
                            'editString' => 'editKelas',
                            'nameXString' => 'Kelas',
                            'confirmDeleteString' => 'deleteKelas',
                            'copyName' => 'Kode RPS',
                            'copyText' => $n->kode_rps ?? '',
                        ]) --}}
                    </flux:dropdown>
                </div>

                {{-- TOMBOL AKSI ELLIPSIS --}}
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom" />
                    {{-- @include('livewire.all-role.nilai-management.nilai-toolbar-table', [
                        'x' => $n,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                    ]) --}}
                </flux:dropdown>
            </div>

            {{-- BODY CARD (Nama Mata Kuliah & Nama Kelas) --}}
            <div class="flex-1 py-3.5 flex flex-col">
                <div class="space-y-1">
                    <h3 class="font-semibold text-sm text-[var(--contrast-main-text)] leading-snug tracking-tight">
                        {{ $n->mk ?? '-' }}
                    </h3>

                    <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                        {{ $n->nilai ?? '-' }}
                    </p>
                    <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                        {{ $n->kode_mk ?? '-' }} - {{ $n->sks_text }}
                    </p>
                </div>

                <div class="mt-auto pt-3 flex justify-end">
                    {{-- <x-button-action color="emerald" href="{{ route('jadwal-management', $n->kode) }}" wire:navigate>
                        <flux:icon name="rectangle-group" class="w-3.5 h-3.5" />
                        <span>Lihat Kelas</span>
                    </x-button-action> --}}
                </div>
            </div>
            {{-- FOOTER CARD (Semester, SKS, Program Studi) --}}
            <div
                class="grid grid-cols-3 gap-2 pt-3 border-t border-[var(--border-table-color)]/40 bg-[var(--second-table-trans)] -mx-4 -mb-4 p-3 rounded-b-xl text-center text-xs">
                <div class="border-r border-[var(--border-table-color)]/60 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Semester</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $n->semester ?? '-' }}</span>
                </div>

                <div class="border-r border-[var(--border-table-color)]/60 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Bobot</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $n->sks ?? '-' }} SKS</span>
                </div>

                <div class="truncate px-1 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">{{ $n->pr_rel->strata ?? '----' }}</span>
                    <span class="font-bold text-[var(--contrast-main-text)] truncate block">
                        {{ $n->kode_pr ?? '---' }}
                    </span>
                </div>
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Kelas ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
