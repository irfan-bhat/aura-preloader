/* Aura Preloader — front-end script */
(function () {
    var el   = document.getElementById('aura-preloader');
    var fill = el && el.querySelector('.aura-fill');
    if (!el) return;

    var cfg        = window.auraConfig || {};
    var fade       = parseInt(cfg.fade,       10) || 500;
    var minDisplay = parseInt(cfg.minDisplay, 10) || 800;

    // Apply fade duration as a CSS custom property
    el.style.setProperty('--aura-fade', (fade / 1000) + 's');

    var startTime = Date.now();

    // Kick progress bar to ~70 % quickly so it feels alive
    if (fill) {
        setTimeout(function () { fill.style.width = '70%'; }, 80);
    }

    function hide() {
        var elapsed  = Date.now() - startTime;
        var remaining = Math.max(0, minDisplay - elapsed);

        setTimeout(function () {
            if (fill) fill.style.width = '100%';

            setTimeout(function () {
                el.classList.add('aura-hidden');

                // Remove from DOM after transition ends
                setTimeout(function () {
                    if (el.parentNode) el.parentNode.removeChild(el);
                }, fade + 50);
            }, 300); // let bar reach 100% visually before fading out
        }, remaining);
    }

    if (document.readyState === 'complete') {
        hide();
    } else {
        window.addEventListener('load', hide);
    }
})();

