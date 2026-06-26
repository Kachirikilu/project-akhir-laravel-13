@php
    $allMapping = collect($this->mapping_pertemuan)->values();
    $daftarCpmk = collect($groupsCpmk)->keys()->toArray();
    $colorPalette = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899', '#14b8a6'];

    // 🌟 STRATEGI: Pecah data mahasiswa menjadi kelompok per halaman (Misal: 5 mahasiswa per halaman A4 Landscape)
    $mahasiswaPerHalaman = 5;
    $userChunks = collect($users)->chunk($mahasiswaPerHalaman);
@endphp

<style>
    @media print {

        /* Sembunyikan semua elemen aplikasi yang tidak perlu dicetak (Sidebar, Navbar, Tombol) */
        body *,
        .no-print {
            visibility: hidden;
        }

        /* Hanya tampilkan area container print */
        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Paksa Browser Atur Kertas ke A4 Landscape */
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }

        /* Efek Potong Halaman Otomatis */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* Pastikan background warna grafik dan legenda ikut tercetak */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="flex justify-end mb-4 no-print">
    <button onclick="window.print()"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak ke A4 PDF (Landscape)
    </button>
</div>

<div id="print-area" class="flex flex-col gap-8 w-full">
    @php
        $allMapping = collect($this->mapping_pertemuan)->values();
        $daftarCpmk = collect($groupsCpmk)->keys()->toArray();
        $colorPalette = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899', '#14b8a6'];

        // 🌟 AMANKAN DI SINI: Ambil koleksi data aslinya dulu baru di-chunk
        // Ini mencegah data hancur/berubah menjadi integer saat proses chunking
        $userItems = method_exists($users, 'getCollection') ? $users->getCollection() : collect($users);

        $mahasiswaPerHalaman = 5;
        $userChunks = $userItems->chunk($mahasiswaPerHalaman);
    @endphp
    @foreach ($userChunks as $pageIndex => $chunkedUsers)
        @php
            // Siapkan kembali penampung data series halaman ini
            $seriesData = [];
            foreach ($daftarCpmk as $kode) {
                $seriesData[$kode] = ['name' => $kode, 'data' => []];
            }
            $daftarNim = [];

            // 🌟 FIX: Pastikan $chunkedUsers dibaca sebagai objek model utuh
            foreach (collect($chunkedUsers) as $user) {
                // Cek pengaman ekstra: jika $user bukan objek model, skip agar tidak fatal error int
                if (!is_object($user)) {
                    continue;
                }

                $daftarNim[] = $user->identity1;

                $arrayNilai = is_array($user->mhs_nilai_array)
                    ? $user->mhs_nilai_array
                    : json_decode($user->mhs_nilai_array ?? '[]', true);

                $bobotCpmkArray = is_array($user->mhs_bobot_array)
                    ? $user->mhs_bobot_array
                    : json_decode($user->mhs_bobot_array ?? '[]', true);

                foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
                    $skorMurniCpmk = 0;
                    foreach ($pertemuans as $pertemuan) {
                        $originalIndex = $allMapping->search(
                            fn($item) => $item['kode_scpmk'] === $pertemuan['kode_scpmk'] &&
                                $item['kode_cpmk'] === $pertemuan['kode_cpmk'],
                        );
                        $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                        $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                        $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
                    }
                    $seriesData[$kodeCpmk]['data'][] = round($skorMurniCpmk, 1);
                }
            }
            $finalSeries = array_values($seriesData);
        @endphp

        <div
            class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden {{ !$loop->last ? 'page-break' : '' }}">

            <div class="flex items-center justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide">
                        Distribusi Capaian Nilai per Mahasiswa (Halaman {{ $pageIndex + 1 }})
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Menampilkan {{ count($chunkedUsers) }} mahasiswa pada
                        halaman ini</p>
                </div>

                <div
                    class="flex items-center gap-3 bg-gray-50 dark:bg-zinc-950/50 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-zinc-800">
                    @foreach ($daftarCpmk as $index => $kodeCpmk)
                        @php $currentColor = $colorPalette[$index % count($colorPalette)]; @endphp
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $currentColor }};">
                            </div>
                            <span
                                class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ $kodeCpmk }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full p-4" wire:ignore>
                <div x-data="{
                    init() {
                        let isDark = document.documentElement.classList.contains('dark');
                        let options = {
                            series: {{ json_encode($finalSeries) }},
                            chart: {
                                type: 'bar',
                                height: 350, // Ditinggikan sedikit agar proporsional di kertas A4
                                toolbar: { show: false },
                                animations: { enabled: false }, // Matikan animasi agar printer browser tidak menangkap layar kosong
                                fontFamily: 'Inter, sans-serif'
                            },
                            colors: {{ json_encode($colorPalette) }},
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '70%',
                                    borderRadius: 3,
                                    dataLabels: { position: 'top' }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) { return val; },
                                offsetY: -18,
                                style: { fontSize: '10px', fontWeight: '700', colors: [isDark ? '#e4e4e7' : '#374151'] }
                            },
                            stroke: { show: true, width: 1, colors: [isDark ? '#18181b' : '#ffffff'] },
                            legend: { show: false },
                            xaxis: {
                                categories: {{ json_encode($daftarNim) }},
                                labels: { style: { colors: '#1f2937', fontSize: '11px', fontWeight: 'bold' } },
                                axisBorder: { show: true, color: '#9ca3af' }
                            },
                            yaxis: {
                                max: 100,
                                min: 0,
                                tickAmount: 5,
                                labels: { style: { colors: '#4b5563', fontSize: '10px' } },
                                axisBorder: { show: true, color: '#9ca3af' }
                            },
                            grid: {
                                show: true,
                                borderColor: '#9ca3af',
                                strokeDashArray: 0,
                                xaxis: { lines: { show: true } }
                            },
                            annotations: {
                                yaxis: [{
                                    y: 70,
                                    borderColor: '#ef4444',
                                    strokeDashArray: 4,
                                    label: { borderColor: '#ef4444', style: { color: '#fff', background: '#ef4444', fontSize: '9px', fontWeight: 700 }, text: '70%' }
                                }]
                            }
                        };
                        new ApexCharts($refs.canvas, options).render();
                    }
                }">
                    <div x-ref="canvas"></div>
                </div>
            </div>

        </div>
    @endforeach

</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
