<div x-data="{ activeTable: '{{ $switchTable ?? 'nilai' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'nilai-mahasiswa-management' || segment === '') ? 'nilai' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">


    {{-- @php
        $periodes = collect([
            [
                'akademik' => '2022/2023',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 9,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 1,
            ],
            [
                'akademik' => '2022/2023',
                'ganjil_genap' => 'Genap',
                'total_mk' => 9,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 2,
            ],
            [
                'akademik' => '2023/2024',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 10,
                'total_sks' => 20,
                'nilai_semester' => '98.75',
                'huruf_semester' => 'A',
                'ip_semester' => '3.95',
                'semester' => 3,
            ],
            [
                'akademik' => '2023/2024',
                'ganjil_genap' => 'Genap',
                'total_mk' => 9,
                'total_sks' => 18,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 4,
            ],
            [
                'akademik' => '2024/2025',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 8,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 5,
            ],
            [
                'akademik' => '2024/2025',
                'ganjil_genap' => 'Genap',
                'total_mk' => 8,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 6,
            ],
            [
                'akademik' => '2025/2026',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 7,
                'total_sks' => 16,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 7,
            ],
            [
                'akademik' => '2025/2026',
                'ganjil_genap' => 'Genap',
                'total_mk' => 3,
                'total_sks' => 14,
                'nilai_semester' => '100.00',
                'huruf_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 8,
            ],
        ])->map(fn($item) => (object) $item);
    @endphp --}}


    @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.global.header.tag-user')
    @endif


    @include(
        'livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-view.nilai-mahasiswa-header',
        [
            'alpine' => 'periode',
            'noBackUrl' => $isNilaiMhs ? 1 : 0,
        ]
    )


    <div class="flex items-center min-w-0 w-full xl:w-auto pb-2" x-data="{ activeTab: @entangle('switchTable') }">
        <div class="w-full">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 w-full">

                <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full pb-1">
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => $stats['periodes'] ?? null,
                        'tabString' => 'nilai',
                        'tabNameString' => 'Nilai Semester Mahasiswa',
                        'icon' => 'chart-pie',
                    ])
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => $stats['cpl'] ?? null,
                        'tabString' => 'cpl',
                        'tabNameString' => 'Capaian Mahasiswa',
                        'icon' => 'document-text',
                    ])
                </div>

            </div>
        </div>
    </div>


    @if ($this->switchTable == 'nilai')
        @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-view.nilai-mahasiswa-card')
    @elseif ($this->switchTable == 'cpl')
        @include('livewire.staff.obe-management.cpl-management.cpl-view.cpl-table', [
            'withCapaian' => 1,
            'isNilaiMahasiswa' => 1,
        ])
    @endif

</div>
