document.addEventListener("alpine:init", () => {
    Alpine.store("nilai", {
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",
        colorIconBg: "",

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

        rps_id_show: "",

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

        setDeleteNilai(name, nim, rps, mk, forceDelete) {
            this.name_delete = name;
            this.nim_delete = nim;
            this.kode_rps_delete = rps;
            this.mk_delete = mk;
            this.isForceDelete = forceDelete;
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },
        resetShow() {
            this.rps_id_show = "";
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
            }
            if (isAdd == 0) {
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
                this.colorIconBg = "";
            }
        },
    });
});
