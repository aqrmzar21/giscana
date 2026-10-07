import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Scroll Reveal Observer Engine
function initScrollReveal() {
    const elements = document.querySelectorAll('.scroll-reveal:not(.reveal-visible)');
    if (!elements.length) return;

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });

        elements.forEach(el => observer.observe(el));
    } else {
        // Fallback for older browsers
        elements.forEach(el => el.classList.add('reveal-visible'));
    }
}

window.initScrollReveal = initScrollReveal;

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
});

document.addEventListener('pjax:complete', () => {
    setTimeout(initScrollReveal, 50);
});

Alpine.start();

// PJAX partial page loading — intercept link clicks & replace only #page-content
import './ajax-router';
