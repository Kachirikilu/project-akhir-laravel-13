<x-global.main-layout-card :paginator="$nilais">

    {{-- 1. Bagian Judul Sesi & Pencarian --}}
    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'created_at',
        ])
    </x-slot:sortir>

    {{-- 2. Looping Data Nilai Mata Kuliah (Per RPS) --}}
    @forelse($nilais as $n)
        <div wire:key="nilai-rps-{{ $n->id }}"
            class="relative flex flex-col justify-between p-4 rounded-xl border table-border bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

            {{-- HEADER CARD: Kode RPS & Nilai Mutu --}}
            <div class="flex items-start justify-between gap-2 pb-3 border-b table-border/60">
                <div class="flex flex-col gap-0.5">
                    {{-- Kode RPS Tampil Di Sini --}}
                    <span class="text-xs font-mono font-bold text-[var(--focus-color)] uppercase tracking-wider">
                        <flux:dropdown>
                            <button class="cursor-pointer">
                                @include('livewire.global.table.badge.level-mk-badge', [
                                    'xValue' => $n->kode_rps,
                                    'sortir' => $n->level_mk,
                                ])
                            </button>

                            {{-- @include('livewire.staff.obe-management.obe-toolbar-table', [
                                'x' => $r,
                                'typeXString' => $switchTable,
                                'nameXString' => 'RPS',
                            ]) --}}
                        </flux:dropdown>
                    </span>
                    <h3
                        class="font-bold text-sm text-[var(--contrast-main-text)] leading-snug line-clamp-2 min-h-[40px]">
                        {{ $n->mk ?? 'Nama Mata Kuliah Belum Diatur' }}
                    </h3>
                </div>

                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.nilai-mutu-badge', [
                            'xValue' => $n->nilai_mutu ?? 'E',
                        ])
                    </button>
                    {{-- @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                    ]) --}}
                </flux:dropdown>
            </div>

            {{-- BODY CARD: Skor Angka Utama --}}
            <div class="flex-1 py-4 flex flex-col justify-center">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[var(--contrast-main-text)] tracking-tight">
                        {{ number_format($n->nilai ?? 0, 1) }}
                    </span>
                    <span class="text-xs font-medium text-[var(--contrast-second-text)]">
                        Nilai Angka
                    </span>
                </div>
            </div>

            {{-- FOOTER CARD: Beban SKS Mata Kuliah --}}
            <div
                class="pt-4 border-t table-border/40 -mx-5 -mb-5 p-5 bg-[var(--second-table-trans)] rounded-b-xl flex items-center justify-between">
                <p class="text-xs font-medium text-[var(--contrast-third-text)] flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                    Beban Kuliah: <strong
                        class="text-[var(--contrast-main-text)]">{{ $n->sks ?? 0 }} SKS</strong>
                </p>

                {{-- Tanda cek kecil untuk indikator validasi data --}}
                <span class="text-xs font-semibold text-[var(--contrast-third-text)] flex items-center gap-1">
                    <flux:icon name="check-circle" class="w-3.5 h-3.5 text-emerald-500" /> Terdata

                </span>
            </div>

        </div>
    @empty
        {{-- Tampilan saat rincian nilai di semester tersebut masih kosong --}}
        <div
            class="col-span-full text-center p-12 rounded-xl border border-dashed border table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">
                Tidak ada rincian nilai mata kuliah yang ditemukan untuk periode ini.
            </p>
        </div>
    @endforelse

</x-global.main-layout-card>
