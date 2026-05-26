document.addEventListener("alpine:init", () => {
    Alpine.store("prodi", {
        typeModal: "",
        typeModal_delete: "",
        isEdit: 0,
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
            this.kode_dp = kodePr;

            this.nama_fk = fakultas;
            this.kode_fk = kodePr;

            this.dp_items = {
                "id": idDp,
                "kode": kodeDp,
                "slot1": departemen,
                "slot2": fakultas,
            };

            this.fk_items = {
                "id": idFk,
                "kode": kodeFk,
                "slot1": fakultas,
            };
        },

        setDeleteProdi(
            prodi,
            departemen,
            fakultas,
            kodePrDelete,
            type,
            forceDelete
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
        
        reset() {
            this.typeModal = "";
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

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
        }
    });
});