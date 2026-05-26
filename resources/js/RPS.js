document.addEventListener("alpine:init", () => {
    Alpine.store("rps", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        rps_delete: "",
        kode_rps_delete: "",

        setEdit(val) {
            this.isEdit = val;
        },
        setColor(val) {
            this.colorIcon = val;
        },

        rps_id_show: "",

        id: "",
        nama_rps: "",

        deskripsi: "",
        digit_akademik: "",

        mk_id: "",
        nama_mk_search: "",
        mk_items: "",

        akademik: "",
        akademik_1: "",
        akademik_2: "",
        is_draf: "",

        count_scpmk: 0,

        bobot_uts: 0,
        bobot_uas: 0,
        total_bobot: 0,

        setCountSCPMK(val) {
            this.count_scpmk = val;

            if (val < 14 && (this.is_draf === 0 || this.is_draf === 1)) {
                this.is_draf = 1;
            } else if (val < 14 && this.is_draf === "") {
                this.is_draf = "";
            }
        },

        ref_cpmk: [],
        ref_scpmk: [],
        cpl_cpmk: [],

        // Di dalam Alpine.store('rps')
        update(allSubItems) {
            if (!allSubItems || allSubItems.length === 0) {
                this.ref_cpmk = [];
                this.ref_scpmk = [];
                this.cpl_cpmk = [];
                this.count_scpmk = 0;
                return;
            }

            this.ref_cpmk = [];
            this.ref_scpmk = [];
            this.cpl_cpmk = [];

            allSubItems.forEach((item) => {
                if (item.scpmk) {
                    let rawSubRefs = item.scpmk.flatMap((sub) => sub.ref || []);
                    const combinedSubRef = [...this.ref_scpmk, ...rawSubRefs];
                    this.ref_scpmk = Array.from(
                        new Map(combinedSubRef.map((i) => [i.id, i])).values(),
                    );
                }
                if (item.cpl) {
                    const combinedCPL = [
                        ...this.cpl_cpmk,
                        ...(Array.isArray(item.cpl) ? item.cpl : []),
                    ];
                    this.cpl_cpmk = Array.from(
                        new Map(combinedCPL.map((i) => [i.id, i])).values(),
                    );
                }
                if (item.ref) {
                    const combinedRef = [
                        ...this.ref_cpmk,
                        ...(Array.isArray(item.ref) ? item.ref : []),
                    ];
                    this.ref_cpmk = Array.from(
                        new Map(combinedRef.map((i) => [i.id, i])).values(),
                    );
                }
            });
        },

        setValueRPS(
            kodeBlok,
            deskripsi,
            idMK,
            kodeMK,
            namaMK,
            tahunAkademik,
            isDraf,
            countScpmk,
            bobotUTS,
            bobotUAS,
            totalBobot,
            kodeSemester,
        ) {
            this.digit_akademik = kodeBlok;
            this.deskripsi = deskripsi;

            this.mk_id = idMK;
            this.nama_mk_search = namaMK;
            this.mk_items = {
                id: idMK,
                kode: kodeMK,
                slot1: namaMK,
                slot2: kodeSemester,
            };

            this.akademik = tahunAkademik;

            if (tahunAkademik && tahunAkademik.includes("/")) {
                let parts = tahunAkademik.split("/");
                this.akademik_1 = parts[0];
                this.akademik_2 = parts[1];
            } else {
                this.akademik_1 = "";
                this.akademik_2 = "";
            }

            if (countScpmk < 14) {
                this.is_draf = 1;
            } else {
                this.is_draf = isDraf;
            }
            this.count_scpmk = countScpmk;

            this.bobot_uts = bobotUTS;
            this.bobot_uas = bobotUAS;
            this.total_bobot = totalBobot;
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteRPS(namaRPS, kodeCPLDelete, forceDelete) {
            this.rps_delete = namaRPS;
            this.kode_rps_delete = kodeCPLDelete;
            this.isForceDelete = forceDelete;
        },

        // resetSelect() {
        //     this.kode_blok = "";
        //     this.semester = "";
        //     this.tipe_sks = "";
        //     this.is_wajib = "";
        // },

        resetShow() {
            this.rps_id_show = "";
        },

        reset() {
            this.typeModal_delete = "";
            this.isEdit = 0;
            this.isForceDelete = 0;
            this.colorIcon = "";

            this.deskripsi = "";

            this.mk_id = "";
            this.nama_mk_search = "";
            this.mk_items = "";

            this.digit_akademik = "";
            this.akademik = "";
            this.akademik_1 = "";
            this.akademik_2 = "";

            this.is_draf = "";
        },
        resetShow() {
            this.typeModal_delete = "";
            this.colorIcon = "";

            this.id = "";
            this.nama_rps = "";
        },
    });
});
