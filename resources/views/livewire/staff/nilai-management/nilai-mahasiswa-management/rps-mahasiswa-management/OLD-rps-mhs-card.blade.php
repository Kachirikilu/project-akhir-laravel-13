<x-global.main-layout-card :paginator="$nilais">

    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'no_mk',
            'alpine' => 'nilai',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'kode_rps',
            'alpine' => 'nilai',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'mk',
            'headString' => 'Mata Kuliah',
            'alpine' => 'nilai',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'sks',
            'alpine' => 'nilai',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'nilai',
            'alpine' => 'nilai',
        ])
    </x-slot:sortir>

    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nilai Semester...',
                'alpine' => 'nilai',
                'isLive' => 1,
                'isBorder' => 2,
            ])
        </div>
    </x-slot:search>

    {{-- 2. Looping Data Nilai Mata Kuliah (Per RPS) --}}
    @forelse($nilais as $n)
        <div wire:key="rps-mahasiswa-{{ $n->id }}" data-rps-mahasiswa-id="{{ $n->id }}"
            class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg">

            {{-- ═══ HERO ═══ --}}
            <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">

                {{-- Baris atas: kode kelas + tombol menu --}}
                <div class="flex items-start justify-between gap-2">

                    {{-- Kode Kelas --}}
                    <flux:dropdown>
                        <button
                            class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 focus:outline-none cursor-pointer">
                            <flux:icon name="academic-cap" class="w-3 h-3" />
                            {{ $n->kode_mk }}
                        </button>
                        @include(
                            'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                            [
                                'x' => $n,
                                // 'editString' => 'editKelas',
                                'nameXString' => 'Nilai',
                                // 'confirmDeleteString' => 'deleteKelas',
                                'copyName' => 'Kode RPS',
                                'copyText' => $n->kode_rps ?? '',
                            ]
                        )
                    </flux:dropdown>

                    {{-- Tombol Menu --}}
                    <flux:dropdown>
                        <button
                            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/22 focus:outline-none cursor-pointer">
                            <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                        </button>
                        @include(
                            'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                            [
                                'x' => $n,
                                'nameXString' => 'Nilai',
                                'copyName' => 'Kode RPS',
                                'copyText' => $n->kode_rps ?? '',
                            ]
                        )
                    </flux:dropdown>
                </div>

                {{-- Nama Mata Kuliah --}}
                <p class="text-[15px] font-bold leading-[1.35] tracking-[0.1em] text-[var(--main-text)]">
                    {{ $n->mk ?? '-' }}
                </p>

                {{-- Sub info: nama kelas + prodi --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="users" class="w-3 h-3" />
                        {{ $nim ?? '-' }}
                    </span>
                    <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="academic-cap" class="w-3 h-3" />
                        {{ $n->sks ?? '-' }} SKS
                    </span>
                </div>
            </div>

            {{-- ═══ BODY ═══ --}}
            <div class="flex flex-1 flex-col gap-2.5 p-4">

                {{-- Baris RPS --}}
                <flux:dropdown>
                    <div
                        class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                        <flux:icon name="document-text" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                        <span
                            class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">RPS</span>
                        <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                            <button class="cursor-pointer focus:outline-none">
                                @include('livewire.global.table.badge.level-mk-badge', [
                                    'xValue' => $n->kode_rps,
                                    'sortir' => $n->rps_rel?->mk_rel?->level_mk,
                                ])
                            </button>
                        </span>
                    </div>
                    @include(
                        'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                        [
                            'x' => $n,
                            'nameXString' => 'Nilai',
                            'copyName' => 'Kode RPS',
                            'copyText' => $n->kode_rps ?? '',
                        ]
                    )
                </flux:dropdown>

                {{-- Stat boxes --}}
                <div class="grid grid-cols-3 gap-1.5">

                    <div
                        class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Nilai</span>
                        <span
                            class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $n->nilai ?? '-' }}</span>
                    </div>

                    <div
                        class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Index</span>
                        <span
                            class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $n->nilai_index ?? '-' }}</span>
                    </div>


                    @include(
                        'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.nilai-mutu',
                        ['value' => $n->nilai_mutu]
                    )
                </div>
            </div>

            {{-- ═══ FOOTER ═══ --}}
            <div class="px-4 pb-4 flex items-center gap-1.5">
                <button
                    class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-bl-[11px] rounded-r-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:z-10 hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                    @click="
                        $store.nilai?.reset();
                        $store.nilai?.setEdit(1);
                        $store.nilai?.setColor('text-cyan-700 dark:text-cyan-400');
                        $store.nilai?.setValueNilai(
                            '{{ $n->id ?? '' }}',
                            '{{ $mahasiswa->name ?? '' }}',
                            '{{ $mahasiswa->nim ?? '' }}',
                            '{{ $n->kode_rps ?? '' }}',
                            '{{ $n->mk ?? '' }}',
                            '{{ $n->sks ?? '' }}',
                            JSON.parse('{{ json_encode($n->nilai_array ?? []) }}'),
                            JSON.parse('{{ json_encode($n->bobot_rps_array ?? []) }}'),
                            JSON.parse('{{ json_encode($n->kode_cpmk_array ?? []) }}'),
                            JSON.parse('{{ json_encode($n->kode_scpmk_array ?? []) }}'),
                            JSON.parse('{{ json_encode($n->metode_array ?? []) }}'),
                        );
                        $flux.modal('nilai-modal').show();
                    ">

                    @if (Auth::user()->admin || Auth::user()->dosen)
                        <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                        <span>Edit Nilai</span>
                    @else
                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                        <span>Lihat Nilai</span>
                    @endif

                </button>

                <button
                    class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-br-[11px] rounded-l-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:z-10 hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                    @click="
                        $store.nilai?.resetShow();
                        $store.nilai?.setShowRPS(
                            '{{ $n->rps_rel->id ?? '' }}',
                        );
                        $flux.modal('rps-detail-modal').show();
                    "
                    wire:click="showRPS({{ $n->rps_rel->id }})">
                    <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5" />
                    <span>Lihat RPS</span>
                </button>
            </div>

        </div>
    @empty
        {{-- Tampilan saat rincian nilai di semester tersebut masih kosong --}}
        <div
            class="col-span-full text-center p-12 rounded-xl border border-dashed border table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">
                Tidak ada rincian nilai Mata Kuliah yang ditemukan untuk Periode ini!
            </p>
        </div>
    @endforelse

</x-global.main-layout-card>
