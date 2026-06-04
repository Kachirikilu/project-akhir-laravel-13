document.addEventListener("alpine:init", () => {
    Alpine.store("cpl", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        cpl_delete: "",
        kode_cpl_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
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

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.deskripsi = "";
                this.kode_cpl = "";
                this.kode_cpl_1 = "";
                this.kode_cpl_2 = "";
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
