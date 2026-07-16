document.addEventListener("alpine:init", () => {
    Alpine.store("theme_manager", {
        duration: 12000,
        currentTheme: localStorage.getItem("app-theme") || "blue",
        isAutoPlaying: localStorage.getItem("auto-play-mode") === "true",
        autoPlayInterval: null,
        allThemes: [
            { id: "blue",   color: "#075985" },
            { id: "cyan",   color: "#008B8B" },
            { id: "purple", color: "#7e22ce" },
            { id: "red",    color: "#991b1b" },
            { id: "green",  color: "#15803d" },
            { id: "lime",   color: "#84cc16" },
            { id: "amber",  color: "#b45309" },
            { id: "yellow", color: "#eab308" },
            { id: "pink",   color: "#db2777" },
            { id: "navy",   color: "#1e3a8a" },
            { id: "brown",  color: "#6d4c41" },
            { id: "gray", color: "#64748b" },
            { id: "slate",  color: "#334155" },
            { id: "black",  color: "#171717" },
        ],

        init() {
            this.applyTheme(this.currentTheme);
            document.addEventListener("livewire:navigated", () => {
                this.applyTheme(this.currentTheme);
            });
            window.Livewire.on('wallpaper-added', () => {
                this.refreshWallpapers(); 
            });
        },


        applyTheme(id) {
            document.documentElement.setAttribute("data-theme", id);
        },

        setTheme(id) {
            this.currentTheme = id;
            this.applyTheme(id);
            localStorage.setItem("app-theme", id);
        },

        getThemeColor(offset) {
            let idx = this.allThemes.findIndex(
                (t) => t.id === this.currentTheme,
            );
            let target =
                (idx + offset + this.allThemes.length) % this.allThemes.length;
            return this.allThemes[target].color;
        },

        startInterval() {
            this.stopInterval();
            this.autoPlayInterval = setInterval(() => {
                let idx = this.allThemes.findIndex(
                    (t) => t.id === this.currentTheme,
                );
                this.setTheme(
                    this.allThemes[(idx + 1) % this.allThemes.length].id,
                );
            }, duration);
        },

        stopInterval() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
                this.autoPlayInterval = null;
            }
        },

        toggleAutoPlay() {
            this.isAutoPlaying = !this.isAutoPlaying;
            localStorage.setItem("auto-play-mode", this.isAutoPlaying);
            this.isAutoPlaying ? this.startInterval() : this.stopInterval();
        },

        scrollThemes(direction, refName) {
            const container = document.querySelector(`[x-ref="${refName}"]`);

            if (container) {
                container.scrollBy({
                    left: direction === "left" ? -200 : 200,
                    behavior: "smooth",
                });
            } else {
                console.error(
                    `Container dengan x-ref="${refName}" tidak ditemukan!`,
                );
            }
        },

        activeWallpaper: (() => {
            const saved = localStorage.getItem("user-active-wp");
            if (!saved || saved === "null") return "";
            if (saved === "none") return null;
            return saved;
        })(),
        
        opacity: parseFloat(localStorage.getItem("wp-opacity")) || 0.3,
        brightness: parseFloat(localStorage.getItem("wp-brightness")) || 0.5,

        setWallpaper(path) {
            if (path === null) {
                this.activeWallpaper = null;
                localStorage.setItem("user-active-wp", "none");
            } else {
                this.activeWallpaper = path;
                localStorage.setItem("user-active-wp", path);
            }
        },
        updateSettings() {
            localStorage.setItem("wp-opacity", this.opacity);
            localStorage.setItem("wp-brightness", this.brightness);
        },

        handleUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert("Ukuran gambar terlalu besar! Maksimal 5 MB.");
                return;
            }
            console.log("File siap diupload:", file.name);
        },
    });
});
