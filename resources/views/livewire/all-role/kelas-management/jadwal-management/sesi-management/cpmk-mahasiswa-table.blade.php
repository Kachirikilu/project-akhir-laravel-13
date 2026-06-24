<x-global.main-layout-table :paginator="$users">
    <x-slot:sortir>
        <div
            class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'NIM',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
            ])
            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'mhs_nilai_akhir',
                    'headString' => 'Nilai',
                ])
            @endif
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'angkatan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'status',
            ])
        </div>
    </x-slot:sortir>
    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            <div class="col-start-1 row-start-1 w-full">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mahasiswa Kelas...',
                    'defaultLive' => 1,
                    'searchMode' => $searchMode,
                    'searchValues' => ['simple', 'full'],
                    'searchOptions' => ['Cari Identitas Mahasiswa', 'Pencarian Kompleks'],
                    'isBorder' => 2,
                ])
            </div>
        </div>
    </x-slot:search>

    <x-slot:header>
        <tr>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mahasiswa_id',
                'headString' => 'MHS ID',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
            ])

            <th rowspan="2" class="table-head border-x">Role</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'NIM',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
                'isSticky' => 1,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])

            @if (Auth::user()->admin || Auth::user()->dosen)
                <th colspan="3" class="table-head-sub">
                    Nilai Mahasiswa
                </th>

                @foreach ($groupsCpmk as $kodeCpmk => $pertemuans)
                    @php
                        $collection = collect($pertemuans);

                        $minP = $collection->min('no_pertemuan');
                        $maxP = $collection->max('no_pertemuan');
                        $rangePertemuan = "(P{$minP}–P{$maxP})";

                        $containsUts = $collection->contains('is_evaluasi', 'UTS');
                        $containsUas = $collection->contains('is_evaluasi', 'UAS');

                        $colorClass = '';
                        $hasEvaluasi = $containsUts || $containsUas;

                        if ($hasEvaluasi) {
                            $colorClass = 'text-amber-700 dark:text-amber-500 font-bold';
                        }
                    @endphp
                    <th colspan="3" x-data="{ showLine: {{ $hasEvaluasi ? 'true' : 'false' }} }"
                        class="{{ $colorClass }} table-head-sub text-center border-x relative">

                        <div class="text-xs sm:text-sm">{{ $kodeCpmk }}</div>
                        <div class="text-[10px] font-normal opacity-90">{{ $rangePertemuan }}</div>

                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[90%] h-[3px] mb-1 bg-amber-700 dark:bg-amber-500 origin-center"
                            x-show="showLine" x-init="$nextTick(() => { if ({{ $hasEvaluasi ? 'true' : 'false' }}) showLine = true; })">
                        </div>
                    </th>
                @endforeach
            @endif

            @include('livewire.global.search-and-filters.table-search', [
                'sortFieldString' => 'angkatan',
                'modelString' => 'searchAngkatan',
                'resetXFilter' => 'resetInputAngkatan()',
                'wInput' => 20,
                'numberOnly' => 1,
                'maxLength' => 4,
                'placeholder' => 'Tahun',
                'rowSpan' => 2,
                'isBorderR' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kampus',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])
            <th rowspan="2" class="table-head border-x">Aksi</th>

        </tr>

        <tr>
            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_akhir',
                    'headString' => 'Angka',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_index',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_mutu',
                    'headString' => 'Mutu',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
                @foreach ($groupsCpmk as $kodeCpmk => $pertemuans)
                    <th class="table-head whitespace-nowrap">
                        Skor Murni
                    </th>
                    <th class="table-head whitespace-nowrap text-blue-700 dark:text-blue-500"
                        title="Nilai Kontribusi Riil terhadap Total Nilai Kelompok">
                        Kontribusi
                    </th>
                    <th class="table-head whitespace-nowrap text-green-700 dark:text-green-500 border-r"
                        title="Persentase Bobot CPMK">
                        Bobot
                    </th>
                @endforeach
            @endif
        </tr>


    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-main text-center">{{ $user->role_id }}</td>

            {{-- Role --}}
            <td class="table-second table-border-r text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="book-open" color="cyan" size="sm">Mahasiswa</flux:badge>
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

            <td class="table-main-sticky table-border-r text-center">{{ $user->identity1 }}</td>


            @php
                $isMahasiswa = false;
                if (Auth::user()->admin || Auth::user()->dosen) {
                    $isMahasiswa = true;
                } elseif (Auth::user()->mahasiswa && Auth::user()->id == $user->id) {
                    $isMahasiswa = true;
                }
            @endphp

            <td class="table-second table-border-r whitespace-nowrap">{{ $user->name ?? '-' }}</td>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="table-second text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_nilai_akhir ?? 0 }}
                    @else
                        -
                    @endif
                </td>
                <td class="table-second text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_nilai_index ?? 0 }}
                    @else
                        -
                    @endif
                </td>
                <td class="table-sub table-border-x text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        <flux:dropdown>
                            <button class="cursor-pointer">
                                @include('livewire.global.table.badge.nilai-mutu-badge', [
                                    'xValue' => $user->mhs_nilai_mutu ?? 'E',
                                ])
                            </button>
                            @include(
                                'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-toolbar-table',
                                ['x' => $user]
                            )
                        </flux:dropdown>
                    @else
                        -
                    @endif
                </td>

                @php
                    $arrayNilai = is_array($user->mhs_nilai_array)
                        ? $user->mhs_nilai_array
                        : json_decode($user->mhs_nilai_array ?? '[]', true);

                    $bobotCpmkArray = is_array($user->mhs_bobot_array)
                        ? $user->mhs_bobot_array
                        : json_decode($user->mhs_bobot_array ?? '[]', true);

                    $globalTotalBobotMentah = collect($groupsCpmk)
                        ->map(function ($pertemuans) {
                            return collect($pertemuans)->sum('bobot');
                        })
                        ->sum();

                    $globalTotalBobotMentah = $globalTotalBobotMentah > 0 ? $globalTotalBobotMentah : 1;
                @endphp

                @foreach ($groupsCpmk as $kodeCpmk => $pertemuans)
                    @php
                        $totalNilaiKontribusiCpmk = 0;
                        $skorMurniCpmk = 0;

                        $allMapping = collect($this->mapping_pertemuan)->values();

                        $bobotMentahCpmkIni = collect($pertemuans)->sum('bobot');
                        $bobotNormalisasiGlobalCpmk = ($bobotMentahCpmkIni / $globalTotalBobotMentah) * 100;

                        foreach ($pertemuans as $pertemuan) {
                            $originalIndex = $allMapping->search(function ($item) use ($pertemuan) {
                                return $item['kode_scpmk'] === $pertemuan['kode_scpmk'] &&
                                    $item['kode_cpmk'] === $pertemuan['kode_cpmk'];
                            });

                            $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                            $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                            $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
                        }
                        $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;

                    @endphp

                    <td class="table-second text-center font-bold">
                        {{ !empty($arrayNilai) ? round($skorMurniCpmk, 2) : '-' }}
                    </td>

                    <td class="table-second text-center font-semibold text-blue-600">
                        {{ !empty($arrayNilai) ? round($totalNilaiKontribusiCpmk, 2) : '-' }}
                    </td>

                    <td class="table-sub text-center border-r font-medium text-green-600">
                        {{ round($bobotNormalisasiGlobalCpmk, 2) }}%
                    </td>
                @endforeach
            @endif

            <td class="table-second table-border-r text-center">{{ $detail->angkatan ?? '-' }}</td>

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer focus:outline-none">
                        @include('livewire.global.table.badge.kode-wilayah-badge', [
                            'xValue' => $user->wilayah,
                            'sortir' => $user->kode_wilayah,
                        ])
                    </button>

                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>
            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

            <td class="table-second min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

        </tr>

    @empty
        <tr>
            <td colspan="{{ 12 + (count($groupsCpmk ?? []) * 3) }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Mahasiswa Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
