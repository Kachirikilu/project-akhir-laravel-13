document.addEventListener("alpine:init", () => {
    Alpine.store("cpl", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal: "",
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        cpl_delete: "",
        kode_cpl_delete: "",

        setType(val) {
            this.typeModal = val;
        },
        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode: "",
        kode_cpl: "",
        kode_cpl_1: "",
        kode_cpl_2: "",
        deskripsi: "",

        rekap_cpl_pr: "",
        index_cpl_pr: "",
        mutu_cpl_pr: "",

        setValueCPL(tingkatanMode, kode, deskripsi) {
            this.typeModal = tingkatanMode;
            this.kode_cpl = kode;
            this.deskripsi = deskripsi;

            if (kode) {
                const mutu = kode.match(/[a-zA-Z]+/g);
                this.kode_cpl_1 = mutu ? mutu[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_cpl_2 = angka ? angka[0] : "";
            } else {
                this.kode_cpl_1 = "";
                this.kode_cpl_2 = "";
            }
        },

        getDataCPL() {
            return {
                typeModal: this.typeModal,
                kode_cpl: this.kode_cpl,
                deskripsi: this.deskripsi,
                kode_cpl: this.kode_cpl,
                kode_cpl_1: this.kode_cpl_1,
                kode_cpl_2: this.kode_cpl_2,
            };
        },

        setValueCPLRPS(
            kode,
            rekap,
            index,
            mutu,
        ) {
            this.kode = kode;
            this.rekap_cpl_pr = rekap;
            this.index_cpl_pr = index;
            this.mutu_cpl_pr = mutu;
        },

        setDeleteCPL(namaCPL, kodeCPLDelete, forceDelete) {
            this.cpl_delete = namaCPL;
            this.kode_cpl_delete = kodeCPLDelete;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.deskripsi = "";
                this.kode = "";
                this.kode_cpl = "";
                this.kode_cpl_1 = "";
                this.kode_cpl_2 = "";
                this.showEdit = 0;

                this.rekap_cpl_pr = "";
                this.index_cpl_pr = "";
                this.mutu_cpl_pr = "";
            }
            if (isAdd == 0) {
                this.typeModal = "";
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
            }
        },
    });
});
