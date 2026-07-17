document.addEventListener("alpine:init", () => {
    Alpine.store("nilai", {
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "text-[var(--contrast-second-text)]",
        colorIconBg: "bg-[var(--contrast-second-text)]/40",

        name_delete: "",
        nim_delete: "",
        kode_rps_delete: "",
        mk_delete: "",

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

        nilai_mahasiswa_id: "",

        search: "",
        perPage: 8,
        sortField: "digit_mk",
        sortDirection: "desc",
        currentPage: 1,
        totalPages: 1,

        fromItem: 1,
        toItem: 8,
        totalItems: 0,

        nama: "",
        nim: "",

        tanggal_unlock: "",
        ganjil_genap: "",
        akademik: "",
        akademik_1: "",
        akademik_2: "",

        kode_rps: 0,
        mk: 0,
        sks: "",

        setValueNilai(
            id,
            name,
            nim,

            rps,
            mk,
            sks,

            nilaiArray,
            bobotArray,
            cpmkArray,
            scpmkArray,
            metodeArray,
        ) {
            this.nilai_mahasiswa_id = id;
            this.nama = name;
            this.nim = nim;

            this.kode_rps = rps;
            this.mk = mk;
            this.sks = sks;

            const arrNilai = Array.isArray(nilaiArray) ? nilaiArray : [];
            const arrBobot = Array.isArray(bobotArray) ? bobotArray : [];
            const arrCpmk = Array.isArray(cpmkArray) ? cpmkArray : [];
            const arrScpmk = Array.isArray(scpmkArray) ? scpmkArray : [];
            const arrMetode = Array.isArray(metodeArray) ? metodeArray : [];

            for (let i = 1; i <= 16; i++) {
                const rawNilai = arrNilai[i - 1];
                const rawBobot = arrBobot[i - 1];
                const rawCpmk = arrCpmk[i - 1];
                const rawScpmk = arrScpmk[i - 1];
                const rawMetode = arrMetode[i - 1];

                this[`nilai_${i}`] = rawNilai ?? "";
                this[`bobot_${i}`] = rawBobot ?? "";
                this[`cpmk_${i}`] = rawCpmk ?? "";
                this[`scpmk_${i}`] = rawScpmk ?? "";
                this[`metode_${i}`] = rawMetode ?? "";

                if (
                    rawBobot !== undefined &&
                    rawBobot !== null &&
                    rawBobot !== ""
                ) {
                    let hitungPersen = parseFloat(rawBobot) * 100;
                    this[`bobot_persen_${i}`] =
                        parseFloat(hitungPersen.toFixed(2)) + "%";
                } else {
                    this[`bobot_persen_${i}`] = "";
                }
            }
        },

        getDataNilai() {
            const data = {
                nilai_mahasiswa_id: this.nilai_mahasiswa_id,
                nama: this.nama,
                nim: this.nim,
                kode_rps: this.kode_rps,
                mk: this.mk,
                sks: this.sks,
            };

            for (let i = 1; i <= 16; i++) {
                data[`nilai_${i}`] = this[`nilai_${i}`];
                data[`bobot_${i}`] = this[`bobot_${i}`];
                data[`cpmk_${i}`] = this[`cpmk_${i}`];
                data[`scpmk_${i}`] = this[`scpmk_${i}`];
                data[`metode_${i}`] = this[`metode_${i}`];
                data[`bobot_persen_${i}`] = this[`bobot_persen_${i}`];
            }

            return data;
        },

        getDataLockNilai() {
            const data = {
                tanggal_unlock: this.tanggal_unlock,
                ganjil_genap: this.ganjil_genap,
                akademik: this.akademik,
                akademik_1: this.akademik_1,
                akademik_2: this.akademik_2,
            };
            return data;
        },

        setDeleteNilai(name, nim, rps, mk, forceDelete) {
            this.name_delete = name;
            this.nim_delete = nim;
            this.kode_rps_delete = rps;
            this.mk_delete = mk;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.nilai_mahasiswa_id = "";
                this.nama = "";
                this.nim = "";

                this.kode_rps = "";
                this.mk = "";
                this.sks = "";

                for (let i = 1; i <= 16; i++) {
                    this[`nilai_${i}`] = "";
                    this[`bobot_${i}`] = "";
                    this[`bobot_persen_${i}`] = "";
                    this[`cpmk_${i}`] = "";
                    this[`scpmk_${i}`] = "";
                    this[`metode_${i}`] = "";
                }

                this.sks = "";
                this.showEdit = 0;

                this.name_delete = "";
                this.nim_delete = "";
                this.kode_rps_delete = "";
                this.mk_delete = "";

                this.tanggal_unlock = "";
                this.ganjil_genap = "";
                this.akademik = "";
                this.akademik_1 = "";
                this.akademik_2 = "";
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
