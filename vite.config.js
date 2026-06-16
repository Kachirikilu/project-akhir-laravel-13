import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/color-template.css', 'resources/css/app.css', 'resources/js/app.js',
                'resources/js/User.js', 'resources/js/ProgramStudi.js', 'resources/js/MataKuliah.js',
                'resources/js/RPS.js', 'resources/js/CPMK.js', 'resources/js/SubCPMK.js', 'resources/js/CPL.js', 'resources/js/Referensi.js',
                'resources/js/Kelas.js', 'resources/js/Nilai.js', 'resources/js/NilaiPeriode.js'],
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
});
