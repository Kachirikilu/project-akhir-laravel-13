document.addEventListener("alpine:init", () => {
    Alpine.store("theme_manager", {
        currentTheme: localStorage.getItem("app-theme") || "blue",
        isAutoPlaying: localStorage.getItem("auto-play-mode") === "true",
        autoPlayInterval: null,
        allThemes: [
            { id: "blue", color: "#075985" },
            { id: "purple", color: "#7e22ce" },
            { id: "red", color: "#991b1b" },
            { id: "green", color: "#059669" },
            { id: "lime", color: "#84cc16" },
            { id: "amber", color: "#b45309" },
            { id: "yellow", color: "#b45309" },
            { id: "yellow", color: "#eab308" },
            { id: "pink", color: "#db2777" },
            { id: "navy", color: "#475569" },
            { id: "brown", color: "#5d4037" },
            { id: "black", color: "#000000" },
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
            }, 12000);
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

        wallpapers: [
            { id: 'none', path: null, isCustom: false },
            { id: 'default-1', path: '/wallpaper/my-alya.png', isCustom: false },
            { id: 'default-2', path: '/wallpaper/my-masha.png', isCustom: false },
            { id: 'default-3', path: '/wallpaper/my-waguri.png', isCustom: false }
        ],
        activeWallpaper:
            localStorage.getItem("user-active-wp") || "/wallpaper/my-alya.png",
        opacity: parseFloat(localStorage.getItem("wp-opacity")) || 0.3,
        brightness: parseFloat(localStorage.getItem("wp-brightness")) || 0.5,

        setWallpaper(path) {
            this.activeWallpaper = path;
            localStorage.setItem("user-active-wp", path);
        },

        updateSettings() {
            localStorage.setItem("wp-opacity", this.opacity);
            localStorage.setItem("wp-brightness", this.brightness);
        },

        // setWallpaper(path) {
        //     this.activeWallpaper = path;
        //     localStorage.setItem("user-active-wp", path);
        //     document.documentElement.style.setProperty(
        //         "--user-bg-image",
        //         `url(${path})`,
        //     );
        // },
        handleUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert("Ukuran gambar terlalu besar! Maksimal 5 MB.");
                return;
            }
            console.log("File siap diupload:", file.name);
        },

        // refreshWallpapers() {
        // }
    });
});
