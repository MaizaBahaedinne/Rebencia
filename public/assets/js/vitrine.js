/**
 * Rebencia Vitrine — JS
 */
(function () {
    'use strict';

    // =========================================================
    //  NAVBAR : scroll effect
    // =========================================================
    const navbar = document.getElementById('rbNavbar');
    if (navbar) {
        const toggleScrolled = () => navbar.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', toggleScrolled, { passive: true });
        toggleScrolled();
    }

    // =========================================================
    //  ANIMATED COUNTERS
    // =========================================================
    const animateCounter = (el) => {
        const target = parseInt(el.dataset.count, 10);
        if (isNaN(target)) return;
        const duration = 1600;
        const start    = performance.now();
        const update   = (now) => {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased    = 1 - Math.pow(1 - progress, 3); // ease-out-cubic
            el.textContent = Math.round(eased * target).toLocaleString() + '+';
            if (progress < 1) requestAnimationFrame(update);
        };
        requestAnimationFrame(update);
    };

    const counters     = document.querySelectorAll('.rb-stat-number[data-count]');
    let   countersDone = false;

    if (counters.length) {
        const observer = new IntersectionObserver((entries) => {
            if (countersDone) return;
            entries.forEach(e => {
                if (e.isIntersecting) {
                    countersDone = true;
                    counters.forEach(animateCounter);
                    observer.disconnect();
                }
            });
        }, { threshold: 0.3 });
        observer.observe(counters[0]);
    }

    // =========================================================
    //  SCROLL REVEAL (simple fade-in-up)
    // =========================================================
    const revealElements = document.querySelectorAll(
        '.rb-property-card, .rb-why-card, .rb-testimonial-card, .rb-team-card, .rb-blog-card'
    );
    if (revealElements.length && 'IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.style.animation = 'fadeInUp .5s ease both';
                    revealObserver.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        revealElements.forEach(el => {
            el.style.opacity   = '0';
            el.style.transform = 'translateY(20px)';
            revealObserver.observe(el);
        });
    }

    // =========================================================
    //  PROPERTY GALLERY (detail page)
    // =========================================================
    window.switchPhoto = function (thumb, url) {
        const main = document.getElementById('mainPhoto');
        if (main) main.src = url;
        document.querySelectorAll('.rb-gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    };

    // =========================================================
    //  FILTER FORM — auto-submit on select change
    // =========================================================
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(sel => {
            sel.addEventListener('change', () => filterForm.submit());
        });
    }

    // =========================================================
    //  FLASH MESSAGES — auto-dismiss after 5s
    // =========================================================
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

})();
