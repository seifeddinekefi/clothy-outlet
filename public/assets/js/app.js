/* app.js — Clothy Outlet global scripts */

(function () {
    'use strict';

    /* ── Auto-dismiss flash messages ───────────────────────── */
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 450);
        }, 4000);
    });

})();
