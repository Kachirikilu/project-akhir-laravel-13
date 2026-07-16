<x-global.main-layout-card :paginator="$jadwals" :mx="''">

    {{-- 1. Isi Bagian Sortir Kiri (Arahkan slot ke nama yang sesuai di komponen Anda, misal: sortir) --}}
    <x-slot:sortir>
        <div class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">
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
        </div>
    </x-slot:sortir>

    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Jadwal Kelas...',
                'defaultLive' => 1,
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode Kelas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                'isBorder' => 2,
            ])
        </div>
    </x-slot:search>

    {{-- 2. Isi Utama (Looping Card) --}}
    @forelse($jadwals as $j)
        <div wire:key="kelas-jadwal-{{ $j->id }}" data-kelas-id="{{ $j->id }}"
            class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg">

            {{-- ═══ HERO ═══ --}}
            <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">

                {{-- Baris atas: kode jadwal + tombol menu --}}
                <div class="flex items-start justify-between gap-2">

                    <div class="flex items-center gap-2">
                        <flux:dropdown>
                            <button
                                class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                <flux:icon name="academic-cap" class="w-3 h-3" />
                                {{ $j->kode }}
                            </button>
                            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table', ['key' => 1])
                        </flux:dropdown>
                        @if (Auth::user()->admin || Auth::user()->dosen)
                            <span class="text-xs text-white/60 font-mono">ID:
                                {{ $j->id }}</span>
                        @endif
                    </div>

                    {{-- Tombol Menu --}}
                    <flux:dropdown>
                        <button
                            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                            <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                        </button>
                        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table', ['key' => 2])
                    </flux:dropdown>

                </div>

                {{-- Label Extra / ID Jadwal --}}
                <p class="mt-1 text-[15px] font-bold leading-[1.35] tracking-[0.24em] text-[var(--main-text)]">
                    {{ $j->label_Extra ?? '-' }}
                </p>

                {{-- Sub info: hari + jam --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="calendar-days" class="w-3 h-3" />
                        {{ $j->hari ?? '-' }}
                    </span>
                    <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/65">
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
                    class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-2 text-left transition-colors focus:outline-none cursor-pointer">
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
                        class="flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all
                            {{ $j->trashed()
                                ? 'cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800'
                                : 'cursor-pointer bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state active:scale-[0.99]' }}"
                        {{ $j->trashed() ? 'disabled' : 'href=' . ($isJadwalOnly ?? null ? route('sesi-jadwal-kelas', [$j->kode_kelas, $j->kode_jadwal]) : route('sesi-management', [$j->kode_kelas, $j->kode_jadwal])) . ' wire:navigate' }}>
                        <flux:icon name="calendar-days" class="w-3.5 h-3.5 {{ $j->trashed() ? 'opacity-40' : '' }}" />
                        <span>Lihat Jadwal Kelas</span>
                    </button>
                @elseif (!empty($j->with_pw))
                    <button
                        class="flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all
                            {{ $j->trashed()
                                ? 'cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800'
                                : 'cursor-pointer bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state active:scale-[0.99]' }}"
                        {{ $j->trashed() ? 'disabled' : '' }}
                        @if (!$j->trashed()) @click="
                            $store.jadwal?.setEdit(0);
                            $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                            $flux.modal('join-jadwal-modal').show();
                            $store.jadwal?.setValueJoinJadwal(
                                '{{ $j->id ?? '' }}',
                                '{{ $j->kode ?? '' }}',
                                '{{ $j->kode_kelas ?? '' }}',
                                '{{ $j->label_extra ?? '' }}',
                            );
                            $dispatch('open-join-jadwal-modal');
                        " @endif>
                        <flux:icon name="user-plus" class="w-3.5 h-3.5 {{ $j->trashed() ? 'opacity-40' : '' }}" />
                        <span>Join Kelas</span>
                    </button>
                @else
                    @if ($j->trashed())
                        <button
                            class="flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800"
                            disabled>
                            <flux:icon name="user-plus" class="w-3.5 h-3.5 opacity-40" />
                            <span>Join Kelas</span>
                        </button>
                    @else
                            <button
                                class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state transition-all active:scale-[0.99]"
                                @click="
                                    $store.jadwal?.setEdit(0);
                                    $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                    $store.jadwal?.setValueJoinJadwal('{{ $j->id ?? '' }}');
                                    $dispatch('join-jadwal-function', { data: $store.jadwal.getDataJoinJadwal() });
                                ">
                                <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                <span>Join Kelas</span>
                            </button>
                    @endif
                @endif
            </div>
        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Jadwal Kelas {{ $switchTable == 'hari-ini' ? 'hari ini' : '' }} ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
