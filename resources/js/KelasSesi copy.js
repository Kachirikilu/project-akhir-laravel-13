document.addEventListener("alpine:init", () => {
    Alpine.store("sesi", {
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "text-[var(--contrast-second-text)]",
        colorIconBg: "bg-[var(--contrast-second-text)]/40",

        nama_sesi_delete: "",
        kode_sesi_delete: "",

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

        setPerPage(value) {
            const next = Number(value) || 8;
            this.perPage = next;
            this.currentPage = 1;
            this.fromItem = 1;
            this.toItem = next;
        },

initData(items) {
            this.rawItems = items || [];
        },

        // Method langsung di store
        isCardVisible(id) {
            if (!Array.isArray(this.rawItems) || this.rawItems.length === 0) return false;

            const cp = Number(this.currentPage) || 1;
            const pp = Number(this.perPage) || 8;
            const start = (cp - 1) * pp;
            const end = start + pp;

            const list = this.filteredAndSortedIds || [];
            return list.slice(start, end).some(item => Number(item?.id) === Number(id));
        },
        get filteredAndSortedIds() {
            if (!Array.isArray(this.rawItems) || this.rawItems.length === 0)
                return [];

            let query = (Alpine.store("sesi")?.search || "")
                .toLowerCase()
                .trim();
            let cleanQuery = query.replace(/[^a-z0-9]/g, "");
            let dotQuery = query.replace(",", ".");
            let normalizedQuery = query.replace(/[\u2013\u2014]/g, "-");

            let filtered = this.rawItems.filter((item) => {
                if (!item) return false;
                if (!query) return true;

                let metode = String(item.metode || "").toLowerCase();
                let tugas = String(item.tugas || "").toLowerCase();
                let kodeScpmk = String(item.kode_scpmk || "").toLowerCase();
                let searchScpmk = String(
                    item.searchKodeSCPMK || "",
                ).toLowerCase();
                let kodeCpmk = String(item.kode_cpmk || "").toLowerCase();
                let searchCpmk = String(
                    item.searchKodeCPMK || "",
                ).toLowerCase();

                let hari = String(item.hari || "").toLowerCase();
                let hariJam = String(item.hari_jam || "")
                    .toLowerCase()
                    .replace(/[\u2013\u2014]/g, "-");
                let hariTanggal = String(item.hari_tanggal || "").toLowerCase();

                if (metode.includes(query) || tugas.includes(query))
                    return true;
                if (
                    kodeScpmk.includes(query) ||
                    (cleanQuery && searchScpmk.includes(cleanQuery))
                )
                    return true;
                if (
                    kodeCpmk.includes(query) ||
                    (cleanQuery && searchCpmk.includes(cleanQuery))
                )
                    return true;
                if (
                    item.searchPertemuan?.some((pText) =>
                        String(pText).toLowerCase().includes(query),
                    )
                )
                    return true;

                if (hari.includes(query) || hariTanggal.includes(query))
                    return true;
                if (hariJam.includes(normalizedQuery)) return true;

                if (
                    item.bobot?.some((bText) => {
                        let text = String(bText).toLowerCase();
                        return text.includes(query) || text.includes(dotQuery);
                    })
                )
                    return true;

                return false;
            });

            let field = Alpine.store("sesi")?.sortField || "pertemuan_ke";
            let direction =
                (Alpine.store("sesi")?.sortDirection || "asc") === "desc"
                    ? -1
                    : 1;

            const getMethodPriority = (value) => {
                const text = String(value ?? "")
                    .trim()
                    .toLowerCase();
                if (text.includes("uas")) return 3;
                if (text.includes("uts")) return 2;
                if (text.includes("teori")) return 1;
                if (text.includes("praktik")) return 0;
                if (text.includes("tugas")) return -1;
                return -2;
            };

            const parseNumber = (value) => {
                if (value === null || value === undefined || value === "")
                    return 0;
                const normalized = String(value)
                    .trim()
                    .replace(/[^0-9,.-]/g, "")
                    .replace(",", ".");
                const num = Number(normalized);
                return Number.isFinite(num) ? num : 0;
            };

            const sortedFiltered = [...filtered];

            if (field) {
                sortedFiltered.sort((a, b) => {
                    const fallbackOrder = () =>
                        Number(a.dbIndex ?? 0) - Number(b.dbIndex ?? 0);

                    if (field === "pertemuan_ke" || field === "total_absensi") {
                        const numA = Number(
                            field === "pertemuan_ke"
                                ? (a.pertemuan_ke ?? 0)
                                : (a.total_absensi ?? 0),
                        );
                        const numB = Number(
                            field === "pertemuan_ke"
                                ? (b.pertemuan_ke ?? 0)
                                : (b.total_absensi ?? 0),
                        );
                        if (numA !== numB) return (numA - numB) * direction;
                        return fallbackOrder();
                    }

                    if (field === "metode") {
                        const rankA = getMethodPriority(a.metode);
                        const rankB = getMethodPriority(b.metode);
                        if (rankA !== rankB) return (rankA - rankB) * direction;

                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }

                    if (field === "bobot") {
                        const safeA = parseNumber(a.bobot_normalisasi);
                        const safeB = parseNumber(b.bobot_normalisasi);
                        if (safeA !== safeB) return (safeA - safeB) * direction;

                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }

                    const valA = a[field];
                    const valB = b[field];
                    const textA = String(valA ?? "")
                        .trim()
                        .toLowerCase();
                    const textB = String(valB ?? "")
                        .trim()
                        .toLowerCase();
                    const result = textA.localeCompare(textB, "id", {
                        numeric: true,
                        sensitivity: "base",
                    });

                    return result !== 0 ? result * direction : fallbackOrder();
                });
            } else {
                sortedFiltered.sort(
                    (a, b) => Number(a.dbIndex ?? 0) - Number(b.dbIndex ?? 0),
                );
            }

            return sortedFiltered;
        },

        // FUNGSI CEK KARTU YANG AMAN DARI ERROR UNDEFINED
        isCardVisible(id) {
            const list = this.filteredAndSortedIds;
            if (!list.length) return false;

            const cp = Number(Alpine.store("sesi")?.currentPage ?? 1) || 1;
            const pp = Number(Alpine.store("sesi")?.perPage ?? 8) || 8;
            const start = (cp - 1) * pp;
            const end = start + pp;

            return list
                .slice(start, end)
                .some((item) => Number(item?.id) === Number(id));
        },

        getCardOrder(id) {
            return this.filteredAndSortedIds.findIndex(
                (entry) => Number(entry?.id) === Number(id),
            );
        },

        search: "",
        perPage: 8,
        sortField: "pertemuan_ke",
        sortDirection: "asc",
        currentPage: 1,
        totalPages: 1,

        fromItem: 1,
        toItem: 8,
        totalItems: 0,

        sesi_id: "",
        pertemuan_ke: "",
        absen: "",
        keterangan: "",

        jam_mulai: "",
        jam_berakhir: "",

        pertemuan_ke: "",
        pertemuan_ke_name: "",

        tanggal: "",

        deskripsi: "",
        materi: "",
        metodologi: "",
        indikator: "",
        deksripsi_tugas: "",
        waktu_tugas: "",
        waktu_mandiri: "",

        nama: "",
        nim: "",

        mhs_poin_absensi: 0,
        mhs_masuk: 0,
        mhs_dispensasi: 0,
        mhs_terlambat: 0,
        mhs_izin: 0,
        mhs_sakit: 0,
        mhs_tidak_masuk: 0,
        mhs_nilai_akhir: 0,
        mhs_nilai_index: 0,
        mhs_nilai_mutu: "E",

        w_pelaksaan: "",
        w_berakhir: "",
        w_telat: "",
        w_dispensasi: "",

        kode_jadwal: "",

        sks: "",
        sks_menit: 0,
        sent: 1,

        setValueSesi(
            jamMulai,
            jamBerakhir,
            pertemuan,
            tanggal,

            // deskripsi,
            // materi,
            // metodologi,
            // indikator,
            // tugas,
            // wTugas,
            // wMandiri,
            // sks,

            sent,
        ) {
            this.jam_mulai = jamMulai?.slice(0, 5) || "";
            this.jam_berakhir = jamBerakhir?.slice(0, 5) || "";

            this.pertemuan_ke = pertemuan;
            this.pertemuan_ke_name = "Pertemuan " + pertemuan;
            this.tanggal = tanggal;

            // this.deskripsi = deskripsi;
            // this.materi = materi;
            // this.metodologi = metodologi;
            // this.indikator = indikator;
            // this.deskripsi_tugas = tugas;
            // this.waktu_tugas = wTugas;
            // this.waktu_mandiri = wMandiri;
            // this.sks = sks;

            this.sent = sent;
        },
        setValueAbsenSesi(
            sesiId,
            kodeJadwal,
            pertemuanKe,
            scpmk,
            keterangan,
            wPelaksanaan,
            wBerakhir,
            wTelat,
            wDispensasi,
        ) {
            this.sesi_id = sesiId;
            this.kode_jadwal = kodeJadwal;
            this.pertemuan_ke = pertemuanKe;
            this.kode_scpmk = scpmk;
            this.keterangan = keterangan;

            this.w_pelaksanaan = wPelaksanaan;
            this.w_berakhir = wBerakhir;
            this.w_telat = wTelat;
            this.w_dispensasi = wDispensasi;

            const opsiTersedia = this.getOpsiStatus();
            if (opsiTersedia.length > 0) {
                this.absen = opsiTersedia[0].label;
            }
        },

        getDataSesi() {
            return {
                jam_mulai: this.jam_mulai,
                jam_berakhir: this.jam_berakhir,
                pertemuan_ke: this.pertemuan_ke,
                pertemuan_ke_name: this.pertemuan_ke_name,
                tanggal: this.tanggal,
                // deskripsi: this.deskripsi,
                // materi: this.materi,
                // metodologi: this.metodologi,
                // indikator: this.indikator,
                // deskripsi_tugas: this.deskripsi_tugas,
                // waktu_tugas: this.waktu_tugas,
                // waktu_mandiri: this.waktu_mandiri,
                sks: this.sks,
                sent: this.sent,
            };
        },
        getDataAbsensiSesi() {
            return {
                sesi_id: this.sesi_id,
                absen: this.absen,
                keterangan: this.keterangan,
                // pertemuan_ke: this.pertemuan_ke,
                // kode_scpmk: this.kode_scpmk,
                // w_pelaksanaan: this.w_pelaksanaan,
                // w_berakhir: this.w_berakhir,
                // w_telat: this.w_telat,
                // w_dispensasi: this.w_dispensasi,
            };
        },

        getWaktuLokal() {
            let d = new Date();
            let tzOffset = d.getTimezoneOffset() * 60000;
            let waktuLokal = new Date(d.getTime() - tzOffset);
            return waktuLokal.toISOString().slice(0, 16);
        },

        getOpsiStatus() {
            const sekarang = this.getWaktuLokal();

            const masterStatus = [
                {
                    label: "Hadir",
                    icon: "check-circle",
                    bg_active:
                        "bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500 ring-emerald-500",
                    icon_active:
                        "bg-emerald-500/10 text-emerald-600 dark:text-emerald-400",
                    icon_default: "text-emerald-500",
                },
                {
                    label: "Terlambat",
                    icon: "clock",
                    bg_active:
                        "bg-amber-50 dark:bg-amber-950/30 border-amber-500 ring-amber-500",
                    icon_active:
                        "bg-amber-500/10 text-amber-600 dark:text-amber-400",
                    icon_default: "text-amber-500",
                },
                {
                    label: "Dispensasi",
                    icon: "shield-check",
                    bg_active:
                        "bg-cyan-50 dark:bg-cyan-950/30 border-cyan-500 ring-cyan-500",
                    icon_active:
                        "bg-cyan-500/10 text-cyan-600 dark:text-cyan-400",
                    icon_default: "text-cyan-500",
                },
                {
                    label: "Izin",
                    icon: "document-text",
                    bg_active:
                        "bg-blue-50 dark:bg-blue-950/30 border-blue-500 ring-blue-500",
                    icon_active:
                        "bg-blue-500/10 text-blue-600 dark:text-blue-400",
                    icon_default: "text-blue-500",
                },
                {
                    label: "Sakit",
                    icon: "heart",
                    bg_active:
                        "bg-rose-50 dark:bg-rose-950/30 border-rose-500 ring-rose-500",
                    icon_active:
                        "bg-rose-500/10 text-rose-600 dark:text-rose-400",
                    icon_default: "text-rose-500",
                },
                {
                    label: "Absen",
                    icon: "x-circle",
                    bg_active:
                        "bg-red-50 dark:bg-red-950/30 border-red-500 ring-red-500",
                    icon_active: "bg-red-500/10 text-red-600 dark:text-red-400",
                    icon_default: "text-red-500",
                },
            ];

            return masterStatus.filter((item) => {
                if (item.label === "Absen") {
                    return true;
                }
                if (["Hadir", "Izin"].includes(item.label)) {
                    return (
                        sekarang >= this.w_pelaksanaan &&
                        sekarang <= this.w_telat
                    );
                }
                if (item.label === "Sakit") {
                    return (
                        sekarang >= this.w_pelaksanaan &&
                        sekarang <= this.w_berakhir
                    );
                }
                if (item.label === "Terlambat") {
                    return (
                        sekarang > this.w_pelaksanaan &&
                        sekarang <= this.w_berakhir
                        // sekarang > this.w_telat && sekarang <= this.w_berakhir
                    );
                }
                if (item.label === "Dispensasi") {
                    return (
                        sekarang > this.w_pelaksanaan &&
                        sekarang <= this.w_dispensasi
                    );
                }
                return false;
            });
        },

        setValueAbsensi(
            name,
            nim,

            poin,
            masuk,
            dispensasi,
            terlambat,
            izin,
            sakit,
            tidakMasuk,

            nilaiAkhir,
            nilaiIndex,
            nilaiMutu,
        ) {
            this.nama = name;
            this.nim = nim;

            this.mhs_poin_absensi = poin;
            this.mhs_masuk = masuk;

            this.mhs_dispensasi = dispensasi;
            this.mhs_terlambat = terlambat;
            this.mhs_izin = izin;
            this.mhs_sakit = sakit;
            this.mhs_tidak_masuk = tidakMasuk;
            this.mhs_nilai_akhir = nilaiAkhir;
            this.mhs_nilai_index = nilaiIndex;
            this.mhs_nilai_mutu = nilaiMutu;
        },

        setDeleteJadwal(namaJadwal, kodeJadwalDelete, forceDelete) {
            this.nama_sesi_delete = namaJadwal;
            this.kode_sesi_delete = kodeJadwalDelete;
            this.isForceDelete = forceDelete;
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.w_pelaksaan = "";
                this.w_berakhir = "";
                this.w_telat = "";
                this.w_dispensasi = "";

                this.kode_jadwal = "";

                this.nama = "";
                this.nim = "";

                this.mhs_poin_absensi = 0;
                this.mhs_masuk = 0;
                this.mhs_dispensasi = 0;
                this.mhs_terlambat = 0;
                this.mhs_izin = 0;
                this.mhs_sakit = 0;
                this.mhs_tidak_masuk = 0;
                this.mhs_nilai_akhir = 0;
                this.mhs_nilai_index = 0;
                this.mhs_nilai_mutu = "E";

                this.sesi_id = "";
                this.pertemuan_ke = "";
                this.kode_scpmk = "";
                ((this.absen = ""),
                    (this.keterangan = ""),
                    (this.jam_mulai = ""));
                this.jam_berakhir = "";

                this.pertemuan_ke_name = "";
                this.tanggal = "";

                // this.deskripsi = "";
                // this.materi = "";
                // this.metodologi = "";
                // this.indikator = "";
                // this.deskripsi_tugas = "";
                // this.waktu_tugas = "";
                // this.waktu_mandiri = "";

                this.sks = "";
                this.sent = 1;
                this.showEdit = 0;
            }
            if (isAdd == 0) {
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "text-[var(--contrast-second-text)]";
                this.colorIconBg = "bg-[var(--contrast-second-text)]/40";
            }
        },

        isFloat(val) {
            if (val === null || val === undefined) return "";

            val = String(val);
            val = val.replace(/,/g, ".");
            val = val.replace(/[^0-9.]/g, "");

            const parts = val.split(".");
            if (parts.length > 2) {
                val = parts[0] + "." + parts.slice(1).join("");
            }

            return val;
        },

        normalizeFloat(val, max = 100, length = 3) {
            val = this.isFloat(val);

            let parts = val.split(".");
            parts[0] = (parts[0] || "").slice(0, length);

            if (parts.length > 1) {
                parts[1] = (parts[1] || "").slice(0, 2);
            }

            val = parts.join(".");

            let num = Number(val);

            if (!isNaN(num)) {
                if (num > max) num = max;
                if (num < 0) num = 0;
                val = num.toString();
            }

            return val;
        },
        init() {
            // =========================================
            // AUTO JAM BERAKHIR
            // =========================================
            Alpine.effect(() => {
                const value = this.jam_mulai;

                if (!value) {
                    this.jam_berakhir = "";
                    return;
                }

                const [hour, minute] = value.split(":").map(Number);

                let totalMinute = hour * 60 + minute;

                totalMinute += Number(this.sks_menit || 0);

                const endHour = String(
                    Math.floor(totalMinute / 60) % 24,
                ).padStart(2, "0");

                const endMinute = String(totalMinute % 60).padStart(2, "0");

                this.jam_berakhir = `${endHour}:${endMinute}`;
            });
        },
    });
});
