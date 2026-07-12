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
            pointer-events: none;

            background:
                radial-gradient(circle at 35% 30%,
                    rgba(255, 255, 255, .55) 0%,
                    transparent 18%),

             
  transition: transform 5s ease-in-out, opacity 5s ease-in-out;
            filter: blur(30px);
            animation: floatGlow 12s ease-in-out infinite;
        }

        .glow-xl {
            width: 340px;
            height: 340px;
            opacity: .35;
        }

        .glow-lg {
            width: 240px;
            height: 240px;
            opacity: .28;
            animation-duration: 16s;
        }

        .glow-md {
            width: 170px;
            height: 170px;
            opacity: .22;
            animation-duration: 11s;
        }

        .glow-sm {
            width: 90px;
            height: 90px;
            opacity: .18;
            animation-duration: 8s;
        }

        .glow-ring {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 2px solid color-mix(in srgb, var(--focus-color) 35%, transparent);
            filter: blur(6px);
            opacity: .18;
            animation: rotateRing 28s linear infinite;
        }

        .glow-ring::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: inherit;
            border: 1px solid color-mix(in srgb, var(--border-main-color) 45%, transparent);
        }

        .glow-ring-2 {
            width: 180px;
            height: 180px;
            animation-direction: reverse;
            animation-duration: 18s;
        }

        .glow-aurora {
            position: absolute;
            width: 420px;
            height: 180px;
            border-radius: 50%;

            background:
                linear-gradient(90deg,
                    transparent,
                    color-mix(in srgb, var(--focus-color) 18%, transparent),
                    color-mix(in srgb, var(--border-main-color) 28%, transparent),
                    transparent);

            filter: blur(65px);
            opacity: .35;

            animation:
                auroraMove 20s ease-in-out infinite;
        }

        .glow-star {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: white;
            box-shadow:
                0 0 12px white,
                0 0 30px var(--focus-color);

            animation:
                twinkle 4s ease-in-out infinite;
        }

        .glow-star-2 {
            width: 6px;
            height: 6px;
            animation-duration: 6s;
        }

        .glow-star-3 {
            width: 14px;
            height: 14px;
            animation-duration: 8s;
        }

        @keyframes floatGlow {

            0%,
            100% {
                transform: translate(0, 0) scale(.9);
            }

            50% {
                transform: translate(25px, -20px) scale(1.08);
            }
        }

        @keyframes rotateRing {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes auroraMove {

            0%,
            100% {
                transform: translateX(-20px) rotate(-8deg);
            }

            50% {
                transform: translateX(30px) rotate(8deg);
            }
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: .25;
                transform: scale(.8);
            }

            50% {
                opacity: 1;
                transform: scale(1.6);
            }
        }

        .glow {

            --tx: 0px;
            --ty: 0px;

            transform:
                translate(var(--tx), var(--ty)) scale(.9);

        }
    </style>

    <div class="glow glow-xl"></div>
    <div class="glow glow-lg"></div>
    <div class="glow glow-md"></div>
    <div class="glow glow-sm"></div>

    <div class="glow-ring"></div>
    <div class="glow-ring glow-ring-2"></div>

    <div class="glow-aurora"></div>

    <div class="glow-star"></div>
    <div class="glow-star glow-star-2"></div>
    <div class="glow-star glow-star-3"></div>

<script>
    const particles = document.querySelectorAll(
        "#glow-bg .glow, #glow-bg .glow-ring, #glow-bg .glow-aurora, #glow-bg .glow-star"
    );

    function randomMove(el) {
        // Tambahkan pembuatan nilai acak untuk x dan y
        const x = Math.floor(Math.random() * 100); // 0-100 vw
        const y = Math.floor(Math.random() * 100); // 0-100 vh

        el.style.setProperty("--tx", x + "vw");
        el.style.setProperty("--ty", y + "vh");
        
        // Opsional: ganti opacity jika ingin efek kedip
        el.style.transition = "all 5s ease-in-out"; 
    }

    particles.forEach(el => {
        // Posisi awal acak
        randomMove(el);

        // Interval gerak
        setInterval(() => {
            randomMove(el);
        }, 7000 + Math.random() * 4000);
    });
</script>

</div>
