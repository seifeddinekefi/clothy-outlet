<!-- app/views/partials/navbar.php -->
<style>
.site-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: #fff;
  border-bottom: 1px solid #e8e6e2;
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.navbar {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  max-width: var(--container-max, 1280px);
  margin: 0 auto;
  padding: 0 var(--container-pad, 1.5rem);
  height: var(--nav-height, 72px);
}
.navbar-brand {
  font-family: Georgia, serif;
  font-size: 1.2rem;
  font-weight: 700;
  color: #0a0a0a;
  text-decoration: none;
  white-space: nowrap;
  flex-shrink: 0;
}
.nav-links {
  display: flex;
  list-style: none;
  gap: 1.5rem;
  flex-shrink: 0;
}
.nav-links a {
  font-size: .83rem;
  font-weight: 600;
  letter-spacing: .06em;
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
  max-width: 420px;
  margin: 0 auto;
}
.nav-search-input {
  width: 100%;
  height: 38px;
  border: 1.5px solid #e0deda;
  border-radius: 20px;
  padding: 0 1rem 0 2.4rem;
  font-size: .85rem;
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

/* ── Autocomplete dropdown ── */
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
  cursor: pointer;
}
.nav-ac-item:hover, .nav-ac-item.highlighted { background: #faf9f7; }
.nav-ac-img {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  object-fit: cover;
  background: #f0ede8;
  flex-shrink: 0;
}
.nav-ac-img-placeholder {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  background: #f0ede8;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ccc;
}
.nav-ac-name {
  font-size: .85rem;
  font-weight: 600;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.nav-ac-price {
  font-size: .8rem;
  color: #7a7570;
  flex-shrink: 0;
}
.nav-ac-footer {
  padding: .55rem 1rem;
  font-size: .78rem;
  color: #c4a97a;
  text-align: center;
  border-top: 1px solid #f0ede8;
  cursor: pointer;
}
.nav-ac-footer:hover { background: #faf9f7; }
.nav-ac-empty {
  padding: 1rem;
  text-align: center;
  font-size: .83rem;
  color: #aaa;
}

/* ── Nav actions ── */
.nav-actions {
  display: flex;
  align-items: center;
  gap: .75rem;
  flex-shrink: 0;
  font-size: .83rem;
}
.nav-actions a {
  color: #3a3730;
  text-decoration: none;
  font-weight: 500;
  transition: color .2s;
}
.nav-actions a:hover { color: #c4a97a; }
.btn-cart {
  font-size: 1.1rem;
  text-decoration: none;
}
.btn { border-radius: 20px; padding: .38rem .9rem; font-size: .8rem; font-weight: 600; text-decoration: none; transition: all .2s; }
.btn-outline { border: 1.5px solid #0a0a0a; color: #0a0a0a; }
.btn-outline:hover { background: #0a0a0a; color: #fff; }
.btn-primary { background: #0a0a0a; color: #fff; border: 1.5px solid #0a0a0a; }
.btn-primary:hover { background: #2a2a2a; border-color: #2a2a2a; }

@media (max-width: 768px) {
  .nav-links { display: none; }
  .nav-search { max-width: none; }
}
</style>

<header class="site-header">
    <nav class="navbar" role="navigation" aria-label="Main navigation">

        <a class="navbar-brand" href="<?= url() ?>"><?= e(APP_NAME) ?></a>

        <ul class="nav-links">
            <li><a href="<?= url() ?>">Home</a></li>
            <li><a href="<?= url('products') ?>">Shop</a></li>
        </ul>

        <!-- Search bar -->
        <div class="nav-search" id="navSearch">
            <svg class="nav-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="nav-search-input" id="navSearchInput"
                   placeholder="Search products…" autocomplete="off"
                   aria-label="Search products" aria-autocomplete="list"
                   aria-controls="navAcDropdown">
            <div class="nav-ac-dropdown" id="navAcDropdown" role="listbox"></div>
        </div>

        <div class="nav-actions">
            <a href="<?= url('cart') ?>" class="btn-cart" aria-label="Cart">🛒</a>

            <?php if (Session::isLoggedIn()): ?>
                <a href="<?= url('account/wishlist') ?>">Wishlist</a>
                <a href="<?= url('account') ?>">My Account</a>
                <a href="<?= url('logout') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-outline">Login</a>
                <a href="<?= url('register') ?>" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>

    </nav>
</header>

<script>
(function () {
    var input      = document.getElementById('navSearchInput');
    var dropdown   = document.getElementById('navAcDropdown');
    var acUrl      = '<?= url('search/autocomplete') ?>';
    var searchUrl  = '<?= url('search') ?>';
    var timer      = null;
    var lastQ      = '';
    var highlighted = -1;

    if (!input) return;

    function debounce(fn, ms) {
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn, ms);
        };
    }

    function open()  { dropdown.classList.add('open'); }
    function close() { dropdown.classList.remove('open'); highlighted = -1; }

    function renderResults(items, q) {
        dropdown.innerHTML = '';
        if (items.length === 0) {
            dropdown.innerHTML = '<div class="nav-ac-empty">No products found for "' + q + '"</div>';
            open();
            return;
        }
        items.forEach(function (p, i) {
            var a = document.createElement('a');
            a.className  = 'nav-ac-item';
            a.href       = p.url;
            a.setAttribute('role', 'option');
            var imgHtml = p.image
                ? '<img class="nav-ac-img" src="' + p.image + '" alt="' + p.name + '" loading="lazy">'
                : '<div class="nav-ac-img-placeholder"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
            a.innerHTML = imgHtml +
                '<span class="nav-ac-name">' + p.name + '</span>' +
                '<span class="nav-ac-price">' + p.price + '</span>';
            dropdown.appendChild(a);
        });
        var footer = document.createElement('div');
        footer.className = 'nav-ac-footer';
        footer.textContent = 'See all results for "' + q + '"';
        footer.addEventListener('click', function () { goSearch(q); });
        dropdown.appendChild(footer);
        highlighted = -1;
        open();
    }

    function goSearch(q) {
        window.location.href = searchUrl + '?q=' + encodeURIComponent(q);
    }

    function fetch(q) {
        if (q === lastQ) return;
        lastQ = q;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', acUrl + '?q=' + encodeURIComponent(q), true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                try { renderResults(JSON.parse(xhr.responseText), q); }
                catch (e) { close(); }
            }
        };
        xhr.send();
    }

    input.addEventListener('input', debounce(function () {
        var q = input.value.trim();
        if (q.length < 2) { close(); lastQ = ''; return; }
        fetch(q);
    }, 280));

    input.addEventListener('keydown', function (e) {
        var items = dropdown.querySelectorAll('.nav-ac-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlighted = Math.min(highlighted + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlighted = Math.max(highlighted - 1, -1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (highlighted >= 0 && items[highlighted]) {
                window.location.href = items[highlighted].href;
            } else {
                goSearch(input.value.trim());
            }
            return;
        } else if (e.key === 'Escape') {
            close(); return;
        }
        items.forEach(function (el, i) {
            el.classList.toggle('highlighted', i === highlighted);
        });
    });

    document.addEventListener('click', function (e) {
        if (!document.getElementById('navSearch').contains(e.target)) close();
    });
})();
</script>
