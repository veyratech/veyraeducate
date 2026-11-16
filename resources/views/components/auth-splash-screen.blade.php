@if (! $errors->any() && ! session('status'))
<div id="auth-splash-screen" class="cyber-splash" aria-hidden="true">
    {{-- Background Cyber Space & Grid --}}
    <div class="cyber-bg">
        <div class="cyber-ambient-blue"></div>
        <div class="cyber-ambient-gold"></div>
        <div class="cyber-grid"></div>
        <div class="cyber-scanline"></div>
    </div>

    {{-- Tombol Lewati / Skip Bergaya Cyber HUD --}}
    <button type="button" id="auth-splash-skip" class="cyber-skip-btn" aria-label="Lewati Intro">
        <span class="cyber-skip-tag">//</span>
        <span>LEWATI INTRO</span>
        <span class="cyber-skip-key">[ESC]</span>
    </button>

    <div class="cyber-stage">
        {{-- High-Tech Orbital Rings & Logo Core --}}
        <div class="cyber-core">
            {{-- Ring Luar (Rotasi Searah Jarum Jam) --}}
            <svg class="cyber-ring cyber-ring-outer" viewBox="0 0 260 260" aria-hidden="true">
                <circle cx="130" cy="130" r="124" stroke="rgba(37, 99, 235, 0.22)" stroke-width="1.5" fill="none" />
                <circle cx="130" cy="130" r="124" stroke="url(#cyber-grad-cyan)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="24 60 12 40 80 50" fill="none" />
                <circle cx="130" cy="130" r="118" stroke="rgba(56, 189, 248, 0.35)" stroke-width="1" stroke-dasharray="3 14" fill="none" />
                <defs>
                    <linearGradient id="cyber-grad-cyan" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38bdf8" />
                        <stop offset="50%" stop-color="#2563eb" />
                        <stop offset="100%" stop-color="#06b6d4" />
                    </linearGradient>
                </defs>
            </svg>

            {{-- Ring Dalam (Rotasi Berlawanan Jarum Jam) --}}
            <svg class="cyber-ring cyber-ring-inner" viewBox="0 0 210 210" aria-hidden="true">
                <circle cx="105" cy="105" r="96" stroke="rgba(234, 179, 8, 0.25)" stroke-width="1.5" fill="none" />
                <circle cx="105" cy="105" r="96" stroke="url(#cyber-grad-gold)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="45 35 15 40" fill="none" />
                <circle cx="105" cy="105" r="88" stroke="rgba(251, 191, 36, 0.4)" stroke-width="1" stroke-dasharray="4 10" fill="none" />
                <defs>
                    <linearGradient id="cyber-grad-gold" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fbbf24" />
                        <stop offset="60%" stop-color="#eab308" />
                        <stop offset="100%" stop-color="#f59e0b" />
                    </linearGradient>
                </defs>
            </svg>

            {{-- Pendar Cahaya Belakang Logo --}}
            <div class="cyber-logo-glow"></div>

            {{-- Logo Container dengan Efek Laser Shimmer Sweep --}}
            <div class="cyber-logo-box">
                <img
                    src="{{ asset('images/logo-veyra-educate.png') }}"
                    alt="{{ config('app.name') }}"
                    width="852"
                    height="838"
                    class="cyber-logo"
                    loading="eager"
                    fetchpriority="high"
                />
                {{-- Kilatan Laser yang Menyapu Logo --}}
                <div class="cyber-laser-sweep"></div>
            </div>
        </div>

        {{-- Cyber HUD Typography --}}
        <div class="cyber-telemetry">
            <div class="cyber-badge">
                <span class="cyber-badge-dot"></span>
                <span>SYSTEM ONLINE · VEYRA OS</span>
            </div>

            <h1 class="cyber-title">
                <span>{{ config('app.name') }}</span>
            </h1>

            <p class="cyber-subtitle">Wadah Aplikasi Sekolah Terpadu</p>
        </div>

        {{-- Cyber Progress Bar & Status Readout --}}
        <div class="cyber-progress-section">
            <div class="cyber-status-row">
                <span id="cyber-status-text" class="cyber-status-label">MEMULAI SUBSISTEM...</span>
                <span id="cyber-percent-text" class="cyber-percent">0%</span>
            </div>

            <div class="cyber-bar-track">
                <div id="cyber-bar-fill" class="cyber-bar-fill">
                    <span class="cyber-bar-tip"></span>
                </div>
            </div>

            {{-- Windows Orbit Dots Terintegrasi Halus --}}
            <div class="cyber-orbit-dots" role="status" aria-label="Memuat">
                <div class="win-dot"></div>
                <div class="win-dot"></div>
                <div class="win-dot"></div>
                <div class="win-dot"></div>
                <div class="win-dot"></div>
            </div>
        </div>
    </div>
</div>

<style>
.cyber-splash {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 100%;
    height: 100%;
    max-height: 100%;
    min-height: -webkit-fill-available;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #020611;
    overflow: hidden;
    user-select: none;
    cursor: default;
    opacity: 1;
    box-sizing: border-box;
    padding-top: max(env(safe-area-inset-top, 0px), 12px);
    padding-bottom: max(env(safe-area-inset-bottom, 0px), 12px);
    padding-left: max(env(safe-area-inset-left, 0px), 16px);
    padding-right: max(env(safe-area-inset-right, 0px), 16px);
    transition: opacity 0.65s cubic-bezier(0.4, 0, 0.2, 1), transform 0.65s cubic-bezier(0.4, 0, 0.2, 1), filter 0.65s ease;
}

.cyber-splash.cyber-splash-hidden {
    opacity: 0;
    transform: scale(1.02);
    filter: blur(8px) brightness(1.25);
    pointer-events: none;
    visibility: hidden;
}

/* Background Cyber Grid & Nebula */
.cyber-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.cyber-ambient-blue {
    position: absolute;
    top: 30%;
    left: 50%;
    width: 600px;
    height: 600px;
    transform: translate(-50%, -50%);
    border-radius: 50%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.28) 0%, rgba(6, 182, 212, 0.12) 40%, transparent 70%);
    filter: blur(60px);
    animation: cyber-glow-drift 6s ease-in-out infinite alternate;
}

.cyber-ambient-gold {
    position: absolute;
    top: 55%;
    left: 50%;
    width: 480px;
    height: 480px;
    transform: translate(-50%, -50%);
    border-radius: 50%;
    background: radial-gradient(circle, rgba(234, 179, 8, 0.16) 0%, transparent 65%);
    filter: blur(50px);
    animation: cyber-glow-drift 5s ease-in-out infinite alternate-reverse;
}

.cyber-grid {
    position: absolute;
    inset: 0;
    opacity: 0.08;
    background-size: 40px 40px;
    background-image:
        linear-gradient(to right, rgba(56, 189, 248, 0.4) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(56, 189, 248, 0.4) 1px, transparent 1px);
    mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
}

.cyber-scanline {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.3) 51%);
    background-size: 100% 4px;
    opacity: 0.25;
}

/* Tombol Lewati HUD */
.cyber-skip-btn {
    position: absolute;
    top: max(16px, env(safe-area-inset-top, 16px));
    right: max(16px, env(safe-area-inset-right, 16px));
    z-index: 50;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 6px;
    border: 1px solid rgba(56, 189, 248, 0.3);
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(12px);
    color: #e2e8f0;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    cursor: pointer;
    box-shadow: 0 0 15px rgba(56, 189, 248, 0.15);
    transition: all 0.2s ease;
    animation: cyber-fade-in 0.8s ease 0.6s both;
}

.cyber-skip-btn:hover {
    border-color: #38bdf8;
    background: rgba(30, 41, 59, 0.85);
    color: #38bdf8;
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
    transform: translateY(-1px);
}

.cyber-skip-tag {
    color: #eab308;
}

.cyber-skip-key {
    color: #94a3b8;
    font-size: 0.625rem;
}

@media (max-width: 639px) {
    .cyber-skip-key {
        display: none;
    }
}

/* Stage Area */
.cyber-stage {
    position: relative;
    z-index: 20;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin: auto;
    padding: 0 16px;
    width: 100%;
    max-width: 480px;
    box-sizing: border-box;
}

/* Core: Logo & Orbital Rings */
.cyber-core {
    position: relative;
    width: 190px;
    height: 190px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: cyber-core-intro 1s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@media (min-width: 640px) {
    .cyber-core {
        width: 230px;
        height: 230px;
    }
}

.cyber-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transform-origin: center center;
    transform-box: fill-box;
    pointer-events: none;
}

.cyber-ring-outer {
    width: 100%;
    height: 100%;
    animation: cyber-spin-cw 18s linear infinite;
    filter: drop-shadow(0 0 8px rgba(56, 189, 248, 0.45));
}

.cyber-ring-inner {
    width: 84%;
    height: 84%;
    animation: cyber-spin-ccw 14s linear infinite;
    filter: drop-shadow(0 0 8px rgba(234, 179, 8, 0.45));
}

.cyber-logo-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transform-origin: center center;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.5) 0%, rgba(234, 179, 8, 0.25) 55%, transparent 75%);
    filter: blur(24px);
    animation: cyber-pulse 2.2s ease-in-out infinite alternate;
    pointer-events: none;
}

@media (min-width: 640px) {
    .cyber-logo-glow {
        width: 135px;
        height: 135px;
    }
}

.cyber-logo-box {
    position: relative;
    z-index: 10;
    width: 86px;
    height: 86px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    overflow: hidden;
}

@media (min-width: 640px) {
    .cyber-logo-box {
        width: 105px;
        height: 105px;
    }
}

.cyber-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    margin: auto;
    display: block;
    filter: drop-shadow(0 0 16px rgba(255, 255, 255, 0.25)) drop-shadow(0 0 28px rgba(56, 189, 248, 0.6));
    animation: cyber-logo-pop 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Kilatan Laser Menyapu Logo */
.cyber-laser-sweep {
    position: absolute;
    top: -50%;
    left: -120%;
    width: 80%;
    height: 200%;
    background: linear-gradient(105deg, transparent 20%, rgba(255, 255, 255, 0.85) 50%, transparent 80%);
    transform: rotate(25deg);
    opacity: 0.9;
    animation: cyber-sweep 2.4s cubic-bezier(0.25, 1, 0.5, 1) 1.2s infinite;
    pointer-events: none;
}

/* Telemetry & Title */
.cyber-telemetry {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100%;
}

.cyber-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 3px 12px;
    border-radius: 9999px;
    border: 1px solid rgba(56, 189, 248, 0.35);
    background: rgba(56, 189, 248, 0.08);
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    margin-right: -0.2em;
    color: #38bdf8;
    box-shadow: 0 0 12px rgba(56, 189, 248, 0.2);
    animation: cyber-fade-in 0.8s ease 0.8s both;
}

.cyber-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 8px #22c55e;
    animation: cyber-blink 1.2s ease-in-out infinite;
}

.cyber-title {
    margin-top: 10px;
    font-size: 1.35rem;
    font-weight: 900;
    letter-spacing: 0.18em;
    margin-right: -0.18em;
    text-transform: uppercase;
    text-align: center;
    background: linear-gradient(135deg, #ffffff 30%, #93c5fd 70%, #fbbf24 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 0 20px rgba(56, 189, 248, 0.45));
    animation: cyber-fade-in 0.8s ease 1s both;
}

@media (min-width: 640px) {
    .cyber-title {
        font-size: 1.75rem;
        letter-spacing: 0.22em;
        margin-right: -0.22em;
    }
}

.cyber-subtitle {
    margin-top: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    margin-right: -0.1em;
    color: #94a3b8;
    text-align: center;
    animation: cyber-fade-in 0.8s ease 1.2s both;
}

@media (min-width: 640px) {
    .cyber-subtitle {
        font-size: 0.8125rem;
        letter-spacing: 0.12em;
        margin-right: -0.12em;
    }
}

/* Progress Section */
.cyber-progress-section {
    margin-top: 24px;
    width: 100%;
    max-width: 340px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-left: auto;
    margin-right: auto;
    animation: cyber-fade-in 0.8s ease 1.4s both;
}

@media (min-width: 640px) {
    .cyber-progress-section {
        margin-top: 28px;
        max-width: 380px;
    }
}

.cyber-status-row {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.6875rem;
    letter-spacing: 0.12em;
    margin-bottom: 8px;
}

.cyber-status-label {
    color: #38bdf8;
    font-weight: 600;
}

.cyber-percent {
    color: #fbbf24;
    font-weight: 800;
}

.cyber-bar-track {
    position: relative;
    width: 100%;
    height: 6px;
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(56, 189, 248, 0.25);
    border-radius: 9999px;
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
}

.cyber-bar-fill {
    position: relative;
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #2563eb 0%, #38bdf8 65%, #fbbf24 100%);
    border-radius: 9999px;
    box-shadow: 0 0 14px rgba(56, 189, 248, 0.7);
    transition: width 0.08s linear;
}

.cyber-bar-tip {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 8px;
    background: #ffffff;
    box-shadow: 0 0 8px #ffffff;
    border-radius: 50%;
}

/* Orbit Dots Windows Terintegrasi */
.cyber-orbit-dots {
    position: relative;
    width: 32px;
    height: 32px;
    margin-top: 18px;
}

.cyber-orbit-dots .win-dot {
    position: absolute;
    inset: 0;
    opacity: 0;
    transform: rotate(225deg);
    animation: win-orbit 4.5s infinite;
}

.cyber-orbit-dots .win-dot::after {
    content: '';
    position: absolute;
    width: 3.5px;
    height: 3.5px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 6px #38bdf8, 0 0 10px #2563eb;
}

.cyber-orbit-dots .win-dot:nth-child(1) { animation-delay: 0.12s; }
.cyber-orbit-dots .win-dot:nth-child(2) { animation-delay: 0.28s; }
.cyber-orbit-dots .win-dot:nth-child(3) { animation-delay: 0.44s; }
.cyber-orbit-dots .win-dot:nth-child(4) { animation-delay: 0.60s; }
.cyber-orbit-dots .win-dot:nth-child(5) { animation-delay: 0.76s; }

/* Animations */
@keyframes cyber-spin-cw {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

@keyframes cyber-spin-ccw {
    from { transform: translate(-50%, -50%) rotate(360deg); }
    to { transform: translate(-50%, -50%) rotate(0deg); }
}

@keyframes cyber-pulse {
    0% { transform: translate(-50%, -50%) scale(0.92); opacity: 0.6; }
    100% { transform: translate(-50%, -50%) scale(1.12); opacity: 0.95; }
}

@keyframes cyber-glow-drift {
    0% { transform: translate(-50%, -50%) scale(0.95); }
    100% { transform: translate(-48%, -52%) scale(1.08); }
}

@keyframes cyber-core-intro {
    0% {
        opacity: 0;
        transform: scale(0.65);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes cyber-logo-pop {
    0% {
        opacity: 0;
        transform: scale(0.85);
        filter: blur(8px) brightness(0.5);
    }
    100% {
        opacity: 1;
        transform: scale(1);
        filter: blur(0px) brightness(1);
    }
}

@keyframes cyber-sweep {
    0% { left: -120%; }
    40%, 100% { left: 160%; }
}

@keyframes cyber-blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

@keyframes cyber-fade-in {
    0% { opacity: 0; transform: translateY(8px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes win-orbit {
    0% {
        transform: rotate(225deg);
        opacity: 1;
        animation-timing-function: ease-out;
    }
    7% {
        transform: rotate(345deg);
        animation-timing-function: linear;
    }
    30% {
        transform: rotate(455deg);
        animation-timing-function: ease-in-out;
    }
    39% {
        transform: rotate(690deg);
        animation-timing-function: linear;
    }
    70% {
        transform: rotate(815deg);
        opacity: 1;
        animation-timing-function: ease-out;
    }
    75% {
        transform: rotate(945deg);
        animation-timing-function: ease-out;
    }
    76% {
        transform: rotate(945deg);
        opacity: 0;
    }
    100% {
        transform: rotate(945deg);
        opacity: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .cyber-splash {
        display: none !important;
    }
}
</style>

<script>
(function() {
    var splash = document.getElementById('auth-splash-screen');
    if (!splash) return;

    // Lewati langsung jika user sudah pernah melihat intro di sesi tab browser ini
    if (sessionStorage.getItem('veyra_auth_splash_seen')) {
        splash.style.display = 'none';
        if (splash.parentNode) splash.parentNode.removeChild(splash);
        return;
    }
    sessionStorage.setItem('veyra_auth_splash_seen', 'true');

    var skipBtn = document.getElementById('auth-splash-skip');
    var statusText = document.getElementById('cyber-status-text');
    var percentText = document.getElementById('cyber-percent-text');
    var barFill = document.getElementById('cyber-bar-fill');

    var dismissed = false;
    var durationMs = 2800; // Optimal 2.8 detik (sebelumnya 5.5 detik yang terlalu lambat)
    var startTime = null;
    var animFrame = null;

    function dismiss() {
        if (dismissed) return;
        dismissed = true;
        if (animFrame) cancelAnimationFrame(animFrame);
        splash.classList.add('cyber-splash-hidden');
        setTimeout(function() {
            if (splash && splash.parentNode) {
                splash.parentNode.removeChild(splash);
            }
        }, 700);
    }

    // Status readouts progresif ala Cyber OS
    function updateProgress(timestamp) {
        if (!startTime) startTime = timestamp;
        var elapsed = timestamp - startTime;
        var progress = Math.min(elapsed / (durationMs - 600), 1); // 100% tercapai di detik ~4.9

        var percent = Math.floor(progress * 100);
        if (percentText) percentText.textContent = percent + '%';
        if (barFill) barFill.style.width = percent + '%';

        if (statusText) {
            if (percent < 25) {
                statusText.textContent = 'MEMULAI SUBSISTEM...';
            } else if (percent < 55) {
                statusText.textContent = 'MEMUAT MODUL APLIKASI...';
            } else if (percent < 85) {
                statusText.textContent = 'MENYIAPKAN SESI AMAN...';
            } else if (percent < 100) {
                statusText.textContent = 'FINALISASI ANTARMUKA...';
            } else {
                statusText.textContent = 'AKSES DIIZINKAN';
                statusText.style.color = '#22c55e';
            }
        }

        if (progress < 1) {
            animFrame = requestAnimationFrame(updateProgress);
        } else {
            // Selesai 100%, tunggu 600ms lalu transisi masuk
            setTimeout(dismiss, 600);
        }
    }

    animFrame = requestAnimationFrame(updateProgress);

    // Skip controls
    if (skipBtn) {
        skipBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dismiss();
        });
    }

    splash.addEventListener('click', function() {
        dismiss();
    });

    window.addEventListener('keydown', function() {
        if (!dismissed) {
            dismiss();
        }
    }, { once: true });
})();
</script>
@endif
