document.addEventListener("alpine:init", () => {
    Alpine.store("tim_dosen", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "text-[var(--contrast-second-text)]",
        colorIconBg: "bg-[var(--contrast-second-text)]/40",

        tim_dosen_delete: "",
        kode_tim_dosen_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val, val2) {
            this.colorIcon = val;
            this.colorIconBg = val2;
        },

        kode: "",
        kode_tim_dosen: "",
        kode_tim_dosen_1: "",
        kode_tim_dosen_2: "",

        nama_tim: "",
        // Prodi
        pr_id: "",
        nama_pr_search: "",
        pr_items: "",


        setValueTimDosen(kode, tim
        ) {
            this.nama_tim = tim;

            if (kode) {
                const mutu = kode.match(/[a-zA-Z]+/g);
                this.kode_tim_dosen_1 = mutu ? mutu[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_tim_dosen_2 = angka ? angka[0] : "";
            } else {
                this.kode_tim_dosen_1 = "";
                this.kode_tim_dosen_2 = "";
            }
        },

        getDataTimDosen() {
            return {
                nama_tim: this.nama_tim,
                kode_tim_dosen: this.kode_tim_dosen,
                kode_tim_dosen_1: this.kode_tim_dosen_1,
                kode_tim_dosen_2: this.kode_tim_dosen_2,
            };
        },

        setValueTimDosenRPS(
            tim,
            ketua,
            nip,
            prodi,
            koor,
            pengajar,
            asisten,
            countRps,
            sks
        ) {
            this.nama_tim = tim;
            this.ketua = ketua;
            this.nip = nip;
            this.prodi = prodi;

            this.count_koordinator = koor;
            this.count_pengajar = pengajar;
            this.count_asisten = asisten;

            this.count_rps = countRps;
            this.total_sks = sks;
        },

        setDeleteTimDosen(namaRef, kodeRefDelete, forceDelete) {
            this.tim_dosen_delete = namaRef;
            this.kode_tim_dosen_delete = kodeRefDelete;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.nama_tim = "";

                this.kode = "";
                this.kode_tim_dosen = "";
                this.kode_tim_dosen_1 = "";
                this.kode_tim_dosen_2 = "";

                // Prodi
                this.pr_id = "";
                this.nama_pr_search = "";
                this.pr_items = "";

                this.ketua = "";
                this.nip = "";
                this.prodi = "";

                this.count_koordinator = "";
                this.count_pengajar = "";
                this.count_asisten = "";

                this.count_rps = "";
                this.total_sks = "";

                this.showEdit = 0;
            }
            if (isAdd == 0) {
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "text-[var(--contrast-second-text)]";
                this.colorIconBg = "bg-[var(--contrast-second-text)]/40";
            }
        },
    });
});
