<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Keyword Triggers Config
    |--------------------------------------------------------------------------
    |
    | Konfigurasi array keyword trigger dipisahkan berdasarkan kategori
    | dan mengambil nilai default dari file .env (dipisahkan koma).
    |
    */

    'keywords' => [
        // Verifikasi & Autentikasi
        'verify' => explode(',', env('WA_KEYWORD_VERIFY', 'VERIFIKASI,VERIFY,VERYFICATION,IDENTITAS,REGISTRASI,LOGIN,LOG IN,MASUK')),
        'data_diri' => explode(',', env('WA_KEYWORD_DATA_DIRI', 'DATA DIRI,MY SELF,DATA,AUTH,AKUN,USER,TOKEN,CHECK TOKEN,CEK TOKEN,INFORMASI TOKEN,LIMTI,CHECK LIMIT,LIMIT')),
        'update_token' => explode(',', env('WA_KEYWORD_UPDATE_TOKEN', 'ISI TOKEN,PERBARUI TOKEN,TOKEN BARU,RESET TOKEN,TAMBAH TOKEN,PERBARUI LIMIT,UPDATE TOKEN,TOKEN UPDATE')),
        'deactivate' => explode(',', env('WA_KEYWORD_DEACTIVATE', 'NONAKTIFKAN,MATIKAN,DIAM,SILENT,LOGOUT,KELUAR,LOG OUT,MUTE,MIC MATI')),

        // Kelas & Jadwal
        'daftar_kelas' => explode(',', env('WA_KEYWORD_DAFTAR_KELAS', 'LIST KELAS,SEMUA KELAS,DAFTAR KELAS')),
        'kelas_minggu' => explode(',', env('WA_KEYWORD_KELAS_MINGGU', 'MINGGU INI,KELAS MINGGU,JADWAL KELAS,KELAS JADWAL,JADWAL')),
        'kelas_hari' => explode(',', env('WA_KEYWORD_KELAS_HARI', 'HARI INI,KELAS HARI')),
        'kelas_extra' => explode(',', env('WA_KEYWORD_KELAS_EXTRA', 'CHECK KELAS,KELAS SAYA,KELAS')),

        // Excel Nilai
        'excel_nilai' => explode(',', env('WA_KEYWORD_EXCEL_NILAI', 'EXCEL NILAI,INPUT NILAI EXCEL,UPLOAD NILAI EXCEL,INPUT FILE NILAI,INPUT NILAI,FILE NILAI')),
        'excel_get_nilai' => explode(',', env('WA_KEYWORD_EXCEL_GET_NILAI', 'DOWNLOAD NILAI,DOWNLOAD EXCEL NILAI,GET EXCEL NILAI,GET NILAI')),

        // PDF Capaian
        'pdf_get_capaian' => explode(',', env('WA_KEYWORD_PDF_GET_CAPAIAN', 'DOWNLOAD CAPAIAN,DOWNLOAD PDF CAPAIAN,GET PDF CAPAIAN,GET CAPAIAN,PRINT CAPAIAN,PRINT PDF CAPAIAN,PRINT CAPAIAN PDF,DOWNLOAD CPMK,DOWNLOAD PDF CPMK,GET PDF CPMK,GET CPMK,PRINT CPMK,PRINT PDF CPMK,PRINT CPMK PDF,DOWNLOAD CPL,DOWNLOAD PDF CPL,GET PDF CPL,GET CPL,PRINT CPL,PRINT PDF CPL,PRINT CPL PDF')),

        // Nilai Mahasiswa
        'nilai_mahasiswa' => explode(',', env('WA_KEYWORD_NILAI_MAHASISWA', 'NILAI,NILAI SAYA,LIHAT NILAI,NILAI SEMESTER')),

        // RPS
        'daftar_rps' => explode(',', env('WA_KEYWORD_DAFTAR_RPS', 'LIST RPS,SEMUA RPS,DAFTAR RPS')),
        'rps_extra' => explode(',', env('WA_KEYWORD_RPS_EXTRA', 'RPS SAYA,RPS')),
        'pdf_get_rps' => explode(',', env('WA_KEYWORD_PDF_GET_RPS', 'DOWNLOAD RPS,DOWNLOAD PDF RPS,GET PDF RPS,GET RPS,PRINT RPS,PRINT PDF RPS,PRINT RPS PDF')),
    ],

];