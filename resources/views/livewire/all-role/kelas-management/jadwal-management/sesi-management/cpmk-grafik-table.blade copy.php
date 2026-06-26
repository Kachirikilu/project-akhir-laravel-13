<x-global.main-layout-table :paginator="$users">

    <x-slot:header>

    </x-slot:header>


    @forelse($users as $user)

        {{-- <tr wire:key="user-{{ $user->id }}" class="table-border">
            <td class="table-main-sticky text-center">{{ $user->identity1 }}</td>
        </tr> --}}

        <tr wire:key="user-chart-{{ $user->id }}"
            class="bg-gray-50/50 dark:bg-zinc-900/30 border-b border-[var(--border-table-color)]">
            <td
                class="table-main-sticky bg-gray-50 dark:bg-zinc-900 text-[10px] uppercase font-bold text-gray-400 text-center py-2">
                Grafik Capaian
            </td>

            @php
                $getCpmkColor = function ($str) {
                    $hash = md5($str);
                    $hue = hexdec(substr($hash, 0, 3)) % 360;
                    return "hsl({$hue}, 70%, 45%)";
                };

                $allMapping = collect($this->mapping_pertemuan)->values();
            @endphp

            <td colspan="{{ 1 + count($groupsCpmk ?? []) * 3 }}" class="p-4">
                <div class="flex flex-col gap-3 w-full max-w-4xl mx-auto">

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
                            // 1. Rekalkulasi Skor CPMK murni & Kontribusi untuk grafik
                            $skorMurniCpmk = 0;
                            $bobotMentahCpmkIni = collect($pertemuans)->sum('bobot');

                            // AMANKAN PEMBAGI GLOBAL
                            $pembagiGlobal = ($globalTotalBobotMentah ?? 0) > 0 ? $globalTotalBobotMentah : 1;
                            $bobotNormalisasiGlobalCpmk = ($bobotMentahCpmkIni / $pembagiGlobal) * 100;


                            foreach ($pertemuans as $pertemuan) {
                                $originalIndex = $allMapping->search(function ($item) use ($pertemuan) {
                                    return $item['kode_scpmk'] === $pertemuan['kode_scpmk'] &&
                                        $item['kode_cpmk'] === $pertemuan['kode_cpmk'];
                                });
                                $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                                $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                                $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
                            }

                            // AMANKAN PEMBAGI CPMK INI
                            $pembagiCpmk = $bobotNormalisasiGlobalCpmk > 0 ? $bobotNormalisasiGlobalCpmk : 1;
                            $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;

                            // Batasi max rendering di 100%
                            $realBarPercent = min($totalNilaiKontribusiCpmk, 100);
                            $pureBarPercent = min($skorMurniCpmk, 100);

                            // 🌟 2. PANGGIL MENGGUNAKAN TANDA '$' KARENA DIA ADALAH CLOSURE VARIABEL
                            $cpmkColor = $getCpmkColor($kodeCpmk);
                        @endphp
                        {{-- Render Single Row Bar CPMK --}}
                        <div class="grid grid-cols-12 items-center gap-2">
                            <div class="col-span-2 text-xs font-bold truncate text-gray-700 dark:text-gray-300"
                                title="{{ $kodeCpmk }}">
                                {{ $kodeCpmk }}
                            </div>

                            <div
                                class="col-span-9 relative h-6 bg-gray-200 dark:bg-zinc-800 rounded-md overflow-hidden ring-1 ring-gray-300/50 dark:ring-zinc-700/50">

                                {{-- 1. Nilai Sebenarnya (Kontribusi Rasio 100) - Opacity Ringan --}}
                                <div class="absolute top-0 left-0 h-full rounded-l-md transition-all duration-500"
                                    style="width: {{ $realBarPercent }}%; background-color: {{ $cpmkColor }}; opacity: 0.35;">
                                </div>

                                {{-- 2. Nilai Skor Murni - Ditimpa Lebih Tebal/Solid di Atasnya --}}
                                <div class="absolute top-1/2 -translate-y-1/2 left-0 h-4 rounded-r-sm transition-all duration-500"
                                    style="width: {{ $pureBarPercent }}%; background-color: {{ $cpmkColor }}; shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                </div>

                                {{-- 3. Garis Batas Capaian Minimal Kriteria (70%) --}}
                                <div class="absolute top-0 bottom-0 left-[70%] w-[2px] bg-red-500 dark:bg-red-400 z-10 before:content-['']"
                                    title="Batas Minimum Kelulusan Capaian (70%)">
                                    <span
                                        class="absolute -top-1.5 -translate-x-1/2 text-[8px] font-black text-red-500 dark:text-red-400 bg-white dark:bg-zinc-900 px-0.5 rounded">70%</span>
                                </div>
                            </div>

                            <div class="col-span-1 text-right text-[11px] font-bold text-gray-600 dark:text-gray-400">
                                {{ round($skorMurniCpmk, 1) }}
                            </div>
                        </div>
                    @endforeach

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ 12 + count($groupsCpmk ?? []) * 3 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Mahasiswa Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
