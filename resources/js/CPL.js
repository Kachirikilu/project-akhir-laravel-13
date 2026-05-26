document.addEventListener("alpine:init", () => {
    Alpine.store("cpl", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        cpl_delete: "",
        kode_cpl_delete: "",

        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode_cpl: "",
        kode_cpl_1: "",
        kode_cpl_2: "",
        deskripsi: "",

        setValueCPL(kode, deskripsi) {
            this.kode_cpl = kode;
            this.deskripsi = deskripsi;

            if (kode) {
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_cpl_1 = huruf ? huruf[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_cpl_2 = angka ? angka[0] : "";
            } else {
                this.kode_cpl_1 = "";
                this.kode_cpl_2 = "";
            }
        },

        setDeleteCPL(namaCPL, kodeCPLDelete, forceDelete) {
            this.cpl_delete = namaCPL;
            this.kode_cpl_delete = kodeCPLDelete;
            this.isForceDelete = forceDelete;
        },

        reset() {
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

            this.deskripsi = "";
            this.kode_cpl = "";
            this.kode_cpl_1 = "";
            this.kode_cpl_2 = "";
        },
    });
});
