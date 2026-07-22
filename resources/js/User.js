document.addEventListener("alpine:init", () => {
    Alpine.store("user", {
        typeModal: "",
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "text-[var(--contrast-second-text)]",
        colorIconBg: "bg-[var(--contrast-second-text)]/40",
        
        // $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400', colors2[type] ?? 'bg-gray-50 dark:bg-gray-950/40');

        label_id1_delete: "",
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
        angkatan_mhs: "",
        status: "",

        // Prodi
        pr_id: "",
        nama_pr_search: "",
        pr_items: "",

        count_rps: "",
        total_sks: "",

        rekap_mhs: "",
        ipk_mhs: "",
        mutu_mhs: "",

        kode_wilayah: "",

        jenis_kelamin: "",
        agama: "",
        tempat_lahir: "",
        tanggal_lahir: "",

        kode_no_hp: "+62",
        no_hp_back: "",
        no_hp: "",

        label_id1: "",
        identity1: "",

        pr_id_show: "",

        setValueUser(email, label1, id1, rps, sks, rekap, index, mutu) {
            this.email = email;
            this.label_id1 = label1;
            this.identity1 = id1;

            this.count_rps = rps;
            this.total_sks = sks;
            this.rekap_mhs = rekap;
            this.ipk_mhs = index;
            this.mutu_mhs = mutu;
        },
        getDataUser() {
            return {
                email: this.email,
                // count_rps: this.count_rps,
                // total_sks: this.total_sks,
                // rekap_mhs: this.rekap_mhs,
                // ipk_mhs: this.ipk_mhs,
                // mutu_mhs: this.mutu_mhs,
            };
        },

        setValueUserFull(
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
            prId,
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

            jk,
            agama,
            tmtLahir,
            tglLahir,
            noHP,
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

            this.pr_id = prId;
            this.nama_pr_search = prodi;
            this.pr_items = {
                id: prId,
                kode: kodePr,
                slot1: prodi,
                slot2: departemen,
                slot3: fakultas,
            };
            this.kode_wilayah = wilayah;
            this.count_rps = rps;
            this.total_sks = sks;

            this.rekap_mhs = rekap;
            this.ipk_mhs = index;
            this.mutu_mhs = mutu;

            this.jenis_kelamin = jk;
            this.agama = agama;
            this.tempat_lahir = tmtLahir;
            this.tanggal_lahir = tglLahir;

            let cleaned = noHP.replace(/\D/g, "").slice(0, 12);
            let formatted = cleaned.match(/(\d{1,3})(\d{1,4})?(\d{1,5})?/);
            this.no_hp_back = formatted
                ? formatted.slice(1).filter(Boolean).join(" - ")
                : cleaned;
        },
        setValueUserRPS(
            name,
            label1,
            id1,

            prodi,

            rps,
            sks,

            rekap,
            index,
            mutu,

            angkatan,
        ) {
            this.name = name;
            this.label_id1 = label1;
            this.identity1 = id1;

            this.prodi = prodi;

            this.count_rps = rps;
            this.total_sks = sks;

            this.rekap_mhs = rekap;
            this.ipk_mhs = index;
            this.mutu_mhs = mutu;

            this.angkatan_mhs = angkatan;
            // this.pr_id_show = prId;
        },

    
        // setValueUserRPS(
        //     name,
        //     nip,
        //     nim,
        //     angkatan,
        //     rps,
        //     sks,

        //     rekap,
        //     index,
        //     mutu,
        //     prId,
        // ) {
        //     this.name = name;
        //     this.nip = nip;
        //     this.nim = nim;
        //     this.angkatan = angkatan;

        //     this.count_rps = rps;
        //     this.total_sks = sks;

        //     this.rekap_mhs = rekap;
        //     this.ipk_mhs = index;
        //     this.mutu_mhs = mutu;
        //     this.pr_id_show = prId;
        // },
        setDeleteUser(email, role, forceDelete) {
            this.label_id1_delete = email;
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
                this.angkatan_mhs = "";
                this.status = "";

                this.label_id1 = "";
                this.identity1 = "";

                // Prodi
                this.pr_id = "";
                this.nama_pr_search = "";
                this.pr_items = "";

                this.kode_wilayah = "";

                // Delete
                this.label_id1_delete = "";
                this.role_delete = "";
                this.showEdit = 0;

                this.count_rps = "";
                this.total_sks = "";

                this.rekap_mhs = "";
                this.ipk_mhs = "";
                this.mutu_mhs = "";
                this.pr_id_show = "";

                this.jenis_kelamin = "";
                this.agama = "";
                this.tempat_lahir = "";
                this.tanggal_lahir = "";

                this.no_hp_back = "";
                this.no_hp = "";
            }
            if (isAdd == 0) {
                this.typeModal = "";
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "text-[var(--contrast-second-text)]";
                this.colorIconBg = "bg-[var(--contrast-second-text)]/40";
            }
        },

        resetLite(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.email = "";
            }
            if (isAdd == 0) {
                this.typeModal = "";
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "text-[var(--contrast-second-text)]";
                this.colorIconBg = "bg-[var(--contrast-second-text)]/40";
            }
        },
    });
});
