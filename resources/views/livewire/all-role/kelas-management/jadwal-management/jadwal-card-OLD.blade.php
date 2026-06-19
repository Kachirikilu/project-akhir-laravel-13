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

    {{-- 2. Isi Utama (Looping Card) --}}
    @forelse($jadwals as $j)
        <div wire:key="kelas-jadwal-{{ $j->id }}" data-kelas-id="{{ $j->id }}"
            class="relative flex flex-col justify-between p-4 rounded-xl border table-border bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

            {{-- HEADER CARD (Kode Jadwal Wilayah & Tombol Aksi) --}}
            <div class="flex items-start justify-between gap-2 pb-3 border-b table-border/60">
                <div class="flex flex-wrap items-center gap-2">
                    {{-- BADGE KODE BERDASARKAN WILAYAH --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.kode-wilayah-badge', [
                                'xValue' => $j->kode,
                                'sortir' => $j->kode_wilayah,
                            ])
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

                    {{-- BADGE ID JADWAL --}}
                    <x-label-card type="sm">
                        {{ $j->label_Extra ?? '-' }}
                    </x-label-card>
                </div>

                {{-- TOMBOL AKSI ELLIPSIS (KANAN ATAS) --}}
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom" />

                    @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table', [
                        'x' => $j,
                        'editString' => 'editJadwal',
                        'nameXString' => 'Jadwal',
                        'confirmDeleteString' => 'deleteJadwal',
                    ])
                </flux:dropdown>
            </div>

            {{-- BODY CARD (Detail Label, Password, dan Waktu) --}}
            <div class="flex-1 py-2 flex flex-col justify-between gap-3">
                {{-- INFORMASI UTAMA JADWAL --}}
                <div
                    class="text-xs bg-[var(--second-table-color)]/30 p-2 rounded-lg border table-border/40">
                    <div class="space-y-1">
                        <p class="font-semibold text-sm text-[var(--contrast-main-text)] leading-snug tracking-tight">
                            Tanggal Kelas
                        </p>
                        <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $j->tanggal_pelaksanaan ?? '-' }}
                        </p>
                    </div>
                    {{-- TOMBOL NAVIGASI JADWAL (KANAN BAWAH BODY) --}}
                    <div class="mt-2 pt-1 flex items-center justify-between gap-2">
                        <div class="text-xs">

                            @if ($j->is_my_class)
                                <code
                                    class="italic font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)]">
                                    Saya Terdaftar
                                </code>
                            @else
                                <span class="text-[10px] text-[var(--contrast-second-text)] block">Password:</span>
                                @if (Auth::user()->admin || Auth::user()->dosen)
                                    @if (!empty($j->password))
                                        <div class="mt-1">
                                            <code
                                                class="font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)]">
                                                {{ $j->password }}
                                            </code>
                                        </div>
                                    @else
                                        <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                                            Tanpa Password
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                                        @if (!empty($j->with_pw))
                                            Memiliki Password
                                        @else
                                            Tanpa Password
                                        @endif
                                    </span>
                                @endif
                            @endif


                        </div>

                        @if ($j->is_my_class || Auth::user()->admin || Auth::user()->dosen)
                            <x-button-action color="amber"
                                href="{{ $isJadwalMhs ?? null ? route('sesi-mahasiswa', [$j->kode_kelas, $j->kode_jadwal]) : route('sesi-management', [$j->kode_kelas, $j->kode_jadwal]) }}"
                                wire:navigate>
                                <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                                <span>Lihat Kelas</span>
                            </x-button-action>
                        @else
                            @php
                                $buttonClass =
                                    'inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/35 transition-all duration-200 text-sm font-medium shadow-sm cursor-pointer';
                            @endphp
                            @if (!empty($j->with_pw))
                                <x-button-action color="blue"
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
                                    <span>Join</span>
                                </x-button-action>
                            @else
                                <form x-on:submit.prevent="$wire.joinJadwal($store.jadwal)" id="jadwalForm">
                                    <x-button-action color="blue"
                                        @click="
                                        $store.jadwal?.setEdit(0);
                                        $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                        $store.jadwal?.setValueJoinJadwal(
                                            '{{ $j->id ?? '' }}',
                                        );
                                    ">
                                        <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                        <span>Join</span>
                                    </x-button-action>
                                </form>
                            @endif
                        @endif

                    </div>
                </div>


            </div>

            {{-- FOOTER CARD (Kapasitas & Sinkronisasi Waktu Grid 3 Kolom) --}}
            <div
                class="grid grid-cols-5 gap-2 border-t table-border/40 bg-[var(--second-table-trans)] -mx-4 -mb-4 p-3 rounded-b-xl text-center text-xs">
                <div class="col-span-2 border-r pl-2 table-border/60 space-y-0.5">
                    <span
                        class="text-left block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">
                        Kapasitas</span>
                    <span class="text-left font-bold text-[var(--focus-color)] block truncate">
                        {{ $j->mahasiswas_count }} / {{ $j->kapasitas }}
                    </span>
                </div>

                <div class="col-span-3 truncate pl-0.5 pr-2 space-y-0.5">
                    <span
                        class="text-right block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Waktu
                        Pelaksanaan</span>
                    <span
                        class="text-right font-medium text-[var(--contrast-main-text)] block truncate">{{ $j->hari ?? '-' }},
                        {{ $j->jam_pelaksanaan ?? '-' }}</span>
                </div>
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Jadwal Kelas ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
