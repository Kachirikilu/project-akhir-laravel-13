{{--
    ============================================================
    BACKGROUND ANIMATED BUBBLES
    Simpan di: resources/views/partials/bg-bubbles.blade.php

    Cara pakai — letakkan di awal <body>:
    @include('partials.bg-bubbles')

    Warna otomatis mengikuti --focus-color & --main-color.
    Semua elemen spawn di posisi random, bergerak bebas,
    dan fade out setelah lifetime tertentu.

    Elemen yang di-spawn:
    • bubble-glow  — gelembung transparan realistis (4 aktif)
    • bubble-dot   — titik bintang kecil berkelip (8 aktif)
    • ring         — cincin outline berputar lambat (3 aktif)
    • aurora       — streak cahaya lebar (2 aktif)
    ============================================================
--}}

<div id="bg-bubbles" aria-hidden="true"
    class="pointer-events-none fixed inset-0 z-0 overflow-hidden">

    {{-- Static background blob — selalu ada, tidak dianimasikan JS ─────── --}}
    <div class="absolute -top-20 -left-16 w-[360px] h-[360px] rounded-full pointer-events-none"
        style="background: var(--main-color); opacity: 0.12; filter: blur(90px);"></div>
    <div class="absolute -bottom-16 -right-12 w-[280px] h-[280px] rounded-full pointer-events-none"
        style="background: var(--focus-color); opacity: 0.10; filter: blur(80px);"></div>

    <style>
        /* ── Bubble realistis ── */
        .bb-bubble {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            will-change: transform, opacity, left, top;
            background:
                radial-gradient(circle at 34% 27%,
                    rgba(255, 255, 255, 0.65) 0%,
                    rgba(255, 255, 255, 0.15) 10%,
                    transparent 40%),
                radial-gradient(circle at 66% 74%,
                    rgba(255, 255, 255, 0.07) 0%,
                    transparent 35%),
                radial-gradient(circle,
                    color-mix(in srgb, var(--focus-color) 30%, transparent) 0%,
                    color-mix(in srgb, var(--focus-color) 15%, transparent) 50%,
                    transparent 80%);
            border: 1px solid rgba(255, 255, 255, 0.10);
            box-shadow:
                inset 0 0 18px rgba(255, 255, 255, 0.04),
                0 4px 28px color-mix(in srgb, var(--focus-color) 18%, transparent);
        }

        /* ── Titik bintang ── */
        .bb-dot {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            will-change: opacity, left, top;
            background: white;
            box-shadow:
                0 0 8px rgba(255, 255, 255, 0.9),
                0 0 24px color-mix(in srgb, var(--focus-color) 70%, transparent);
        }

        /* ── Ring ── */
        .bb-ring {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            will-change: transform, opacity, left, top;
            border: 1px solid color-mix(in srgb, var(--focus-color) 28%, transparent);
        }
        .bb-ring::before {
            content: '';
            position: absolute;
            inset: 14px;
            border-radius: 50%;
            border: 0.5px solid color-mix(in srgb, var(--border-main-color) 20%, transparent);
        }

        /* ── Aurora streak ── */
        .bb-aurora {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            will-change: transform, opacity, left, top;
            background: linear-gradient(
                100deg,
                transparent,
                color-mix(in srgb, var(--focus-color) 14%, transparent),
                color-mix(in srgb, var(--border-main-color) 22%, transparent),
                transparent
            );
            filter: blur(44px);
        }
    </style>

    <script>
    (function () {
        const container = document.getElementById('bg-bubbles');
        function rnd(a, b) { return a + Math.random() * (b - a); }

        /* ── BUBBLE ── */
        function spawnBubble() {
            const el = document.createElement('div');
            el.className = 'bb-bubble';
            const size = rnd(36, 120);
            let cx = rnd(2, 90), cy = rnd(5, 85);
            el.style.cssText = `width:${size}px;height:${size}px;left:${cx}%;top:${cy}%;opacity:0;transition:opacity 2s ease`;
            container.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.style.opacity = rnd(0.18, 0.50);
            }));

            let t = Math.random() * 100;
            const spd = rnd(0.006, 0.018), amp = rnd(2.5, 7);
            function tick() {
                t += spd;
                el.style.left = (cx + Math.sin(t * 0.65) * amp) + '%';
                el.style.top  = (cy + Math.cos(t * 0.45) * (amp * 0.6)) + '%';
                el._raf = requestAnimationFrame(tick);
            }
            tick();

            const life = rnd(9000, 20000);
            setTimeout(() => {
                el.style.transition = 'opacity 1.8s ease';
                el.style.opacity = 0;
                setTimeout(() => { cancelAnimationFrame(el._raf); el.remove(); }, 1900);
            }, life);
        }

        /* ── DOT ── */
        function spawnDot() {
            const el = document.createElement('div');
            el.className = 'bb-dot';
            const size = rnd(2.5, 6);
            let cx = rnd(3, 95), cy = rnd(5, 90);
            el.style.cssText = `width:${size}px;height:${size}px;left:${cx}%;top:${cy}%;opacity:0;transition:opacity 1.2s ease`;
            container.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.style.opacity = rnd(0.35, 0.9);
            }));

            // twinkle
            let dir = 1;
            function twinkle() {
                const v = Math.min(0.95, Math.max(0.2, (parseFloat(el.style.opacity)||0.5) + dir * rnd(0.02, 0.06)));
                if (v >= 0.92) dir = -1; if (v <= 0.22) dir = 1;
                el.style.transition = `opacity ${rnd(80,200)}ms ease`;
                el.style.opacity = v;
                el._tw = setTimeout(twinkle, rnd(80, 220));
            }
            twinkle();

            let t = Math.random() * 100, spd = rnd(0.004, 0.012);
            function tick() {
                t += spd;
                el.style.left = (cx + Math.sin(t) * rnd(0.8, 2.5)) + '%';
                el.style.top  = (cy + Math.cos(t * 0.7) * rnd(0.5, 1.8)) + '%';
                el._raf = requestAnimationFrame(tick);
            }
            tick();

            const life = rnd(5000, 13000);
            setTimeout(() => {
                clearTimeout(el._tw);
                el.style.transition = 'opacity 1s ease'; el.style.opacity = 0;
                setTimeout(() => { cancelAnimationFrame(el._raf); el.remove(); }, 1100);
            }, life);
        }

        /* ── RING ── */
        function spawnRing() {
            const el = document.createElement('div');
            el.className = 'bb-ring';
            const size = rnd(70, 200);
            let cx = rnd(2, 85), cy = rnd(2, 80);
            el.style.cssText = `width:${size}px;height:${size}px;left:${cx}%;top:${cy}%;opacity:0;transition:opacity 2.2s ease`;
            container.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.style.opacity = rnd(0.12, 0.28);
            }));

            let angle = 0, t = Math.random() * 50, spd = rnd(0.002, 0.008);
            const rotSpd = rnd(0.15, 0.5) * (Math.random() > 0.5 ? 1 : -1);
            function tick() {
                t += spd; angle += rotSpd;
                el.style.left = (cx + Math.sin(t * 0.4) * 2.5) + '%';
                el.style.top  = (cy + Math.cos(t * 0.3) * 2) + '%';
                el.style.transform = `rotate(${angle}deg)`;
                el._raf = requestAnimationFrame(tick);
            }
            tick();

            const life = rnd(12000, 24000);
            setTimeout(() => {
                el.style.transition = 'opacity 2s ease'; el.style.opacity = 0;
                setTimeout(() => { cancelAnimationFrame(el._raf); el.remove(); }, 2100);
            }, life);
        }

        /* ── AURORA ── */
        function spawnAurora() {
            const el = document.createElement('div');
            el.className = 'bb-aurora';
            const w = rnd(180, 360), h = rnd(55, 100);
            let cx = rnd(5, 72), cy = rnd(5, 72);
            el.style.cssText = `width:${w}px;height:${h}px;left:${cx}%;top:${cy}%;opacity:0;transition:opacity 2.8s ease`;
            container.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.style.opacity = rnd(0.10, 0.25);
            }));

            let t = Math.random() * 50, spd = rnd(0.002, 0.006), rotAmp = rnd(4, 10);
            function tick() {
                t += spd;
                el.style.left = (cx + Math.sin(t * 0.5) * 4) + '%';
                el.style.top  = (cy + Math.cos(t * 0.4) * 2.5) + '%';
                el.style.transform = `rotate(${Math.sin(t) * rotAmp}deg)`;
                el._raf = requestAnimationFrame(tick);
            }
            tick();

            const life = rnd(14000, 28000);
            setTimeout(() => {
                el.style.transition = 'opacity 2.5s ease'; el.style.opacity = 0;
                setTimeout(() => { cancelAnimationFrame(el._raf); el.remove(); }, 2600);
            }, life);
        }

        /* ── Initial spawn ── */
        for (let i = 0; i < 4; i++) setTimeout(spawnBubble, i * 500);
        for (let i = 0; i < 8; i++) setTimeout(spawnDot,    i * 180);
        for (let i = 0; i < 3; i++) setTimeout(spawnRing,   i * 700);
        for (let i = 0; i < 2; i++) setTimeout(spawnAurora, i * 1000);

        /* ── Continuous spawn ── */
        setInterval(spawnBubble, 3800);
        setInterval(spawnDot,    2200);
        setInterval(spawnRing,   5500);
        setInterval(spawnAurora, 9000);
    })();
    </script>
</div>