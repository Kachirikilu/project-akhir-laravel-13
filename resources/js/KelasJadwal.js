document.addEventListener("alpine:init", () => {
    Alpine.store("jadwal", {
        isEdit: 0,
        showEdit: 0,
        isForceDelete: 0,
        colorIcon: "",

        label_jadwal_delete: "",
        kode_jadwal_delete: "",

        setEdit(val) {
            this.isEdit = val;
            if (val == 1) {
                this.showEdit = 1;
            }
        },
        setColor(val) {
            this.colorIcon = val;
        },

        search: "",
        perPage: 8,
        sortField: "label_full",
        sortDirection: "asc",
        currentPage: 1,
        totalPages: 1,

        jadwal_id: "",
        kode: "",
        kode_kelas: "",
        label_extra: "",

        rps_id_show: "",

        kode_wilayah: "",
        label_kelas: "",

        password: "",

        hari_pelaksanaan: "",
        jam_mulai: "",
        jam_berakhir: "",

        tanggal_mulai: "",
        tanggal_mulai_fix: "",
        tanggal_berakhir: "",

        kapasitas: "",
        restart_sesi: 0,

        sks_menit: 0,

        setValueJadwal(
            label,
            wilayah,

            hari,
            jamMulai,
            jamBerakhir,
            tanggalMulai,
            tanggalBerakhir,

            kapasitas,
            password,
        ) {
            this.label_kelas = label;
            this.kode_wilayah = wilayah;

            this.hari_pelaksanaan = hari;
            this.jam_mulai = jamMulai?.slice(0, 5) || "";
            this.jam_berakhir = jamBerakhir?.slice(0, 5) || "";

            this.tanggal_mulai_fix = toWeekFormat(tanggalMulai);
            // this.tanggal_berakhir = toWeekFormat(tanggalBerakhir);

            this.kapasitas = kapasitas;
            this.password = password;
        },

        setValueJoinJadwal(id, kode, kodeKelas, labelExtra) {
            this.jadwal_id = id;
            this.kode = kode;
            this.kode_kelas = kodeKelas;
            this.label_extra = labelExtra;
        },

        setShowRPS(idRPS) {
            this.resetShow();
            this.rps_id_show = idRPS;
        },

        setDeleteJadwal(labelJadwal, kodeJadwalDelete, forceDelete) {
            this.label_jadwal_delete = labelJadwal;
            this.kode_jadwal_delete = kodeJadwalDelete;
            this.isForceDelete = forceDelete;
        },

        resetShow() {
            this.rps_id_show = "";
        },

        reset(isAdd = 0) {
            if ((this.showEdit == 1 && isAdd == 1) || isAdd == 0) {
                this.restart_sesi = 0;

                this.jadwal_id = "";
                this.kode = "";
                this.kode_kelas = "";
                this.label_extra = "";

                this.kode_wilayah = "";
                this.label_kelas = "";

                this.password = "";

                this.hari_pelaksanaan = "";

                this.jam_mulai = "";
                this.jam_berakhir = "";

                this.tanggal_mulai = "";
                this.tanggal_mulai_fix = "";
                this.tanggal_berakhir = "";

                this.kapasitas = "";

                this.label_jadwal_delete = "";
                this.kode_jadwal_delete = "";

                for (let i = 1; i <= 16; i++) {
                    this[`sesi_${i}`] = "";
                    this[`base_sesi_${i}`] = "";
                }
                this.showEdit = 0;
            }
            if (isAdd == 0) {
                this.isEdit = 0;
                this.isForceDelete = 0;
                this.colorIcon = "";
            }
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

            // =========================================
            // AUTO TANGGAL BERAKHIR (+6 BULAN)
            // =========================================
            Alpine.effect(() => {
                const value = this.tanggal_mulai;

                if (!value) {
                    this.tanggal_berakhir = "";
                    return;
                }

                const [yearPart, weekPart] = value.split("-W");

                const year = parseInt(yearPart);

                const week = parseInt(weekPart);

                const date = new Date(year, 0, 1);

                date.setDate(date.getDate() + (week - 1) * 7);

                date.setMonth(date.getMonth() + 6);

                const target = new Date(date);

                const dayNum = (target.getDay() + 6) % 7;

                target.setDate(target.getDate() - dayNum + 3);

                const firstThursday = new Date(target.getFullYear(), 0, 4);

                const weekNumber =
                    1 +
                    Math.round(
                        ((target - firstThursday) / 86400000 -
                            3 +
                            ((firstThursday.getDay() + 6) % 7)) /
                            7,
                    );

                const finalYear = target.getFullYear();

                this.tanggal_berakhir = `${finalYear}-W${String(
                    weekNumber,
                ).padStart(2, "0")}`;
            });

            // =========================================
            // GENERATE BASE SESI
            // =========================================
            Alpine.effect(() => {
                const weekValue = this.tanggal_mulai;

                const hari = this.hari_pelaksanaan;

                if (!weekValue || !hari) {
                    return;
                }

                const hariMap = {
                    Senin: 1,
                    Selasa: 2,
                    Rabu: 3,
                    Kamis: 4,
                    Jumat: 5,
                    Sabtu: 6,
                    Minggu: 0,
                };

                const targetDay = hariMap[hari];

                const [yearPart, weekPart] = weekValue.split("-W");

                const year = parseInt(yearPart);

                const week = parseInt(weekPart);

                // ISO WEEK START
                const simple = new Date(year, 0, 1 + (week - 1) * 7);

                const dayOfWeek = simple.getDay() || 7;

                const isoWeekStart = new Date(simple);

                if (dayOfWeek <= 4) {
                    isoWeekStart.setDate(simple.getDate() - dayOfWeek + 1);
                } else {
                    isoWeekStart.setDate(simple.getDate() + 8 - dayOfWeek);
                }

                // CARI HARI SESUAI
                const firstDay = new Date(isoWeekStart);

                while (firstDay.getDay() !== targetDay) {
                    firstDay.setDate(firstDay.getDate() + 1);
                }

                // BASE DATE
                const baseDate = new Date(firstDay);

                // GENERATE BASE
                for (let i = 1; i <= 16; i++) {
                    const sesiDate = new Date(baseDate);

                    sesiDate.setDate(sesiDate.getDate() + (i - 1) * 7);

                    const y = sesiDate.getFullYear();

                    const m = String(sesiDate.getMonth() + 1).padStart(2, "0");

                    const d = String(sesiDate.getDate()).padStart(2, "0");

                    const formatted = `${y}-${m}-${d}`;

                    // SIMPAN BASE
                    this[`base_sesi_${i}`] = formatted;

                    // ISI JIKA MASIH KOSONG
                    this[`sesi_${i}`] = formatted;
                }
            });

            // =========================================
            // SHIFT BERDASARKAN DELTA MINGGU
            // =========================================
            for (let i = 1; i <= 15; i++) {
                Alpine.effect(() => {
                    const current = this[`sesi_${i}`];

                    const base = this[`base_sesi_${i}`];

                    if (!current || !base) {
                        return;
                    }

                    const currentDate = new Date(current);

                    const baseDate = new Date(base);

                    const diffDays = Math.floor(
                        (currentDate - baseDate) / (1000 * 60 * 60 * 24),
                    );

                    // HITUNG GAP 7 HARI
                    const shiftWeek = Math.floor(diffDays / 7);

                    // UPDATE SESI BERIKUT
                    for (let j = i + 1; j <= 16; j++) {
                        const nextBase = this[`base_sesi_${j}`];

                        if (!nextBase) {
                            continue;
                        }

                        const nextDate = new Date(nextBase);

                        nextDate.setDate(nextDate.getDate() + shiftWeek * 7);

                        const y = nextDate.getFullYear();

                        const m = String(nextDate.getMonth() + 1).padStart(
                            2,
                            "0",
                        );

                        const d = String(nextDate.getDate()).padStart(2, "0");

                        this[`sesi_${j}`] = `${y}-${m}-${d}`;
                    }
                });
            }
        },
        isWeekend(dateString) {
            const day = new Date(dateString).getDay();

            return day === 0 || day === 6;
        },

        formatHari(dateString) {
            if (!dateString) return "";

            const date = new Date(dateString);

            return date.toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        },
    });

    window.addEventListener("fill-modal-jadwal", (event) => {
        const j = event.detail.jadwal;
        Alpine.store("jadwal").setValueJadwal(
            j.kode_jadwal || j.kode_kelas,
            j.nama_jadwal || j.nama_kelas,
            j.deskripsi || j.deskripsi_kelas,
            j.pr_id,
            j.pr_rel?.kode_pr || j.kode_pr,
            j.pr_rel?.prodi || j.prodi,
            j.pr_rel?.departemen_dp || j.departemen,
            j.pr_rel?.fakultas_fk || j.fakultas,
            j.rps_id,
            j.rps_rel?.kode_rps || j.kode_rps,
            j.rps_rel?.rps || j.rps,
            j.rps_rel?.sks_full || j.sks_full,
            j.wajib_text || j.wajib_text,
            j.rps_rel?.draf_full || j.draf_full,
        );
    });

    function toWeekFormat(dateString) {
        if (!dateString) return "";

        const date = new Date(dateString);
        const target = new Date(date.valueOf());
        const dayNum = (date.getDay() + 6) % 7;

        target.setDate(target.getDate() - dayNum + 3);
        const firstThursday = new Date(target.getFullYear(), 0, 4);
        const weekNumber =
            1 +
            Math.round(
                ((target - firstThursday) / 86400000 -
                    3 +
                    ((firstThursday.getDay() + 6) % 7)) /
                    7,
            );

        return `${target.getFullYear()}-W${String(weekNumber).padStart(
            2,
            "0",
        )}`;
    }
});
