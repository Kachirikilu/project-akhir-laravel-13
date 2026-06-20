import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    console.log("VITE_HMR_HOST =", env.VITE_HMR_HOST);

    return {
        plugins: [
            laravel({
                input: [
                    "resources/css/color-template.css",
                    "resources/css/app.css",
                    "resources/js/app.js",
                    "resources/js/User.js",
                    "resources/js/ProgramStudi.js",
                    "resources/js/MataKuliah.js",
                    "resources/js/RPS.js",
                    "resources/js/CPMK.js",
                    "resources/js/SubCPMK.js",
                    "resources/js/CPL.js",
                    "resources/js/Referensi.js",
                    "resources/js/Kelas.js",
                    "resources/js/KelasJadwal.js",
                    "resources/js/KelasSesi.js",
                    "resources/js/Nilai.js",
                    "resources/js/NilaiPeriode.js",
                ],
                refresh: true,
            }),
            tailwindcss(),
        ],

        server: {
            cors: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },

        // server: {
        //     host: '0.0.0.0',
        //     port: 5173,

        //     allowedHosts: [
        //         '.ngrok-free.app',
        //     ],

        //     cors: {
        //         origin: '*',
        //         credentials: true,
        //     },

        //     hmr: {
        //         host: env.VITE_HMR_HOST,
        //         protocol: 'wss',
        //         clientPort: 443,
        //     },
        // },
    };
});