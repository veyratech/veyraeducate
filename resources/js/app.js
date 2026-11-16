import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Micro haptic vibration helper for mobile devices (Android)
window.vibrateHaptic = function (pattern = 12) {
    if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
        try {
            navigator.vibrate(pattern);
        } catch (e) {
            // Ignore if vibration not permitted
        }
    }
};

Alpine.data('countUp', (target, options = {}) => ({
    val: 0,
    init() {
        const final = Number(target) || 0;
        const prefix = options.prefix ?? '';
        const duration = options.duration ?? 1000;
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const formatter = new Intl.NumberFormat('id-ID');

        this.display = prefix + formatter.format(final);

        if (reduced || final === 0) {
            return;
        }

        const start = performance.now();
        const step = (now) => {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            this.val = Math.round(final * eased);
            this.display = prefix + formatter.format(this.val);

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        this.val = 0;
        requestAnimationFrame(step);
    },
}));

if (typeof window.sidebarCollapsed === 'undefined') {
    window.sidebarCollapsed = false;
}

if (typeof window.appLayout === 'function') {
    Alpine.data('appLayout', window.appLayout);
}

/* Scroll-triggered reveal system */
(function initScrollReveal() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (typeof IntersectionObserver === 'undefined') return;

    const revealEls = document.querySelectorAll('.reveal, .reveal-up, .reveal-down, .reveal-left, .reveal-right, .reveal-scale, .section-reveal, .card-reveal, .stagger-reveal, .stagger-reveal-scale, .reveal-split-left, .reveal-split-right');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(el => observer.observe(el));
})();

Alpine.start();

