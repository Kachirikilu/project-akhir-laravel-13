<?php

namespace Database\Seeders;

use App\Models\Akademik\MataKuliah;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $allProdiIds = Prodi::pluck('id')->toArray();
            $counters = [];

            $getNextDigit = function ($level, $unitId, $semester) use (&$counters) {
                $key = "{$level}_{$unitId}_{$semester}";
                $counters[$key] = ($counters[$key] ?? 0) + 1;

                return str_pad($counters[$key], 2, '0', STR_PAD_LEFT);
            };

            // Biarkan, jangan hapus commad ini
            $elektroDataRaw = [
                ['kode' => 'TKE1103', 'nama' => 'FISIKA TEKNIK', 'sks' => 3, 'smt' => 1, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1105', 'nama' => 'PENGENALAN BIDANG TEKNIK ELEKTRO', 'sks' => 2, 'smt' => 1, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1106', 'nama' => 'DASAR KOMPUTER DAN PEMPROGRAMAN', 'sks' => 2, 'smt' => 1, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1101', 'nama' => 'KESELAMATAN DAN KESEHATAN KERJA', 'sks' => 2, 'smt' => 1, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1104', 'nama' => 'MENGGAMBAR TEKNIK', 'sks' => 2, 'smt' => 1, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1107', 'nama' => 'PRAKTIKUM FISIKA TEKNIK', 'sks' => 1, 'smt' => 1, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE1102', 'nama' => 'KALKULUS', 'sks' => 3, 'smt' => 1, 'level' => 1, 'wajib' => 1],

                ['kode' => 'TKE1213', 'nama' => 'DASAR ELEKTRONIKA', 'sks' => 2, 'smt' => 2, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1214', 'nama' => 'PRAKTIKUM FISIKA ELEKTRO', 'sks' => 1, 'smt' => 2, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE1209', 'nama' => 'FISIKA ELEKTRO', 'sks' => 3, 'smt' => 2, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE1208', 'nama' => 'MATEMATIKA ELEKTRO', 'sks' => 3, 'smt' => 2, 'level' => 1, 'wajib' => 1],

                ['kode' => 'TKE2110', 'nama' => 'PRAKTIKUM PENGUKURAN BESARAN LISTRIK', 'sks' => 1, 'smt' => 3, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE2103', 'nama' => 'PENGETAHUAN LINGKUNGAN', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2105', 'nama' => 'DASAR ANALOG DAN DIGITAL', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2107', 'nama' => 'DASAR SISTEM TELEKOMUNIKASI', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2108', 'nama' => 'PENGUKURAN BESARAN LISTRIK', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2102', 'nama' => 'PROBABILITAS DAN STATISTIK', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2104', 'nama' => 'RANGKAIAN LISTRIK', 'sks' => 3, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2101', 'nama' => 'MATRIKS DAN ANALISA VEKTOR', 'sks' => 3, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2106', 'nama' => 'MANAJEMAN DAN KEWIRAUSAHAAN', 'sks' => 2, 'smt' => 3, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2109', 'nama' => 'PRAKTIKUM DASAR ELEKTRONIKA', 'sks' => 1, 'smt' => 3, 'level' => 1, 'wajib' => 1, 'type' => 2],

                ['kode' => 'TKE2220', 'nama' => 'PRAKTIKUM RANGKAIAN LISTRIK', 'sks' => 1, 'smt' => 4, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE2216', 'nama' => 'RANGKAIAN TRANSIEN DAN LAPLACE', 'sks' => 3, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2211', 'nama' => 'MATEMATIKA TEKNIK', 'sks' => 3, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2218', 'nama' => 'PRAKTIKUM DASAR SISTEM KENDALI', 'sks' => 1, 'smt' => 4, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE2215', 'nama' => 'METODA NUMERIK', 'sks' => 2, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2213', 'nama' => 'DASAR SISTEM KENDALI', 'sks' => 3, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2219', 'nama' => 'PRAKTIKUM DASAR TELEKOMUNIKASI', 'sks' => 1, 'smt' => 4, 'level' => 1, 'wajib' => 1, 'type' => 2],
                ['kode' => 'TKE2217', 'nama' => 'KONVERSI ENERGI LISTRIK', 'sks' => 2, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2212', 'nama' => 'MEDAN ELEKTROMAGNETIK', 'sks' => 3, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE2214', 'nama' => 'MIKROKONTROLER', 'sks' => 2, 'smt' => 4, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE3106', 'nama' => 'TRANSFORMATOR DAYA', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3114', 'nama' => 'JARINGAN SYARAF TIRUAN', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3116', 'nama' => 'JARINGAN TELEKOMUNIKASI', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3120', 'nama' => 'PENGOLAHAN SINYAL DIGITAL', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3113', 'nama' => 'OTOMASI INDUSTRI', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3107', 'nama' => 'KECERDASAN BUATAN', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3104', 'nama' => 'ANALISIS SISTEM KETENAGALISTRIKAN', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3108', 'nama' => 'SISTEM LINIER', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3112', 'nama' => 'ARSITEKTUR KOMPUTER', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3118', 'nama' => 'SISTEM KOMUNIKASI OPTIK', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3119', 'nama' => 'KOMUNIKASI DATA', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3102', 'nama' => 'TEKNIK PENERANGAN DAN INSTALASI', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3105', 'nama' => 'MESIN-MESIN LISTRIK', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3109', 'nama' => 'LOGIKA SAMAR', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3110', 'nama' => 'ELEKTRONIKA DIGITAL', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3115', 'nama' => 'PENGENALAN POLA', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3111', 'nama' => 'INSTRUMENTASI INDUSTRI', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3101', 'nama' => 'BAHAN-BAHAN LISTRIK', 'sks' => 2, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3121', 'nama' => 'SISTEM INTERNET OF THINGS', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3117', 'nama' => 'REKAYASA PERANGKAT LUNAK', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3122', 'nama' => 'PRAKTIKUM TEKNIK TELEKOMUNIKASI DAN INFORMASI', 'sks' => 1, 'smt' => 5, 'level' => 1, 'wajib' => 0, 'type' => 2],
                ['kode' => 'TKE3103', 'nama' => 'TEKNIK TEGANGAN TINGGI', 'sks' => 3, 'smt' => 5, 'level' => 1, 'wajib' => 0],

                ['kode' => 'TKE3255', 'nama' => 'KOMUNIKASI BERGERAK', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3249', 'nama' => 'SISTEM BASIS DATA', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3236', 'nama' => 'BASIS DATA', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3225', 'nama' => 'SISTEM PEMBANGKIT', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],

                ['kode' => 'TKE3254', 'nama' => 'SISTEM TRANSMISI', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3224', 'nama' => 'ELEKTRONIKA DAYA', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3257', 'nama' => 'PEMROGRAMAN TERSTRUKTUR', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3256', 'nama' => 'JARINGAN KOMPUTER', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3252', 'nama' => 'MEKATRONIKA', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3228', 'nama' => 'PRAKTIKUM TEKNIK TEGANGAN TINGGI', 'sks' => 1, 'smt' => 6, 'level' => 1, 'wajib' => 0, 'type' => 2],
                ['kode' => 'TKE3234', 'nama' => 'PRAKTIKUM SISTEM MIKROPROSESOR', 'sks' => 1, 'smt' => 6, 'level' => 1, 'wajib' => 0, 'type' => 2],
                ['kode' => 'TKE3226', 'nama' => 'SISTEM PEMBUMIAN', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3259', 'nama' => 'TEKNIK PENGKODEAN DAN APLIKASI WEB', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3239', 'nama' => 'SISTEM SMART CITY', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3233', 'nama' => 'SISTEM KENDALI OPTIMAL', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3230', 'nama' => 'PENGOLAHAN SINYAL DIGITAL', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3253', 'nama' => 'SISTEM KENDALI LANJUT', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3244', 'nama' => 'TRANSMISI ARUS SEARAH', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3240', 'nama' => 'PROTEKSI TEGANGAN LEBIH', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3251', 'nama' => 'SISTEM KENDALI PROSES', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3232', 'nama' => 'SISTEM KENDALI ADAPTIF', 'sks' => 2, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3246', 'nama' => 'MEDAN ELEKTROMAGNETIK LANJUT', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3245', 'nama' => 'ANALISIS SISTEM KETENAGALISTRIKAN LANJUT', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3237', 'nama' => 'REKAYASA TELETRAFIK', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3258', 'nama' => 'KECERDASAN BUATAN', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3238', 'nama' => 'ELEKTRONIKA KOMUNIKASI', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3243', 'nama' => 'PENGGUNAAN MOTOR LISTRIK', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3242', 'nama' => 'ANALISIS MESIN-MESIN LISTRIK', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3247', 'nama' => 'PERMODELAN DAN SIMULASI MENGGUNAKAN FEM', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3231', 'nama' => 'SISTEM KENDALI MULTIVARIABEL', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3250', 'nama' => 'PEMROGRAMAN LANJUT', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3248', 'nama' => 'SISTEM KENDALI TERDISTRIBUSI', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3235', 'nama' => 'ANTENA DAN PROPAGASI', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3241', 'nama' => 'DIELEKTRIKA', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3227', 'nama' => 'DISTRIBUSI SISTEM KETENAGALISTRIKAN', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3223', 'nama' => 'PROTEKSI RELE', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE3229', 'nama' => 'SISTEM MIKROPROSESOR', 'sks' => 3, 'smt' => 6, 'level' => 1, 'wajib' => 0],

                ['kode' => 'TKE4123', 'nama' => 'SISTEM KENDALI DIGITAL', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4125', 'nama' => 'KOMUNIKASI SATELIT', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4121', 'nama' => 'DATA MINING', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4118', 'nama' => 'OTOMASI DALAM SISTEM KETENAGALISTRIKAN', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4130', 'nama' => 'DIGITAL ENTERPRISE', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4122', 'nama' => 'PEMODELAN SIMULASI NUMERIK', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4127', 'nama' => 'BROADBAND', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4108', 'nama' => 'SISTEM KENDALI CERDAS', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4109', 'nama' => 'PERANCANGAN TEKNOLOGI INFORMASI', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4124', 'nama' => 'BIG DATA', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4110', 'nama' => 'PERANCANGAN JARINGAN TELEKOMUNIKASI', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4114', 'nama' => 'PROTEKSI MOTOR LISTRIK', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4131', 'nama' => 'KULIAH KERJA NYATA', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4117', 'nama' => 'ANALISIS GANGGUAN KETENAGALISTRIKAN', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4111', 'nama' => 'KAPITA SELEKTA TEKNIK TELEKOMUNIKASI DAN INFORMASI', 'sks' => 1, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4119', 'nama' => 'PERANCANGAN SISTEM KENDALI', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4105', 'nama' => 'PRAKTIKUM DISTRIBUSI SISTEM KETENAGALISTRIKAN', 'sks' => 1, 'smt' => 7, 'level' => 1, 'wajib' => 0, 'type' => 2],
                ['kode' => 'TKE4116', 'nama' => 'KEANDALAN SISTEM KETENAGALISTRIKAN', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4115', 'nama' => 'PERENCANAAN MESIN-MESIN LISTRIK', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4101', 'nama' => 'METODE PENELITIAN DAN PENULISAN ILMIAH', 'sks' => 2, 'smt' => 7, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE4002', 'nama' => 'PROYEK TUGAS AKHIR 1', 'sks' => 2, 'smt' => 7, 'level' => 1, 'wajib' => 1],

                ['kode' => 'TKE4129', 'nama' => 'APLIKASI MOBILE', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4107', 'nama' => 'KAPITA SELEKTA TEKNIK KONTROL', 'sks' => 1, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4104', 'nama' => 'PRAKTIKUM MESIN-MESIN LISTRIK', 'sks' => 1, 'smt' => 7, 'level' => 1, 'wajib' => 0, 'type' => 2],
                ['kode' => 'TKE4120', 'nama' => 'KOMUNIKASI DATA', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4126', 'nama' => 'KINERJA SISTEM TELEKOMUNIKASI', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4103', 'nama' => 'PERENCANAAN SISTEM KELISTRIKAN', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4001', 'nama' => 'KERJA PRAKTIK', 'sks' => 2, 'smt' => 7, 'level' => 1, 'wajib' => 1],
                ['kode' => 'TKE4112', 'nama' => 'TEKNIK ISOLASI TEGANGAN TINGGI', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4102', 'nama' => 'TRANSMISI DAYA ARUS BOLAK BALIK DAN GARDU INDUK', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4128', 'nama' => 'KEAMANAN SISTEM', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4106', 'nama' => 'ROBOTIKA', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],
                ['kode' => 'TKE4113', 'nama' => 'KESESUAIAN MEDAN ELEKTROMAGNETIK', 'sks' => 3, 'smt' => 7, 'level' => 1, 'wajib' => 0],

                ['kode' => 'TKE4003', 'nama' => 'PROYEK TUGAS AKHIR 2', 'sks' => 6, 'smt' => 8, 'level' => 1, 'wajib' => 1],
            ];

            foreach ($elektroDataRaw as $data) {
                $digitMkRaw = substr($data['kode'], -2);
                $smt = $data['smt'];

                $mk = MataKuliah::create([
                    'level_mk' => $data['level'],
                    'nama_mk' => $data['nama'],
                    'digit_semester' => $this->generateDigitSemester($smt, false),
                    'digit_mk' => $digitMkRaw,
                    'semester' => $smt,
                    'sks_kuliah' => $data['sks'],
                    'tipe_sks' => $data['type'] ?? 1,
                    'is_wajib' => $data['wajib'],
                    'deskripsi' => 'Deskripsi untuk '.$data['nama'],
                    'bahan_kajian' => 'Bahan kajian '.$data['nama'],
                ]);

                $mk->prodis()->attach(1);
            }
            // Biarkan, jangan hapus commad ini

            // 1. MK UNIVERSITAS (Level 4) - Contoh: MK Kepemimpinan, Pancasila
            $namaMkUniv = ['Pancasila', 'Kewarganegaraan', 'Bahasa Indonesia', 'Kepemimpinan'];
            foreach ($namaMkUniv as $nama) {
                $smt = rand(1, 4);
                $mk = MataKuliah::create([
                    'level_mk' => 4,
                    'nama_mk' => $nama,
                    'digit_semester' => ceil($smt / 2).($smt % 2 == 0 ? 2 : 1),
                    'digit_mk' => $getNextDigit(4, 0, $smt),
                    'semester' => $smt,
                    'sks_kuliah' => 2, 'tipe_sks' => 1, 'is_wajib' => true,
                    'deskripsi' => 'MK Universitas', 'bahan_kajian' => 'Umum',
                ]);
                $mk->prodis()->attach($allProdiIds);
            }

            // 2. MK FAKULTAS, DEPARTEMEN, DAN PRODI
            $fakultasList = Fakultas::with('departemens.prodis')->get();
            foreach ($fakultasList as $fak) {
                // Level 3: MK Fakultas
                for ($i = 1; $i <= 2; $i++) {
                    $smt = rand(1, 6);
                    $mk = MataKuliah::create([
                        'level_mk' => 3, 'nama_mk' => "MK Fak $fak->nama_fakultas $i",
                        'digit_semester' => ceil($smt / 2).($smt % 2 == 0 ? 2 : 1),
                        'digit_mk' => $getNextDigit(3, $fak->id, $smt),
                        'semester' => $smt, 'sks_kuliah' => 3, 'tipe_sks' => 1,
                        'is_wajib' => true, 'deskripsi' => 'Deskripsi dan Bahan Kajian', 'bahan_kajian' => 'Deskripsi dan Bahan Kajian',
                    ]);
                    $mk->prodis()->attach($fak->departemens->flatMap->prodis->pluck('id'));
                }

                foreach ($fak->departemens as $dept) {
                    // Level 2: MK Departemen
                    for ($i = 1; $i <= 2; $i++) {
                        $smt = rand(1, 6);
                        $mk = MataKuliah::create([
                            'level_mk' => 2, 'nama_mk' => "MK Dept $dept->nama_dept $i",
                            'digit_semester' => ceil($smt / 2).($smt % 2 == 0 ? 2 : 1),
                            'digit_mk' => $getNextDigit(2, $dept->id, $smt),
                            'semester' => $smt, 'sks_kuliah' => 3, 'tipe_sks' => 1,
                            'is_wajib' => true, 'deskripsi' => 'Deskripsi dan Bahan Kajian', 'bahan_kajian' => 'Deskripsi dan Bahan Kajian',
                        ]);
                        $mk->prodis()->attach($dept->prodis->pluck('id'));
                    }

                    foreach ($dept->prodis as $prodi) {
                        // Level 1: MK Prodi
                        for ($i = 1; $i <= 3; $i++) {
                            $smt = rand(1, 8);
                            $mk = MataKuliah::create([
                                'level_mk' => 1, 'nama_mk' => "MK $prodi->prodi $i",
                                'digit_semester' => ceil($smt / 2).($smt % 2 == 0 ? 2 : 1),
                                'digit_mk' => $getNextDigit(1, $prodi->id, $smt),
                                'semester' => $smt, 'sks_kuliah' => 3, 'tipe_sks' => 1,
                                'is_wajib' => true, 'deskripsi' => 'Deskripsi dan Bahan Kajian', 'bahan_kajian' => 'Deskripsi dan Bahan Kajian',
                            ]);
                            $mk->prodis()->attach($prodi->id);
                        }
                    }
                }
            }
        });
    }

    private function generateDigitSemester($semester, $isTA = false)
    {
        $group = ceil($semester / 2);
        if ($isTA) {
            return $group.'0';
        }
        $parity = $semester % 2 === 0 ? 2 : 1;

        return $group.$parity;
    }

    private function attachProdis($mk, $level, $prodi, $allProdiIds)
    {
        if ($level == 4) {
            $mk->prodis()->attach($allProdiIds);
        } elseif ($level == 3) {
            $fakultasIdAsli = $prodi->dp_rel->fk_id;
            $fakultas = Fakultas::find($fakultasIdAsli);

            if ($fakultas) {
                $prodiIds = [];
                foreach ($fakultas->departemens as $dept) {
                    foreach ($dept->prodis as $p) {
                        $prodiIds[] = $p->id;
                    }
                }
                $mk->prodis()->attach(array_unique($prodiIds));
            }
        } elseif ($level == 2) {
            $departemen = Departemen::find($prodi->dp_id);

            if ($departemen) {
                $prodiIds = $departemen->prodis->pluck('id')->toArray();
                $mk->prodis()->attach($prodiIds);
            }
        } else {
            $mk->prodis()->attach($prodi->id);
        }
    }
}
