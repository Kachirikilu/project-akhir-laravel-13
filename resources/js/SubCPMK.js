document.addEventListener("alpine:init", () => {
    Alpine.store("scpmk", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        scpmk_delete: "",
        kode_scpmk_delete: "",

        setEdit(val) {
            this.isEdit = val;
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
        bobot: 0,

        setValueSCPMK(kode, deskripsi, materi, metodologi, indikator,
            metode, desTugas, wTugas, wMandiri, bobot
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
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_scpmk_1 = huruf ? huruf[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_scpmk_2 = angka ? angka[0] : "";
            } else {
                this.kode_scpmk_1 = "";
                this.kode_scpmk_2 = "";
            }
        },

        setDeleteSCPMK(namaSCPMK, kodeSCPMKDelete, forceDelete) {
            this.scpmk_delete = namaSCPMK;
            this.kode_scpmk_delete = kodeSCPMKDelete;
            this.isForceDelete = forceDelete;
        },

        reset() {
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

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
        },
    });
});
