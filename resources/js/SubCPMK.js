document.addEventListener("alpine:init", () => {
    Alpine.store("scpmk", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        scpmk_delete: "",
        kode_scpmk_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode_scpmk: "",
        kode_scpmk_1: "",
        kode_scpmk_2: "",
        deskripsi: "",
        materi: "",
        metodologi: "",
        indikator: "",
        metode: "",
        deskripsi_tugas: "",
        waktu_tugas: "",
        waktu_mandiri: "",
        bobot: "",

        setValueSCPMK(
            kode,
            deskripsi,
            materi,
            metodologi,
            indikator,
            metode,
            desTugas,
            wTugas,
            wMandiri,
            bobot,
        ) {
            this.kode_scpmk = kode;
            this.deskripsi = deskripsi;
            this.materi = materi;
            this.metodologi = metodologi;
            this.indikator = indikator;
            this.metode = metode;
            this.deskripsi_tugas = desTugas;
            this.waktu_tugas = wTugas;
            this.waktu_mandiri = wMandiri;
            this.bobot = bobot;

            if (kode) {
                const mutu = kode.match(/[a-zA-Z]+/g);
                this.kode_scpmk_1 = mutu ? mutu[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_scpmk_2 = angka ? angka[0] : "";
            } else {
                this.kode_scpmk_1 = "";
                this.kode_scpmk_2 = "";
            }
        },

        getDataSCPMK() {
            return {
                kode_scpmk: this.kode_scpmk,
                deskripsi: this.deskripsi,
                materi: this.materi,
                metodologi: this.metodologi,
                indikator: this.indikator,
                metode: this.metode,
                deskripsi_tugas: this.deskripsi_tugas,
                waktu_tugas: this.waktu_tugas,
                waktu_mandiri: this.waktu_mandiri,
                bobot: this.bobot,
                kode_scpmk: this.kode_scpmk,
                kode_scpmk_1: this.kode_scpmk_1,
                kode_scpmk_2: this.kode_scpmk_2,
            };
        },

        setDeleteSCPMK(namaSCPMK, kodeSCPMKDelete, forceDelete) {
            this.scpmk_delete = namaSCPMK;
            this.kode_scpmk_delete = kodeSCPMKDelete;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.deskripsi = "";
                this.materi = "";
                this.metodologi = "";
                this.indikator = "";
                this.metode = "";
                this.deskripsi_tugas = "";
                this.waktu_tugas = "";
                this.waktu_mandiri = "";
                this.bobot = "";

                this.kode_scpmk = "";
                this.kode_scpmk_1 = "";
                this.kode_scpmk_2 = "";
                this.showEdit = 0;
            }

            if (isAdd == 0) {
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
            }
        },
    });
});
