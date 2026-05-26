document.addEventListener("alpine:init", () => {
    Alpine.store("ref", {
        isFlyout: false,

        setFlyout(val) {
            this.isFlyout = !!val;
        },

        typeModal_delete: "",
        isEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        ref_delete: "",
        kode_ref_delete: "",

        setEdit(val) {
            this.isEdit = val;
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

        setValueRef(kode, judul, penulis, penerbit, tahun, link) {
            this.kode_ref = kode;
            this.judul = judul;
            this.penulis = penulis;
            this.penerbit = penerbit;
            this.tahun = tahun;
            this.link = link;

            if (kode) {
                const huruf = kode.match(/[a-zA-Z]+/g);
                this.kode_ref_1 = huruf ? huruf[0] : "";
                const angka = kode.match(/\d+/g);
                this.kode_ref_2 = angka ? angka[0] : "";
            } else {
                this.kode_ref_1 = "";
                this.kode_ref_2 = "";
            }
        },

        setDeleteRef(namaRef, kodeRefDelete, forceDelete) {
            this.ref_delete = namaRef;
            this.kode_ref_delete = kodeRefDelete;
            this.isForceDelete = forceDelete;
        },

        reset() {
            this.typeModal_delete = "",
            this.isEdit = 0,
            this.isForceDelete = 0,
            this.colorIcon = "",

            this.kode_ref = "",
            this.kode_ref_1 = "",
            this.kode_ref_2 = "",

            this.judul = ""
            this.penulis = ""
            this.penerbit = ""
            this.tahun = ""
            this.link = ""
        },
    });
});
