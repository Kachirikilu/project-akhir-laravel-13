document.addEventListener("alpine:init", () => {
    Alpine.store("cpmk", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        cpmk_delete: "",
        kode_cpmk_delete: "",

        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode_cpmk: "",
        kode_cpmk_1: "",
        kode_cpmk_2: "",
        deskripsi: "",

        count_scpmk: 0,
        total_bobot: 0,

        ref_scpmk: [],

        update(allSubItems) {
            if (!allSubItems || allSubItems.length === 0) {
                this.ref_scpmk = [];
                return;
            }

            let allRefs = allSubItems.flatMap((item) => {
                const scpmkList = item.scpmk || [];
                return scpmkList.flatMap((sub) => sub.ref || []);
            });

            this.ref_scpmk = Array.from(
                new Map(allRefs.map((r) => [r.id, r])).values(),
            );
        },

        setValueCPMK(kode, deskripsi) {
            this.kode_cpmk = kode;
            this.deskripsi = deskripsi;

            if (kode) {
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_cpmk_1 = huruf ? huruf[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_cpmk_2 = angka ? angka[0] : "";
            } else {
                this.kode_cpmk_1 = "";
                this.kode_cpmk_2 = "";
            }
        },

        setCountSCPMK(val) {
            this.count_scpmk = val;

            if (val < 14 && (this.is_draf === 0 || this.is_draf === 1)) {
                this.is_draf = 1;
            } else if (val < 14 && this.is_draf === "") {
                this.is_draf = "";
            }
        },

        setDeleteCPMK(namaCPMK, kodeCPMKDelete, forceDelete) {
            this.cpmk_delete = namaCPMK;
            this.kode_cpmk_delete = kodeCPMKDelete;
            this.isForceDelete = forceDelete;
        },

        reset() {
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

            this.deskripsi = "";
            this.kode_cpmk = "";
            this.kode_cpmk_1 = "";
            this.kode_cpmk_2 = "";
        },
    });
});
