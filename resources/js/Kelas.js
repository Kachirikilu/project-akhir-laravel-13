document.addEventListener("alpine:init", () => {
    Alpine.store("kelas", {
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",
        te: "",

        nama_kelas_delete: "",
        kode_kelas_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val) {
            this.colorIcon = val;
        },

        rps_id_show: "",

        kode_kelas: "",
        kode_kelas_1: "",
        kode_kelas_2: "",
        nama_kelas: "",
        deskripsi: "",

        pr_id: "",
        nama_pr_search: "",
        pr_items: "",
        rps_id: "",
        nama_rps_search: "",
        rps_items: "",

        setValueKelas(
            kode,
            kelas,
            deskripsi,
            idPr,
            kodePr,
            prodi,
            departemen,
            fakultas,
            idRPS,
            kodeRPS,
            rps,
            sksRPS,
            wajibRPS,
            drafRPS,
        ) {
            this.kode_kelas = kode;
            this.nama_kelas = kelas;
            this.deskripsi = deskripsi;

            if (kode) {
                const mutu = kode.match(/[a-zA-Z]+/g);
                this.kode_kelas_1 = mutu ? mutu[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_kelas_2 = angka ? angka[0] : "";
            } else {
                this.kode_kelas_1 = "";
                this.kode_kelas_2 = "";
            }

            this.pr_id = idPr;
            this.nama_pr_search = prodi;
            this.pr_items = {
                id: idPr,
                kode: kodePr,
                slot1: prodi,
                slot2: departemen,
                slot3: fakultas,
            };

            this.rps_id = idRPS;
            this.nama_rps_search = rps;
            this.rps_items = {
                id: idRPS,
                kode: kodeRPS,
                slot1: rps,
                slot2: sksRPS,
                slot3: wajibRPS,
                slot4: drafRPS,
            };
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteKelas(namaKelas, kodeKelasDelete, forceDelete) {
            this.nama_kelas_delete = namaKelas;
            this.kode_kelas_delete = kodeKelasDelete;
            this.isForceDelete = forceDelete;
        },

        resetShow() {
            this.rps_id_show = "";
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                ((this.kode_kelas = ""),
                    (this.kode_kelas_1 = ""),
                    (this.kode_kelas_2 = ""),
                    (this.nama_kelas = ""));
                this.deskripsi = "";

                this.pr_id = "";
                this.nama_pr_search = "";
                this.pr_items = "";

                this.rps_id = "";
                this.nama_rps_search = "";
                this.rps_items = "";
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
