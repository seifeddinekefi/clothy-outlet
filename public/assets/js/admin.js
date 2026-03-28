/**
 * public/assets/js/admin.js
 * Admin panel JavaScript — Clothy Outlet (ENHANCED v2)
 */
(function () {
    'use strict';

    // ── Sidebar toggle (mobile) ─────────────────────────────────
    const sidebar    = document.getElementById('admin-sidebar');
    const toggleBtn  = document.getElementById('sidebar-toggle');
    const closeBtn   = document.getElementById('sidebar-close');
    const overlay    = document.getElementById('sidebar-overlay');

    function openSidebar()  { if (sidebar)  { sidebar.classList.add('sidebar-open');    if (overlay) overlay.style.display='block'; } }
    function closeSidebar() { if (sidebar)  { sidebar.classList.remove('sidebar-open'); if (overlay) overlay.style.display='none';  } }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn)  closeBtn.addEventListener('click',  closeSidebar);
    if (overlay)   overlay.addEventListener('click',   closeSidebar);

    document.addEventListener('click', function (e) {
        if (sidebar && sidebar.classList.contains('sidebar-open') &&
            !sidebar.contains(e.target) && e.target !== toggleBtn) {
            closeSidebar();
        }
    });

    // ── Delete / destructive confirm forms ─────────────────────
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var msg = form.getAttribute('data-confirm') || 'Are you sure? This action cannot be undone.';
            if (!window.confirm(msg)) { e.preventDefault(); }
        });
    });

    // ── Table row click navigation ──────────────────────────────
    document.querySelectorAll('tr[data-href]').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a,button,form,input,select,.table-actions')) return;
            window.location.href = row.getAttribute('data-href');
        });
    });

    // ── Auto-generate slug from name field ──────────────────────
    function toSlug(str) {
        return str.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim()
                  .replace(/[\s]+/g, '-').replace(/-+/g, '-');
    }

    var nameField = document.getElementById('name');
    var slugField = document.getElementById('slug');
    if (nameField && slugField) {
        var slugManuallyEdited = slugField.value.length > 0;
        slugField.addEventListener('input', function () { slugManuallyEdited = true; });
        nameField.addEventListener('input', function () {
            if (!slugManuallyEdited) { slugField.value = toSlug(nameField.value); }
        });
    }

    // ── Flash message auto-dismiss ──────────────────────────────
    document.querySelectorAll('.flash').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.45s ease,max-height 0.45s ease';
            el.style.opacity = '0'; el.style.maxHeight = '0';
            setTimeout(function () { el.remove(); }, 450);
        }, 5500);
    });

    // ── Product image preview ────────────────────────────────────
    var imageInput = document.querySelector('input[name="images[]"]');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            var prev = document.getElementById('image-preview');
            if (!prev) {
                prev = document.createElement('div');
                prev.id = 'image-preview'; prev.className = 'image-grid';
                prev.style.marginTop = '0.5rem';
                imageInput.parentNode.appendChild(prev);
            }
            prev.innerHTML = '';
            Array.from(this.files).forEach(function (file) {
                if (!file.type.startsWith('image/')) return;
                var reader = new FileReader();
                reader.onload = function (ev) {
                    var wrap = document.createElement('div');
                    wrap.className = 'image-thumb';
                    var img = document.createElement('img');
                    img.src = ev.target.result; img.alt = file.name;
                    wrap.appendChild(img); prev.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // ── Dashboard Charts (Chart.js) ─────────────────────────────
    function initDashboardCharts() {
        var revenueCanvas = document.getElementById('revenueChart');
        var statusCanvas  = document.getElementById('statusChart');
        if (typeof Chart === 'undefined') return;

        // Revenue line chart
        if (revenueCanvas) {
            try {
                var raw    = revenueCanvas.getAttribute('data-values')  || '[]';
                var labels = revenueCanvas.getAttribute('data-labels')  || '[]';
                var vals   = JSON.parse(raw);
                var lbls   = JSON.parse(labels);

                Chart.defaults.font.family = '-apple-system,BlinkMacSystemFont,"Inter","Segoe UI",Roboto,sans-serif';

                new Chart(revenueCanvas, {
                    type: 'line',
                    data: {
                        labels: lbls,
                        datasets: [{
                            label: 'Revenue ($)',
                            data: vals,
                            borderColor: '#c4a97a',
                            backgroundColor: 'rgba(196,169,122,0.10)',
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) { return ' $' + parseFloat(ctx.raw).toFixed(2); }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display:false }, ticks: { maxTicksLimit:7, color:'#94a3b8', font:{size:11} } },
                            y: { grid: { color:'rgba(0,0,0,0.04)' }, ticks: { color:'#94a3b8', font:{size:11},
                                 callback: function(v){ return '$'+v.toLocaleString(); } } }
                        }
                    }
                });
            } catch(e) { console.warn('Revenue chart error:', e); }
        }

        // Order status donut chart
        if (statusCanvas) {
            try {
                var sLabels = JSON.parse(statusCanvas.getAttribute('data-labels') || '[]');
                var sVals   = JSON.parse(statusCanvas.getAttribute('data-values') || '[]');
                var colors  = ['#f59e0b','#3b82f6','#8b5cf6','#10b981','#ef4444'];

                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: sLabels,
                        datasets: [{ data: sVals, backgroundColor: colors.slice(0, sLabels.length),
                                     borderWidth: 2, borderColor: '#fff', hoverBorderWidth: 0 }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { position:'bottom', labels:{ padding:14, boxWidth:12, color:'#475569', font:{size:12} } }
                        }
                    }
                });
            } catch(e) { console.warn('Status chart error:', e); }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts);
    } else {
        initDashboardCharts();
    }

})();
