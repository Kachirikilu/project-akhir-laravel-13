<div id="glow-bg" aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    <style>
        #glow-bg {
            overflow: hidden;
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .glow {
            position: absolute;
            border-radius: 9999px;
            background: radial-gradient(circle,
                    color-mix(in srgb, var(--focus-color) 75%, transparent) 0%,
                    color-mix(in srgb, var(--focus-color) 40%, transparent) 35%,
                    transparent 75%);
            filter: blur(28px);
            opacity: .5;
            transition: left 8s linear, top 8s linear, opacity 8s linear, width 8s linear, height 8s linear;
            animation: pulseGlow 8s ease-in-out infinite;
        }

        @keyframes pulseGlow {

            0%,
            100% {
                transform: scale(0.8);
            }

            50% {
                transform: scale(1.2);
            }
        }
    </style>

        <div class="glow"></div>
        <div class="glow"></div>
        <div class="glow"></div>

        <div class="glow blur-xl opacity-40"></div>
        <div class="glow blur-3xl opacity-20"></div>

    <svg width="100%" height="100%" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice"
        xmlns="http://www.w3.org/2000/svg">

        {{-- ── BLOB BESAR (Blurred ellipse) ── --}}

        {{-- Blob utama kiri atas --}}
        <ellipse cx="-60" cy="80" rx="420" ry="380" fill="var(--main-color)" opacity="0.10"
            style="filter: blur(80px);" />

        {{-- Blob fokus kanan bawah --}}
        <ellipse cx="1520" cy="820" rx="380" ry="340" fill="var(--focus-color)" opacity="0.10"
            style="filter: blur(80px);" />

        {{-- Blob kecil tengah atas --}}
        <ellipse cx="720" cy="-60" rx="260" ry="180" fill="var(--main-color)" opacity="0.06"
            style="filter: blur(60px);" />

        {{-- Blob kecil kanan tengah --}}
        <ellipse cx="1380" cy="420" rx="200" ry="180" fill="var(--focus-color)" opacity="0.07"
            style="filter: blur(70px);" />


        {{-- ── LINGKARAN OUTLINE ── --}}

        {{-- Lingkaran besar kiri --}}
        <circle cx="120" cy="500" r="280" fill="none" stroke="var(--main-color)" stroke-width="0.8"
            opacity="0.36" />
        <circle cx="120" cy="500" r="200" fill="none" stroke="var(--main-color)" stroke-width="0.4"
            opacity="0.22" />

        {{-- Lingkaran besar kanan atas --}}
        <circle cx="1320" cy="240" r="320" fill="none" stroke="var(--focus-color)" stroke-width="0.8"
            opacity="0.30" />
        <circle cx="1320" cy="240" r="230" fill="none" stroke="var(--focus-color)" stroke-width="0.4"
            opacity="0.20" />

        {{-- Lingkaran tengah bawah --}}
        <circle cx="700" cy="860" r="140" fill="none" stroke="var(--main-color)" stroke-width="0.6"
            opacity="0.12" />


        {{-- ── GARIS MELENGKUNG (Curved lines) ── --}}

        {{-- Arc atas mengalir kiri ke kanan --}}
        <path d="M -80 280 Q 360 60 720 200 T 1520 120" fill="none" stroke="var(--focus-color)" stroke-width="0.7"
            opacity="0.32" stroke-dasharray="8 14" />
        <path d="M -80 340 Q 380 110 740 250 T 1520 180" fill="none" stroke="var(--focus-color)" stroke-width="0.4"
            opacity="0.20" stroke-dasharray="5 18" />

        {{-- Arc bawah --}}
        <path d="M -60 680 Q 460 540 840 640 T 1520 580" fill="none" stroke="var(--main-color)" stroke-width="0.6"
            opacity="0.13" stroke-dasharray="6 16" />

        {{-- Arc pojok kiri bawah --}}
        <path d="M 0 900 Q 160 680 340 740" fill="none" stroke="var(--main-color)" stroke-width="1.2"
            opacity="0.14" />
        <path d="M 0 900 Q 200 640 400 710" fill="none" stroke="var(--main-color)" stroke-width="0.5"
            opacity="0.18" />

        {{-- Arc pojok kanan atas --}}
        <path d="M 1440 0 Q 1260 200 1080 120" fill="none" stroke="var(--focus-color)" stroke-width="1.2"
            opacity="0.14" />
        <path d="M 1440 0 Q 1220 220 1020 140" fill="none" stroke="var(--focus-color)" stroke-width="0.5"
            opacity="0.18" />


        {{-- ── GRID DOT ── --}}

        {{-- Dot grid kanan atas (7×8 titik, gap 22px) --}}
        <g opacity="0.14" fill="var(--focus-color)">
            <g transform="translate(980, 40)">
                {{-- row 0 --}}
                <circle cx="0" cy="0" r="1.6" />
                <circle cx="22" cy="0" r="1.6" />
                <circle cx="44" cy="0" r="1.6" />
                <circle cx="66" cy="0" r="1.6" />
                <circle cx="88" cy="0" r="1.6" />
                <circle cx="110" cy="0" r="1.6" />
                <circle cx="132" cy="0" r="1.6" />
                {{-- row 1 --}}
                <circle cx="0" cy="22" r="1.6" />
                <circle cx="22" cy="22" r="1.6" />
                <circle cx="44" cy="22" r="1.6" />
                <circle cx="66" cy="22" r="1.6" />
                <circle cx="88" cy="22" r="1.6" />
                <circle cx="110" cy="22" r="1.6" />
                <circle cx="132" cy="22" r="1.6" />
                {{-- row 2 --}}
                <circle cx="0" cy="44" r="1.6" />
                <circle cx="22" cy="44" r="1.6" />
                <circle cx="44" cy="44" r="1.6" />
                <circle cx="66" cy="44" r="1.6" />
                <circle cx="88" cy="44" r="1.6" />
                <circle cx="110" cy="44" r="1.6" />
                <circle cx="132" cy="44" r="1.6" />
                {{-- row 3 --}}
                <circle cx="0" cy="66" r="1.6" />
                <circle cx="22" cy="66" r="1.6" />
                <circle cx="44" cy="66" r="1.6" />
                <circle cx="66" cy="66" r="1.6" />
                <circle cx="88" cy="66" r="1.6" />
                <circle cx="110" cy="66" r="1.6" />
                <circle cx="132" cy="66" r="1.6" />
                {{-- row 4 --}}
                <circle cx="0" cy="88" r="1.6" />
                <circle cx="22" cy="88" r="1.6" />
                <circle cx="44" cy="88" r="1.6" />
                <circle cx="66" cy="88" r="1.6" />
                <circle cx="88" cy="88" r="1.6" />
                <circle cx="110" cy="88" r="1.6" />
                <circle cx="132" cy="88" r="1.6" />
                {{-- row 5 --}}
                <circle cx="0" cy="110" r="1.6" />
                <circle cx="22" cy="110" r="1.6" />
                <circle cx="44" cy="110" r="1.6" />
                <circle cx="66" cy="110" r="1.6" />
                <circle cx="88" cy="110" r="1.6" />
                <circle cx="110" cy="110" r="1.6" />
                <circle cx="132" cy="110" r="1.6" />
                {{-- row 6 --}}
                <circle cx="0" cy="132" r="1.6" />
                <circle cx="22" cy="132" r="1.6" />
                <circle cx="44" cy="132" r="1.6" />
                <circle cx="66" cy="132" r="1.6" />
                <circle cx="88" cy="132" r="1.6" />
                <circle cx="110" cy="132" r="1.6" />
                <circle cx="132" cy="132" r="1.6" />
                {{-- row 7 --}}
                <circle cx="0" cy="154" r="1.6" />
                <circle cx="22" cy="154" r="1.6" />
                <circle cx="44" cy="154" r="1.6" />
                <circle cx="66" cy="154" r="1.6" />
                <circle cx="88" cy="154" r="1.6" />
                <circle cx="110" cy="154" r="1.6" />
                <circle cx="132" cy="154" r="1.6" />
            </g>
        </g>

        {{-- Dot grid kiri bawah (6×6 titik) --}}
        <g opacity="0.22" fill="var(--main-color)">
            <g transform="translate(60, 680)">
                <circle cx="0" cy="0" r="1.6" />
                <circle cx="22" cy="0" r="1.6" />
                <circle cx="44" cy="0" r="1.6" />
                <circle cx="66" cy="0" r="1.6" />
                <circle cx="88" cy="0" r="1.6" />
                <circle cx="110" cy="0" r="1.6" />
                <circle cx="0" cy="22" r="1.6" />
                <circle cx="22" cy="22" r="1.6" />
                <circle cx="44" cy="22" r="1.6" />
                <circle cx="66" cy="22" r="1.6" />
                <circle cx="88" cy="22" r="1.6" />
                <circle cx="110" cy="22" r="1.6" />
                <circle cx="0" cy="44" r="1.6" />
                <circle cx="22" cy="44" r="1.6" />
                <circle cx="44" cy="44" r="1.6" />
                <circle cx="66" cy="44" r="1.6" />
                <circle cx="88" cy="44" r="1.6" />
                <circle cx="110" cy="44" r="1.6" />
                <circle cx="0" cy="66" r="1.6" />
                <circle cx="22" cy="66" r="1.6" />
                <circle cx="44" cy="66" r="1.6" />
                <circle cx="66" cy="66" r="1.6" />
                <circle cx="88" cy="66" r="1.6" />
                <circle cx="110" cy="66" r="1.6" />
                <circle cx="0" cy="88" r="1.6" />
                <circle cx="22" cy="88" r="1.6" />
                <circle cx="44" cy="88" r="1.6" />
                <circle cx="66" cy="88" r="1.6" />
                <circle cx="88" cy="88" r="1.6" />
                <circle cx="110" cy="88" r="1.6" />
                <circle cx="0" cy="110" r="1.6" />
                <circle cx="22" cy="110" r="1.6" />
                <circle cx="44" cy="110" r="1.6" />
                <circle cx="66" cy="110" r="1.6" />
                <circle cx="88" cy="110" r="1.6" />
                <circle cx="110" cy="110" r="1.6" />
            </g>
        </g>


        {{-- ── BUBBLE OUTLINE (melayang) ── --}}

        <circle cx="460" cy="160" r="7" fill="none" stroke="var(--focus-color)" stroke-width="1.0"
            opacity="0.22" />
        <circle cx="860" cy="90" r="10" fill="none" stroke="var(--main-color)" stroke-width="1.0"
            opacity="0.36" />
        <circle cx="1060" cy="460" r="8" fill="none" stroke="var(--focus-color)" stroke-width="0.8"
            opacity="0.32" />
        <circle cx="300" cy="340" r="5" fill="none" stroke="var(--main-color)" stroke-width="0.8"
            opacity="0.36" />
        <circle cx="1240" cy="640" r="12" fill="none" stroke="var(--focus-color)" stroke-width="0.8"
            opacity="0.14" />
        <circle cx="580" cy="620" r="6" fill="none" stroke="var(--main-color)" stroke-width="0.8"
            opacity="0.30" />
        <circle cx="1380" cy="340" r="9" fill="none" stroke="var(--main-color)" stroke-width="0.8"
            opacity="0.13" />
        <circle cx="220" cy="740" r="14" fill="none" stroke="var(--focus-color)" stroke-width="0.8"
            opacity="0.13" />
        <circle cx="920" cy="740" r="5" fill="none" stroke="var(--main-color)" stroke-width="0.8"
            opacity="0.14" />

        {{-- Bubble isi sangat transparan --}}
        <circle cx="520" cy="700" r="26" fill="var(--focus-color)" opacity="0.05"
            stroke="var(--focus-color)" stroke-width="0.6" />
        <circle cx="1160" cy="160" r="20" fill="var(--main-color)" opacity="0.12"
            stroke="var(--main-color)" stroke-width="0.6" />
        <circle cx="180" cy="220" r="32" fill="var(--focus-color)" opacity="0.04"
            stroke="var(--focus-color)" stroke-width="0.5" />
        <circle cx="1320" cy="780" r="22" fill="var(--main-color)" opacity="0.05"
            stroke="var(--main-color)" stroke-width="0.5" />


        {{-- ── GARIS TIPIS VERTIKAL / HORIZONTAL ── --}}

        <line x1="680" y1="0" x2="680" y2="900" stroke="var(--main-color)"
            stroke-width="0.4" opacity="0.12" stroke-dasharray="4 24" />
        <line x1="0" y1="450" x2="1440" y2="450" stroke="var(--focus-color)"
            stroke-width="0.4" opacity="0.05" stroke-dasharray="4 24" />

    </svg>

    <script>
        const glows = document.querySelectorAll("#glow-bg .glow");

        function randomMove(el) {
            el.style.left = Math.random() * 100 + "%";
            el.style.top = Math.random() * 100 + "%";
            el.style.opacity = (0.2 + Math.random() * 0.3).toString();

            const size = 150 + Math.random() * 150;
            el.style.width = size + "px";
            el.style.height = size + "px";
        }

        glows.forEach(glow => {

            glow.style.left = Math.random() * 100 + "%";
            glow.style.top = Math.random() * 100 + "%";

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    randomMove(glow);
                });
            });

            setInterval(() => randomMove(glow), 7000 + Math.random() * 3000);
        });
    </script>

</div>
