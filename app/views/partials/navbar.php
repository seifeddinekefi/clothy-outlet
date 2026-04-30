<!-- app/views/partials/navbar.php -->
<style>
:root {
  --nav-height: 68px;
  --container-max: 1280px;
  --container-pad: clamp(1rem, 4vw, 2rem);
}

.site-header {
  position: sticky;
  top: 0;
  z-index: 500;
  background: #fff;
  border-bottom: 1px solid #e8e6e2;
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
}

.navbar {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 var(--container-pad);
  height: var(--nav-height);
}

.navbar-brand {
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 1.18rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #0a0a0a;
  text-decoration: none;
  white-space: nowrap;
  flex-shrink: 0;
}

/* ── Desktop nav links ── */
.nav-links {
  display: flex;
  list-style: none;
  gap: 1.75rem;
  flex-shrink: 0;
}

.nav-links a {
  font-size: .75rem;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #3a3730;
  text-decoration: none;
  transition: color .2s;
}

.nav-links a:hover { color: #c4a97a; }

/* ── Search ── */
.nav-search {
  flex: 1;
  position: relative;
  max-width: 400px;
  margin: 0 auto;
}

.nav-search-input {
  width: 100%;
  height: 36px;
  border: 1.5px solid #e0deda;
  border-radius: 18px;
  padding: 0 1rem 0 2.3rem;
  font-size: .82rem;
  background: #fafaf9;
  color: #0a0a0a;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}

.nav-search-input:focus {
  border-color: #c4a97a;
  box-shadow: 0 0 0 3px rgba(196,169,122,.12);
  background: #fff;
}

.nav-search-icon {
  position: absolute;
  left: .75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
  pointer-events: none;
}

/* ── Autocomplete ── */
.nav-ac-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #e8e6e2;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,.10);
  overflow: hidden;
  z-index: 9999;
  display: none;
}

.nav-ac-dropdown.open { display: block; }

.nav-ac-item {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .6rem 1rem;
  text-decoration: none;
  color: #0a0a0a;
  transition: background .15s;
}

.nav-ac-item:hover, .nav-ac-item.highlighted { background: #faf9f7; }

.nav-ac-img {
  width: 38px; height: 38px;
  border-radius: 6px;
  object-fit: cover;
  background: #f0ede8;
  flex-shrink: 0;
}

.nav-ac-img-placeholder {
  width: 38px; height: 38px;
  border-radius: 6px;
  background: #f0ede8;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ccc;
}

.nav-ac-name {
  font-size: .83rem;
  font-weight: 600;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-ac-price { font-size: .78rem; color: #7a7570; flex-shrink: 0; }

.nav-ac-footer {
  padding: .5rem 1rem;
  font-size: .75rem;
  color: #c4a97a;
  text-align: center;
  border-top: 1px solid #f0ede8;
  cursor: pointer;
}

.nav-ac-footer:hover { background: #faf9f7; }
.nav-ac-empty { padding: 1rem; text-align: center; font-size: .82rem; color: #aaa; }

/* ── Desktop auth actions ── */
.nav-actions {
  display: flex;
  align-items: center;
  gap: .75rem;
  flex-shrink: 0;
}

.nav-cart-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  color: #0a0a0a;
  text-decoration: none;
  transition: color .2s, transform .15s;
  flex-shrink: 0;
}

.nav-cart-btn:hover { color: #c4a97a; transform: scale(1.1); }

.nav-actions a.nav-link-plain {
  font-size: .75rem;
  font-weight: 500;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: #3a3730;
  text-decoration: none;
  transition: color .2s;
}

.nav-actions a.nav-link-plain:hover { color: #c4a97a; }

.nav-btn {
  display: inline-flex;
  align-items: center;
  padding: .38rem 1rem;
  font-size: .72rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  border-radius: 4px;
  text-decoration: none;
  transition: all .2s;
  white-space: nowrap;
}

.nav-btn-outline {
  border: 1.5px solid #0a0a0a;
  color: #0a0a0a;
  background: transparent;
}

.nav-btn-outline:hover { background: #0a0a0a; color: #fff; }

.nav-btn-solid {
  background: #0a0a0a;
  color: #fff;
  border: 1.5px solid #0a0a0a;
}

.nav-btn-solid:hover { background: #2a2a2a; border-color: #2a2a2a; }

/* ── Hamburger (mobile only) ── */
.nav-hamburger {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: .2rem;
  color: #0a0a0a;
  flex-shrink: 0;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 30px;
  height: 30px;
}

.nav-hamburger span {
  display: block;
  width: 100%;
  height: 1.5px;
  background: currentColor;
  transform-origin: center;
  transition: transform .3s ease, opacity .3s ease;
}

.nav-hamburger.open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
.nav-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.nav-hamburger.open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

/* ── Full-screen mobile overlay ── */
.nav-mobile-overlay {
  position: fixed;
  inset: 0;
  z-index: 499;
  background: #0a0a0a;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s ease;
  padding: 5rem 2rem 3rem;
}

.nav-mobile-overlay.open {
  opacity: 1;
  pointer-events: auto;
}

.nav-mobile-overlay > a {
  font-family: Georgia, 'Times New Roman', serif;
  font-size: clamp(1.8rem, 6vw, 2.6rem);
  color: rgba(255,255,255,.88);
  letter-spacing: .04em;
  text-decoration: none;
  transition: color .2s;
}

.nav-mobile-overlay > a:hover { color: #f5f0e8; }

.nav-mobile-overlay .mob-search {
  width: 100%;
  max-width: 320px;
  position: relative;
}

.nav-mobile-overlay .mob-search-input {
  width: 100%;
  height: 42px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 21px;
  padding: 0 1rem 0 2.4rem;
  font-size: .85rem;
  color: #fff;
  outline: none;
  transition: border-color .2s;
}

.nav-mobile-overlay .mob-search-input::placeholder { color: rgba(255,255,255,.35); }
.nav-mobile-overlay .mob-search-input:focus { border-color: rgba(196,169,122,.6); }

.nav-mobile-overlay .mob-search-icon {
  position: absolute;
  left: .8rem;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255,255,255,.35);
  pointer-events: none;
}

.nav-mobile-overlay .mob-ac-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  left: 0; right: 0;
  background: #1a1a1a;
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 10px;
  overflow: hidden;
  z-index: 9999;
  display: none;
}

.nav-mobile-overlay .mob-ac-dropdown.open { display: block; }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-item { color: #fff; }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-item:hover { background: rgba(255,255,255,.07); }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-price { color: #c4a97a; }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-footer { color: #c4a97a; border-color: rgba(255,255,255,.08); }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-footer:hover { background: rgba(255,255,255,.05); }
.nav-mobile-overlay .mob-ac-dropdown .nav-ac-empty { color: rgba(255,255,255,.4); }

.mob-divider {
  width: 40px;
  height: 1px;
  background: rgba(255,255,255,.1);
}

.nav-mobile-auth {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .8rem;
  width: 100%;
  max-width: 240px;
}

.nav-mobile-auth a {
  display: block;
  width: 100%;
  text-align: center;
  padding: .65rem 1.5rem;
  font-size: .72rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  font-weight: 600;
  border-radius: 4px;
  text-decoration: none;
  transition: all .2s;
}

.mob-auth-outline {
  color: rgba(255,255,255,.7);
  border: 1px solid rgba(255,255,255,.22);
}

.mob-auth-outline:hover { color: #fff; border-color: #fff; }

.mob-auth-solid { background: #fff; color: #0a0a0a; }
.mob-auth-solid:hover { background: #f5f0e8; }

.mob-auth-ghost {
  color: rgba(255,255,255,.45);
  font-size: .7rem;
  letter-spacing: .1em;
}

.mob-auth-ghost:hover { color: rgba(255,255,255,.75); }

/* Mobile group hidden on desktop */
.nav-mobile-group {
  display: none;
  align-items: center;
  gap: .5rem;
  margin-left: auto;
  flex-shrink: 0;
}

@media (max-width: 768px) {
  .nav-links { display: none; }
  .nav-search { display: none; }
  .nav-actions-desktop { display: none; }
  .nav-mobile-group { display: flex; }
  .nav-hamburger { display: flex; }
}
</style>

<header class="site-header">
    <nav class="navbar" role="navigation" aria-label="Main navigation">

        <a class="navbar-brand" href="<?= url() ?>"><?= e(APP_NAME) ?></a>

        <ul class="nav-links">
            <li><a href="<?= url() ?>">Home</a></li>
            <li><a href="<?= url('products') ?>">Shop</a></li>
        </ul>

        <!-- Desktop search -->
        <div class="nav-search" id="navSearch">
            <svg class="nav-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="nav-search-input" id="navSearchInput"
                   placeholder="Search products…" autocomplete="off"
                   aria-label="Search products">
            <div class="nav-ac-dropdown" id="navAcDropdown" role="listbox"></div>
        </div>

        <!-- Desktop actions -->
        <div class="nav-actions nav-actions-desktop">
            <a href="<?= url('cart') ?>" class="nav-cart-btn" aria-label="Cart">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
            </a>

            <?php if (Session::isLoggedIn()): ?>
                <a href="<?= url('account/wishlist') ?>" class="nav-link-plain">Wishlist</a>
                <a href="<?= url('account') ?>" class="nav-link-plain">Account</a>
                <a href="<?= url('logout') ?>" class="nav-link-plain">Logout</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="nav-btn nav-btn-outline">Login</a>
                <a href="<?= url('register') ?>" class="nav-btn nav-btn-solid">Register</a>
            <?php endif; ?>
        </div>

        <!-- Mobile group: cart + hamburger (hidden on desktop via CSS) -->
        <div class="nav-mobile-group" id="navMobileGroup">
            <a href="<?= url('cart') ?>" class="nav-cart-btn" aria-label="Cart">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
            </a>
            <button class="nav-hamburger" id="navHamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>

    </nav>
</header>

<!-- Full-screen mobile overlay -->
<div class="nav-mobile-overlay" id="mobileOverlay" role="dialog" aria-label="Mobile navigation">

    <!-- Search -->
    <div class="mob-search" id="mobSearchWrap">
        <svg class="mob-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" class="mob-search-input" id="mobSearchInput"
               placeholder="Search products…" autocomplete="off"
               aria-label="Search">
        <div class="mob-ac-dropdown nav-ac-dropdown" id="mobAcDropdown" role="listbox"></div>
    </div>

    <a href="<?= url() ?>" onclick="closeMobMenu()">Home</a>
    <a href="<?= url('products') ?>" onclick="closeMobMenu()">Shop</a>

    <div class="mob-divider"></div>

    <div class="nav-mobile-auth">
        <?php if (Session::isLoggedIn()): ?>
            <a href="<?= url('account/wishlist') ?>" class="mob-auth-ghost" onclick="closeMobMenu()">Wishlist</a>
            <a href="<?= url('account') ?>" class="mob-auth-ghost" onclick="closeMobMenu()">My Account</a>
            <a href="<?= url('logout') ?>" class="mob-auth-outline" onclick="closeMobMenu()">Logout</a>
        <?php else: ?>
            <a href="<?= url('login') ?>" class="mob-auth-outline" onclick="closeMobMenu()">Login</a>
            <a href="<?= url('register') ?>" class="mob-auth-solid" onclick="closeMobMenu()">Create Account</a>
        <?php endif; ?>
    </div>

</div>

<script>
(function () {
    var acUrl    = '<?= url('search/autocomplete') ?>';
    var searchUrl = '<?= url('search') ?>';

    /* ── Shared autocomplete factory ── */
    function initAC(input, dropdown, container) {
        if (!input || !dropdown) return;
        var timer = null, lastQ = '', hi = -1;

        function open()  { dropdown.classList.add('open'); }
        function close() { dropdown.classList.remove('open'); hi = -1; }

        function render(items, q) {
            dropdown.innerHTML = '';
            if (!items.length) {
                dropdown.innerHTML = '<div class="nav-ac-empty">No results for "' + q + '"</div>';
                open(); return;
            }
            items.forEach(function (p) {
                var a = document.createElement('a');
                a.className = 'nav-ac-item';
                a.href = p.url;
                a.setAttribute('role', 'option');
                a.innerHTML = (p.image
                    ? '<img class="nav-ac-img" src="' + p.image + '" alt="' + p.name + '" loading="lazy">'
                    : '<div class="nav-ac-img-placeholder"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div>')
                    + '<span class="nav-ac-name">' + p.name + '</span>'
                    + '<span class="nav-ac-price">' + p.price + '</span>';
                dropdown.appendChild(a);
            });
            var f = document.createElement('div');
            f.className = 'nav-ac-footer';
            f.textContent = 'See all results for "' + q + '"';
            f.onclick = function () { go(q); };
            dropdown.appendChild(f);
            hi = -1; open();
        }

        function go(q) { window.location.href = searchUrl + '?q=' + encodeURIComponent(q); }

        function doFetch(q) {
            if (q === lastQ) return;
            lastQ = q;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', acUrl + '?q=' + encodeURIComponent(q), true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    try { render(JSON.parse(xhr.responseText), q); } catch(e) { close(); }
                }
            };
            xhr.send();
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                var q = input.value.trim();
                if (q.length < 2) { close(); lastQ = ''; return; }
                doFetch(q);
            }, 280);
        });

        input.addEventListener('keydown', function (e) {
            var items = dropdown.querySelectorAll('.nav-ac-item');
            if (e.key === 'ArrowDown') { e.preventDefault(); hi = Math.min(hi+1, items.length-1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); hi = Math.max(hi-1, -1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                if (hi >= 0 && items[hi]) window.location.href = items[hi].href;
                else go(input.value.trim());
                return;
            } else if (e.key === 'Escape') { close(); return; }
            items.forEach(function (el, i) { el.classList.toggle('highlighted', i === hi); });
        });

        document.addEventListener('click', function (e) {
            if (container && !container.contains(e.target)) close();
        });
    }

    initAC(
        document.getElementById('navSearchInput'),
        document.getElementById('navAcDropdown'),
        document.getElementById('navSearch')
    );
    initAC(
        document.getElementById('mobSearchInput'),
        document.getElementById('mobAcDropdown'),
        document.getElementById('mobSearchWrap')
    );

    /* ── Mobile overlay toggle ── */
    var hamburger = document.getElementById('navHamburger');
    var overlay   = document.getElementById('mobileOverlay');

    window.closeMobMenu = function () {
        overlay.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    if (hamburger && overlay) {
        hamburger.addEventListener('click', function () {
            var isOpen = overlay.classList.toggle('open');
            hamburger.classList.toggle('open', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) closeMobMenu();
    });
})();
</script>
