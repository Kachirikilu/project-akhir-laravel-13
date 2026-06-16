<x-global.main-layout-card :paginator="$jadwals">

    {{-- 1. Isi Bagian Sortir Kiri (Arahkan slot ke nama yang sesuai di komponen Anda, misal: sortir) --}}
    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'kode',
            'headString' => 'Kode',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'label_kelas',
            'headString' => 'Label',
        ])
        @if ($kelas == null)
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'kode_mk',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'mk',
                'headString' => 'Mata Kuliah',
            ])
        @endif
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'hari_pelaksanaan',
            'headString' => 'Hari',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'tanggal_pelaksanaan',
            'headString' => 'Tanggal',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'kapasitas',
        ])
    </x-slot:sortir>

    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Jadwal Kelas...',
                'defaultLive' => 1,
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Kode Kelas', 'Pencarian Kompleks'],
                'isBorder' => 2,
            ])
        </div>
    </x-slot:search>

    {{-- 2. Isi Utama (Looping Card) --}}
    @forelse($jadwals as $j)
        <div wire:key="kelas-jadwal-{{ $j->id }}" data-kelas-id="{{ $j->id }}"
            class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg">

            {{-- ═══ HERO ═══ --}}
            <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">

                {{-- Baris atas: kode jadwal + tombol menu --}}
                <div class="flex items-start justify-between gap-2">

                    <flux:dropdown>
                        <button
                            class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 focus:outline-none cursor-pointer">
                            <flux:icon name="academic-cap" class="w-3 h-3" />
                            {{ $j->kode }}
                        </button>
                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table',
                            [
                                'x' => $j,
                                'editString' => 'editJadwal',
                                'nameXString' => 'Jadwal',
                                'confirmDeleteString' => 'deleteJadwal',
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
                            'livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table',
                            [
                                'x' => $j,
                                'editString' => 'editJadwal',
                                'nameXString' => 'Jadwal',
                                'confirmDeleteString' => 'deleteJadwal',
                            ]
                        )
                    </flux:dropdown>
                </div>

                {{-- Label Extra / ID Jadwal --}}
                <p class="text-[15px] font-bold leading-[1.35] tracking-[0.24em] text-[var(--main-text)]">
                    {{ $j->label_Extra ?? '-' }}
                </p>

                {{-- Sub info: hari + jam --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="calendar-days" class="w-3 h-3" />
                        {{ $j->hari ?? '-' }}
                    </span>
                    <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="clock" class="w-3 h-3" />
                        {{ $j->jam_pelaksanaan ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- ═══ BODY ═══ --}}
            <div class="flex flex-1 flex-col gap-2.5 p-4">

                {{-- Info Mata Kuliah (hanya jika $kelas == null) --}}
                @if ($kelas == null)
                    <div
                        class="flex w-full items-start gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                        <flux:icon name="book-open"
                            class="w-3.5 h-3.5 mt-0.5 text-[var(--contrast-third-text)] flex-shrink-0" />
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <span
                                class="text-xs font-semibold text-[var(--contrast-main-text)] leading-snug truncate">{{ $j->mk ?? '---' }}</span>
                            <span class="text-[10px] text-[var(--contrast-third-text)]">{{ $j->kode_mk ?? '-' }} ·
                                {{ $j->semester ?? '-' }} Sem · {{ $j->sks ?? '-' }} SKS</span>
                        </div>
                    </div>
                @endif

                {{-- Tanggal Pelaksanaan --}}
                <div
                    class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                    <flux:icon name="calendar" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                    <span
                        class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">Tanggal</span>
                    <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                        {{ $j->tanggal_pelaksanaan ?? '-' }}
                    </span>
                </div>

                {{-- Stat boxes: Kapasitas + Password/Status --}}
                <div class="grid grid-cols-2 gap-1.5">

                    {{-- Kapasitas --}}
                    <div
                        class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Kapasitas</span>
                        <span
                            class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $j->count_mhs_jadwal }}</span>
                        <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">Mahasiswa</span>
                    </div>

                    {{-- Password / Status --}}
                    <div
                        class="flex flex-col items-center justify-center gap-1 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        @if ($j->is_my_class)
                            <span
                                class="bg-[var(--focus-color)] rounded px-1.5 py-[2px] text-[9px] font-extrabold uppercase tracking-[0.08em] text-[var(--main-text)]">Terdaftar</span>
                            <span class="text-[10px] font-semibold text-[var(--contrast-main-text)]">Kelas Saya</span>
                        @elseif (Auth::user()->admin || Auth::user()->dosen)
                            <span
                                class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Password</span>
                            @if (!empty($j->password))
                                <code
                                    class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)]">
                                    {{ $j->password }}
                                </code>
                            @else
                                <span class="text-[10px] italic text-[var(--contrast-second-text)]">Tanpa PW</span>
                            @endif
                        @else
                            <span
                                class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Password</span>
                            <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                                {{ !empty($j->with_pw) ? 'Memiliki PW' : 'Tanpa PW' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══ FOOTER ═══ --}}
            <div class="px-4 pb-4">
                @if ($j->is_my_class || Auth::user()->admin || Auth::user()->dosen)
                    <button
                        class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                        href="{{ $isJadwalMhs ?? null ? route('sesi-mahasiswa', [$j->kode_kelas, $j->kode_jadwal]) : route('sesi-management', [$j->kode_kelas, $j->kode_jadwal]) }}"
                        wire:navigate>
                        <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                        <span>Lihat Jadwal Kelas</span>
                    </button>
                @elseif (!empty($j->with_pw))
                    <button
                        class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                        @click="
                    $store.jadwal?.setEdit(0);
                    $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                    $flux.modal('jadwal-join').show();
                    $store.jadwal?.setValueJoinJadwal(
                        '{{ $j->id ?? '' }}',
                        '{{ $j->kode ?? '' }}',
                        '{{ $j->kode_kelas ?? '' }}',
                        '{{ $j->label_extra ?? '' }}',
                    );
                ">
                        <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                        <span>Join Kelas</span>
                    </button>
                @else
                    <form x-on:submit.prevent="$wire.joinJadwal($store.jadwal)" id="jadwalForm">
                        <button
                            class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                            @click="
                        $store.jadwal?.setEdit(0);
                        $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                        $store.jadwal?.setValueJoinJadwal('{{ $j->id ?? '' }}');
                    ">
                            <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                            <span>Join Kelas</span>
                        </button>
                    </form>
                @endif
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Jadwal Kelas ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
