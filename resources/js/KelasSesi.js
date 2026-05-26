document.addEventListener("alpine:init", () => {
    Alpine.store("sesi", {
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_sesi_delete: "",
        kode_sesi_delete: "",

        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

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

            sks
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
            
            this.jam_mulai = "";
            this.jam_berakhir = "";

            this.pertemuan_ke = "";
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
