@php
        $allMapping = collect($mapping_pertemuan ?? [])->values();
        $globalTotalBobotMentah = collect($groupsCpmk)->map(fn($p) => collect($p)->sum('bobot'))->sum() ?: 1;
        $daftarCpmk = collect($groupsCpmk)->keys()->toArray();

        $seriesData = [];
        foreach ($daftarCpmk as $kode) {
            $seriesData[$kode] = [
                'name' => $kode,
                'data' => [],
            ];
        }
        $daftarNim = [];

        foreach ($users as $user) {
            $daftarNim[] = $user->identity1;

            $arrayNilai = is_array($user->mhs_nilai_array)
                ? $user->mhs_nilai_array
                : json_decode($user->mhs_nilai_array ?? '[]', true);
            $bobotCpmkArray = is_array($user->mhs_bobot_array)
                ? $user->mhs_bobot_array
                : json_decode($user->mhs_bobot_array ?? '[]', true);

            foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
                $skorMurniCpmk = 0;

                $bobotMentahCpmkIni = collect($pertemuans)->sum('bobot');
                $bobotNormalisasiGlobalCpmk = ($bobotMentahCpmkIni / $globalTotalBobotMentah) * 100;

                foreach ($pertemuans as $pertemuan) {
                    $originalIndex = $allMapping->search(
                        fn($item) => $item['kode_scpmk'] === $pertemuan['kode_scpmk'] &&
                            $item['kode_cpmk'] === $pertemuan['kode_cpmk'],
                    );

                    $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                    $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                    $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
                }
                $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;
                $seriesData[$kodeCpmk]['data'][] = round($totalNilaiKontribusiCpmk, 1);
            }
        }

        $bobotCpmkLegend = [];
        foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
            $bobotMentah = collect($pertemuans)->sum('bobot');
            $bobotNormalisasi = ($bobotMentah / $globalTotalBobotMentah) * 100;
            $bobotCpmkLegend[$kodeCpmk] = number_format($bobotNormalisasi, 2, '.', '');
        }

        $finalSeries = array_values($seriesData);
        $colorPalette = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899', '#14b8a6'];
        $totalMahasiswa = count($daftarNim);
        $calculatedWidth = max($totalMahasiswa * 160, 800);
    @endphp