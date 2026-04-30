<!-- app/views/partials/footer.php -->
<style>
.site-footer {
  background: #0a0a0a;
  color: #ccc;
  padding: 3rem 0 1.5rem;
  margin-top: 4rem;
}
.footer-inner {
  max-width: var(--container-max, 1280px);
  margin: 0 auto;
  padding: 0 var(--container-pad, 1.5rem);
}
.footer-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 2.5rem;
}
.footer-brand-name {
  font-family: Georgia, serif;
  font-size: 1.25rem;
  color: #fff;
  margin-bottom: .5rem;
}
.footer-tagline {
  font-size: .82rem;
  color: #888;
  line-height: 1.6;
  max-width: 260px;
}

/* Newsletter */
.footer-nl-title {
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: #c4a97a;
  margin-bottom: .6rem;
}
.footer-nl-desc {
  font-size: .82rem;
  color: #888;
  margin-bottom: 1rem;
  line-height: 1.5;
}
.footer-nl-form {
  display: flex;
  gap: .5rem;
}
.footer-nl-input {
  flex: 1;
  height: 40px;
  border: 1.5px solid #333;
  border-radius: 8px;
  background: #1a1a1a;
  color: #fff;
  padding: 0 .9rem;
  font-size: .84rem;
  outline: none;
  transition: border-color .2s;
}
.footer-nl-input::placeholder { color: #555; }
.footer-nl-input:focus { border-color: #c4a97a; }
.footer-nl-btn {
  height: 40px;
  padding: 0 1.1rem;
  background: #c4a97a;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: .8rem;
  font-weight: 700;
  letter-spacing: .05em;
  cursor: pointer;
  white-space: nowrap;
  transition: background .2s;
}
.footer-nl-btn:hover { background: #a88a5a; }

.footer-bottom {
  border-top: 1px solid #1e1e1e;
  padding-top: 1.25rem;
  font-size: .78rem;
  color: #555;
  text-align: center;
}

@media (max-width: 640px) {
  .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
  .footer-nl-form { flex-direction: column; }
}
</style>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">

            <!-- Brand -->
            <div>
                <div class="footer-brand-name"><?= e(APP_NAME) ?></div>
                <p class="footer-tagline">Premium fashion delivered to your door. Quality you can feel, style you can trust.</p>
            </div>

            <!-- Newsletter -->
            <div>
                <div class="footer-nl-title">Stay in the loop</div>
                <p class="footer-nl-desc">New arrivals, exclusive deals — no spam, unsubscribe anytime.</p>
                <form method="POST" action="<?= url('newsletter/subscribe') ?>" class="footer-nl-form">
                    <?= csrfField() ?>
                    <input type="email" name="email" class="footer-nl-input"
                           placeholder="your@email.com" required maxlength="180">
                    <button type="submit" class="footer-nl-btn">Subscribe</button>
                </form>
            </div>

        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.
        </div>
    </div>
</footer>
