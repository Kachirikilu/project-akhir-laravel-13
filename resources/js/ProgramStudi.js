document.addEventListener("alpine:init", () => {
    Alpine.store("prodi", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_pr_delete: "",
        nama_dp_delete: "",
        nama_fk_delete: "",
        kode_delete: "",

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

        // Prodi
        nama_pr: "",
        dp_id: "",
        nama_dp_search: "",
        kodePr: "",
        strata: "",

        // Departemen
        nama_dp: "",
        dp_id: "",
        nama_fakulas_search: "",
        kodeDp: "",

        // Fakultas
        nama_fk: "",
        kodeFk: "",

        // Items
        departemenItems: "",
        fakultasItems: "",

        setValueProdi(
            prodi,
            strata,
            idDp,
            departemen,
            idFk,
            fakultas,
            kodePr,
            kodeDp,
            kodeFk,
        ) {
            this.nama_pr = prodi;
            this.dp_id = idDp;
            this.nama_dp_search = departemen;
            this.kode_pr = kodePr;
            this.strata = strata;

            this.nama_dp = departemen;
            this.fk_id = idFk;
            this.nama_fk_search = fakultas;
            this.kode_dp = kodeDp;

            this.nama_fk = fakultas;
            this.kode_fk = kodeFk;

            this.dp_items = {
                id: idDp,
                kode: kodeDp,
                slot1: departemen,
                slot2: fakultas,
            };

            this.fk_items = {
                id: idFk,
                kode: kodeFk,
                slot1: fakultas,
            };
        },
        getData() {
            return {
                nama_pr: this.nama_pr,
                dp_id: this.dp_id,
                nama_dp_search: this.nama_dp_search,
                kode_pr: this.kode_pr,
                strata: this.strata,

                nama_dp: this.nama_dp,
                fk_id: this.fk_id,
                nama_fk_search: this.nama_fk_search,
                kode_dp: this.kode_dp,

                nama_fk: this.nama_fk,
                kode_fk: this.kode_fk,

                dp_items: this.dp_items,
                fk_items: this.fk_items,
            };
        },

        setDeleteProdi(
            prodi,
            departemen,
            fakultas,
            kodePrDelete,
            type,
            forceDelete,
        ) {
            this.nama_pr_delete = prodi;
            this.nama_dp_delete = departemen;
            this.nama_fk_delete = fakultas;
            this.kode_delete = kodePrDelete;
            this.typeModal_delete = type;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.strata = "";
        // },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.nama_pr = "";
                this.dp_id = "";
                this.nama_dp_search = "";
                this.kode_pr = "";
                this.strata = "";

                this.nama_dp = "";
                this.fk_id = "";
                this.nama_fk_search = "";
                this.kode_dp = "";

                this.nama_fk = "";
                this.kode_fk = "";

                this.dp_items = "";
                this.fk_items = "";

                this.nama_pr_delete = "";
                this.nama_dp_delete = "";
                this.nama_fk_delete = "";
                this.kode_delete = "";
                this.showEdit = 0;
            }
            if (isAdd == 0) {
                this.typeModal = "";
                this.typeModal_delete = "";
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
            }
        },
    });
});
