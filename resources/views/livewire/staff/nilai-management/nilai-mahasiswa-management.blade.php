<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'nilai-mahasiswa-management' || segment === '') ? '' : segment;
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
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 1,
            ],
            [
                'akademik' => '2022/2023',
                'ganjil_genap' => 'Genap',
                'total_mk' => 9,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 2,
            ],
            [
                'akademik' => '2023/2024',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 10,
                'total_sks' => 20,
                'nilai_semester' => '98.75',
                'mutu_semester' => 'A',
                'ip_semester' => '3.95',
                'semester' => 3,
            ],
            [
                'akademik' => '2023/2024',
                'ganjil_genap' => 'Genap',
                'total_mk' => 9,
                'total_sks' => 18,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 4,
            ],
            [
                'akademik' => '2024/2025',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 8,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 5,
            ],
            [
                'akademik' => '2024/2025',
                'ganjil_genap' => 'Genap',
                'total_mk' => 8,
                'total_sks' => 19,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 6,
            ],
            [
                'akademik' => '2025/2026',
                'ganjil_genap' => 'Ganjil',
                'total_mk' => 7,
                'total_sks' => 16,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 7,
            ],
            [
                'akademik' => '2025/2026',
                'ganjil_genap' => 'Genap',
                'total_mk' => 3,
                'total_sks' => 14,
                'nilai_semester' => '100.00',
                'mutu_semester' => 'A',
                'ip_semester' => '4.00',
                'semester' => 8,
            ],
        ])->map(fn($item) => (object) $item);
    @endphp --}}
    @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.global.header.tag-user')
    @endif
    
    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-view.nilai-mahasiswa-header', [
        'alpine' => 'periode',
        'noBackUrl' => $isNilaiMhs ? 1 : 0,
    ])

    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-view.nilai-mahasiswa-card')

</div>
