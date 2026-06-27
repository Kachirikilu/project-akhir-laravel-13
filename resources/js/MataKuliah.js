document.addEventListener("alpine:init", () => {
    Alpine.store("mk", {
        typeModal: "",
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        nama_mk_delete: "",
        kode_mk_delete: "",

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

        nama_mk: "",
        digit_semester: "",
        digit_mk: "",
        semester: "",
        kode_blok: "",
        tipe_sks: "",
        sks_kuliah: "",
        is_wajib: "",
        deskripsi: "",
        bahan_kajian: "",

        pr_id: "",
        pr_id_array: [],
        nama_pr_search: "",
        pr_items: "",
        pr_items_array: [],

        // nama_pr_search_array: [],
        // pr_id: "",
        // pr_items: "",
        // pr_id_array: [],
        // pr_items_array: [],

        // init() {
        //     // Listener untuk mengisi store dari Livewire
        //     window.addEventListener('fill-store-mk', (event) => {
        //         this.isEdit = event.detail.isEdit;
        //         this.pr_id = event.detail.pr_id;
        //         this.nama_pr = event.detail.nama_pr;
        //         this.pr_items = event.detail.pr_items;
        //     });
        // },

        setValueMK(
            tingkatanMode,
            namaMK,
            kodeBlok,
            digitSemester,
            digitMK,
            // prId,
            // kodePr,
            // namaProdi,
            // namaDepartemen,
            // namaFakultas,
            semester,
            sksKuliah,
            tipeSKS,
            isWajib,
            deskripsi,
            bahanKajian,
        ) {
            this.typeModal = tingkatanMode;
            this.nama_mk = namaMK;

            this.kode_blok = kodeBlok;
            this.digit_semester = digitSemester;
            this.digit_mk = digitMK;

            this.semester = semester;
            this.sks_kuliah = sksKuliah;
            this.tipe_sks = tipeSKS;
            this.is_wajib = isWajib;

            this.deskripsi = deskripsi;
            this.bahan_kajian = bahanKajian;

            // this.nama_pr_search = namaProdi;
            // this.pr_id = prId;
            // this.pr_items = kodePr;
            // this.pr_items = {
            //     "kode": kodePr,
            //     "slot1": namaProdi,
            //     // "slot2": namaDepartemen,
            //     // "slot3": namaFakultas,
            // };
        },

        setDeleteMK(namaMK, kodeMKDelete, forceDelete) {
            this.nama_mk_delete = namaMK;
            this.kode_mk_delete = kodeMKDelete;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.kode_blok = "";
        //     this.semester = "";
        //     this.tipe_sks = "";
        //     this.is_wajib = "";
        // },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.nama_mk = "";

                this.kode_blok = "";
                this.digit_semester = "";
                this.digit_mk = "";

                this.semester = "";
                this.sks_kuliah = "";
                this.tipe_sks = "";
                this.is_wajib = "";

                this.pr_id = "";
                this.nama_pr_search = "";
                this.pr_items = "";
                this.pr_id_array = [];
                this.pr_items_array = [];

                this.deskripsi = "";
                this.bahan_kajian = "";

                this.nama_mk_delete = "";
                this.kode_mk_delete = "";
                this.showEdit = 0;
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
