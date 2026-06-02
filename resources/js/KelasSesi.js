document.addEventListener("alpine:init", () => {
    Alpine.store("sesi", {
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",
        colorIconBg: "",

        nama_sesi_delete: "",
        kode_sesi_delete: "",

        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val, val2) {
            this.colorIcon = val;
            this.colorIconBg = val2;
        },

        search: "",
        perPage: 8,
        sortField: "pertemuan_ke",
        sortDirection: "asc",
        currentPage: 1,
        totalPages: 1,

        fromItem: 1,
        toItem: 8,
        totalItems: 0,

        sesi_id: "",
        pertemuan_ke: "",
        absen: "",
        keterangan: "",

        rps_id_show: "",

        jam_mulai: "",
        jam_berakhir: "",

        pertemuan_ke: "",
        pertemuan_ke_name: "",

        tanggal: "",

        deskripsi: "",
        materi: "",
        metodologi: "",
        indikator: "",
        deksripsi_tugas: "",
        waktu_tugas: "",
        waktu_mandiri: "",

        nama_mahasiswa: "",
        nim_mahasiswa: "",

        mhs_poin_absensi: 0,
        mhs_masuk: 0,
        mhs_dispensasi: 0,
        mhs_terlambat: 0,
        mhs_izin: 0,
        mhs_sakit: 0,
        mhs_mangkir: 0,
        mhs_tidak_masuk: 0,

        w_pelaksaan: "",
        w_berakhir: "",
        w_telat: "",
        w_dispensasi: "",

        sks: "",
        sks_menit: 0,

        setValueSesi(
            jamMulai,
            jamBerakhir,
            pertemuan,
            tanggal,

            deskripsi,
            materi,
            metodologi,
            indikator,
            tugas,
            wTugas,
            wMandiri,

            sks,
        ) {
            this.jam_mulai = jamMulai?.slice(0, 5) || "";
            this.jam_berakhir = jamBerakhir?.slice(0, 5) || "";

            this.pertemuan_ke = pertemuan;
            this.pertemuan_ke_name = "Pertemuan " + pertemuan;
            this.tanggal = tanggal;

            this.deskripsi = deskripsi;
            this.materi = materi;
            this.metodologi = metodologi;
            this.indikator = indikator;
            this.deskripsi_tugas = tugas;
            this.waktu_tugas = wTugas;
            this.waktu_mandiri = wMandiri;

            this.sks = sks;
        },
        setValueAbsenSesi(
            sesiId,
            pertemuanKe,
            wPelaksanaan,
            wBerakhir,
            wTelat,
            wDispensasi,
        ) {
            this.sesi_id = sesiId;
            this.pertemuan_ke = pertemuanKe;

            this.w_pelaksanaan = wPelaksanaan;
            this.w_berakhir = wBerakhir;
            this.w_telat = wTelat;
            this.w_dispensasi = wDispensasi;

            const opsiTersedia = this.getOpsiStatus();
            if (opsiTersedia.length > 0) {
                this.absen = opsiTersedia[0].label;
            }
        },

        getWaktuLokal() {
            let d = new Date();
            let tzOffset = d.getTimezoneOffset() * 60000;
            let waktuLokal = new Date(d.getTime() - tzOffset);
            return waktuLokal.toISOString().slice(0, 16);
        },

        getOpsiStatus() {
            const sekarang = this.getWaktuLokal();

            const masterStatus = [
                {
                    label: "Hadir",
                    icon: "check-circle",
                    bg_active:
                        "bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500 ring-emerald-500",
                    icon_active:
                        "bg-emerald-500/10 text-emerald-600 dark:text-emerald-400",
                    icon_default: "text-emerald-500",
                },
                {
                    label: "Terlambat",
                    icon: "clock",
                    bg_active:
                        "bg-amber-50 dark:bg-amber-950/30 border-amber-500 ring-amber-500",
                    icon_active:
                        "bg-amber-500/10 text-amber-600 dark:text-amber-400",
                    icon_default: "text-amber-500",
                },
                {
                    label: "Dispensasi",
                    icon: "shield-check",
                    bg_active:
                        "bg-cyan-50 dark:bg-cyan-950/30 border-cyan-500 ring-cyan-500",
                    icon_active:
                        "bg-cyan-500/10 text-cyan-600 dark:text-cyan-400",
                    icon_default: "text-cyan-500",
                },
                {
                    label: "Izin",
                    icon: "document-text",
                    bg_active:
                        "bg-blue-50 dark:bg-blue-950/30 border-blue-500 ring-blue-500",
                    icon_active:
                        "bg-blue-500/10 text-blue-600 dark:text-blue-400",
                    icon_default: "text-blue-500",
                },
                {
                    label: "Sakit",
                    icon: "heart",
                    bg_active:
                        "bg-rose-50 dark:bg-rose-950/30 border-rose-500 ring-rose-500",
                    icon_active:
                        "bg-rose-500/10 text-rose-600 dark:text-rose-400",
                    icon_default: "text-rose-500",
                },
                {
                    label: "Absen",
                    icon: "x-circle",
                    bg_active:
                        "bg-red-50 dark:bg-red-950/30 border-red-500 ring-red-500",
                    icon_active: "bg-red-500/10 text-red-600 dark:text-red-400",
                    icon_default: "text-red-500",
                },
            ];

            return masterStatus.filter((item) => {
                if (item.label === "Absen") {
                    return true;
                }
                if (["Hadir", "Izin"].includes(item.label)) {
                    return (
                        sekarang >= this.w_pelaksanaan &&
                        sekarang <= this.w_telat
                    );
                }
                if (item.label === "Sakit") {
                    return (
                        sekarang >= this.w_pelaksanaan &&
                        sekarang <= this.w_berakhir
                    );
                }
                if (item.label === "Terlambat") {
                    return (
                        sekarang > this.w_telat && sekarang <= this.w_berakhir
                    );
                }
                if (item.label === "Dispensasi") {
                    return (
                        sekarang > this.w_pelaksanaan &&
                        sekarang <= this.w_dispensasi
                    );
                }
                return false;
            });
        },

        setValueAbsensi(
            name,
            nim,

            poin,
            masuk,
            dispensasi,
            terlambat,
            izin,
            sakit,
            mangkir,
            tidakMasuk,
        ) {
            this.nama_mahasiswa = name;
            this.nim_mahasiswa = nim;

            this.mhs_poin_absensi = poin;
            this.mhs_masuk = masuk;
            this.mhs_dispensasi = dispensasi;
            this.mhs_terlambat = terlambat;
            this.mhs_izin = izin;
            this.mhs_sakit = sakit;
            this.mhs_mangkir = mangkir;
            this.mhs_tidak_masuk = tidakMasuk;
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteJadwal(namaJadwal, kodeJadwalDelete, forceDelete) {
            this.nama_sesi_delete = namaJadwal;
            this.kode_sesi_delete = kodeJadwalDelete;
            this.isForceDelete = forceDelete;
        },

        resetShow() {
            this.rps_id_show = "";
        },

        reset() {
            this.typeModal = "";
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";
            this.colorIconBg = "";

            this.w_pelaksaan = "";
            this.w_berakhir = "";
            this.w_telat = "";
            this.w_dispensasi = "";

            this.nama_mahasiswa = "";
            this.nim_mahasiswa = "";

            this.mhs_poin_absensi = 0;
            this.mhs_masuk = 0;
            this.mhs_dispensasi = 0;
            this.mhs_terlambat = 0;
            this.mhs_izin = 0;
            this.mhs_sakit = 0;
            this.mhs_mangkir = 0;
            this.mhs_tidak_masuk = 0;

            this.sesi_id = "";
            this.pertemuan_ke = "";
            ((this.absen = ""), (this.keterangan = ""), (this.jam_mulai = ""));
            this.jam_berakhir = "";

            this.pertemuan_ke_name = "";
            this.tanggal = "";

            this.deskripsi = "";
            this.materi = "";
            this.metodologi = "";
            this.indikator = "";
            this.deskripsi_tugas = "";
            this.waktu_tugas = "";
            this.waktu_mandiri = "";

            this.sks = "";
        },

        init() {
            // =========================================
            // AUTO JAM BERAKHIR
            // =========================================
            Alpine.effect(() => {
                const value = this.jam_mulai;

                if (!value) {
                    this.jam_berakhir = "";
                    return;
                }

                const [hour, minute] = value.split(":").map(Number);

                let totalMinute = hour * 60 + minute;

                totalMinute += Number(this.sks_menit || 0);

                const endHour = String(
                    Math.floor(totalMinute / 60) % 24,
                ).padStart(2, "0");

                const endMinute = String(totalMinute % 60).padStart(2, "0");

                this.jam_berakhir = `${endHour}:${endMinute}`;
            });
        },
    });
});
