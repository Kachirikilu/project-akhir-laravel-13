document.addEventListener("alpine:init", () => {
    Alpine.store("ref", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "text-[var(--contrast-second-text)]",
        colorIconBg: "bg-[var(--contrast-second-text)]/40",

        ref_delete: "",
        kode_ref_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val) {
            this.colorIcon = val;
        },

        kode_ref: "",
        kode_ref_1: "",
        kode_ref_2: "",

        judul: "",
        penulis: "",
        penerbit: "",
        tahun: "",
        link: "",
        citation: "",


        setValueRef(kode, judul, penulis, penerbit, tahun, link) {
            this.kode_ref = kode;
            this.judul = judul;
            this.penulis = penulis;
            this.penerbit = penerbit;
            this.tahun = tahun;
            this.link = link;

            if (kode) {
                const mutu = kode.match(/[a-zA-Z]+/g);
                this.kode_ref_1 = mutu ? mutu[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_ref_2 = angka ? angka[0] : "";
            } else {
                this.kode_ref_1 = "";
                this.kode_ref_2 = "";
            }
        },

        getDataRef() {
            return {
                kode_ref: this.kode_ref,
                judul: this.judul,
                penulis: this.penulis,
                penerbit: this.penerbit,
                tahun: this.tahun,
                link: this.link,
                kode_ref: this.kode_ref,
                kode_ref_1: this.kode_ref_1,
                kode_ref_2: this.kode_ref_2,
            };
        },

        setDeleteRef(namaRef, kodeRefDelete, forceDelete) {
            this.ref_delete = namaRef;
            this.kode_ref_delete = kodeRefDelete;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.kode_ref = "";
                this.kode_ref_1 = "";
                this.kode_ref_2 = "";

                this.judul = "";
                this.penulis = "";
                this.penerbit = "";
                this.tahun = "";
                this.link = "";
                this.citation = "";
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
