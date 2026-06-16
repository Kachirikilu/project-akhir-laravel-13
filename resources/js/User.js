document.addEventListener("alpine:init", () => {
    Alpine.store("user", {
        typeModal: "",
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        email_delete: "",
        role_delete: "",

        setType(val) {
            this.typeModal = val;
        },
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

        // User
        email: "",
        password: "",

        // Admin, Dosen, Mahasiswa
        name: "",
        nip: "",
        nitk: "",
        nidn: "",
        nidk: "",
        nim: "",
        nik: "",
        angkatan: "",
        status: "",

        // Prodi
        pr_id: "",
        nama_pr_search: "",
        pr_items: "",

        count_rps: "",
        total_sks: "",

        rekap_mhs: "",
        index_mhs: "",
        mutu_mhs: "",

        kode_wilayah: "",

        setValueUser(
            email,
            password,
            name,
            nip,
            nitk,
            nidn,
            nidk,
            nim,
            nik,
            angkatan,
            status,
            idPr,
            kodePr,
            prodi,
            departemen,
            fakultas,
            wilayah,
            rps,
            sks,
            rekap,
            index,
            mutu,
        ) {
            this.email = email;
            this.password = password;

            this.name = name;
            this.nip = nip;
            this.nitk = nitk;
            this.nidn = nidn;
            this.nidk = nidk;
            this.nim = nim;
            this.nik = nik;
            this.angkatan = angkatan;
            this.status = status;

            this.pr_id = idPr;
            this.nama_pr_search = prodi;
            this.pr_items = {
                id: idPr,
                kode: kodePr,
                slot1: prodi,
                slot2: departemen,
                slot3: fakultas,
            };
            this.kode_wilayah = wilayah;
            this.count_rps = rps;
            this.total_sks = sks;

            this.rekap_mhs = rekap;
            this.index_mhs = index;
            this.mutu_mhs = mutu;
        },
        setValueUserRPS(
            name,
            nip,
            nim,
            angkatan,
            rps,
            sks,

            rekap,
            index,
            mutu,
        ) {
            this.name = name;
            this.nip = nip;
            this.nim = nim;
            this.angkatan = angkatan;

            this.count_rps = rps;
            this.total_sks = sks;

            this.rekap_mhs = rekap;
            this.index_mhs = index;
            this.mutu_mhs = mutu;
        },
        setDeleteUser(email, role, forceDelete) {
            this.email_delete = email;
            this.role_delete = role;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.status = "";
        // },
        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.email = "";
                this.password = "";

                // Admin, Dosen, Mahasiswa
                this.name = "";
                this.nip = "";
                this.nitk = "";
                this.nidn = "";
                this.nidk = "";
                this.nim = "";
                this.nik = "";
                this.angkatan = "";
                this.status = "";

                // Prodi
                this.pr_id = "";
                this.nama_pr_search = "";
                this.pr_items = "";

                this.kode_wilayah = "";

                // Delete
                this.email_delete = "";
                this.role_delete = "";
                this.showEdit = 0;

                this.count_rps = "";
                this.total_sks = "";

                this.rekap_mhs = "";
                this.index_mhs = "";
                this.mutu_mhs = "";
            }
            if (isAdd == 0) {
                this.typeModal = "";
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
                this.colorIconBg = "";
            }
        },
    });
});
