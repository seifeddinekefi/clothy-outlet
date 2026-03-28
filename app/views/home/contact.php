<?php
/**
 * app/views/home/contact.php
 * Contact page.
 */
?>
<?php $view->startSection('head') ?>
<style>
  .contact-page { padding-top: calc(72px + 0px); }
  .contact-hero { background: #0a0a0a; padding: 4.5rem 0 3.5rem; text-align: center; color: #fff; }
  .contact-hero-label { font-size: .7rem; letter-spacing: .28em; text-transform: uppercase; color: #c4a97a; display: block; margin-bottom: .75rem; }
  .contact-hero h1 { font-family: Georgia, serif; font-size: clamp(2.25rem, 5vw, 3.5rem); font-weight: normal; }
  .contact-hero p  { font-size: .9rem; color: rgba(255,255,255,.4); margin-top: .65rem; }

  .contact-body { padding: 4rem 0 5rem; background: #fafaf9; }
  .contact-layout { display: grid; grid-template-columns: 1fr 1.4fr; gap: 3.5rem; align-items: start; }

  .contact-info h2 { font-family: Georgia, serif; font-size: 1.5rem; font-weight: normal; margin-bottom: .5rem; }
  .contact-info .lead { font-size: .875rem; color: #7a7570; margin-bottom: 2rem; line-height: 1.7; }
  .contact-item { display: flex; gap: .75rem; margin-bottom: 1.25rem; align-items: flex-start; }
  .contact-item-icon { width: 36px; height: 36px; border: 1px solid #e8e6e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; background: #fff; }
  .contact-item-body h4 { font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #7a7570; margin-bottom: .2rem; }
  .contact-item-body p  { font-size: .875rem; color: #3a3730; }

  .contact-form-card { background: #fff; border: 1px solid #e8e6e2; border-radius: 10px; padding: 2rem; }
  .contact-form-title { font-size: .7rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: #7a7570; margin-bottom: 1.5rem; border-bottom: 1px solid #e8e6e2; padding-bottom: .75rem; }
  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
  .form-group { margin-bottom: 1.1rem; }
  .form-label { display: block; font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #7a7570; margin-bottom: .45rem; }
  .form-input, .form-textarea {
    display: block; width: 100%; padding: .78rem .95rem;
    border: 1.5px solid #e8e6e2; border-radius: 6px; font-size: .9rem;
    font-family: inherit; transition: border-color .15s, box-shadow .15s;
  }
  .form-input:focus, .form-textarea:focus { border-color: #0a0a0a; box-shadow: 0 0 0 3px rgba(10,10,10,.05); outline: none; }
  .form-textarea { min-height: 130px; resize: vertical; }
  .btn-send {
    padding: .88rem 2.5rem; background: #0a0a0a; color: #fff;
    font-size: .75rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
    border: none; border-radius: 6px; cursor: pointer; transition: background .2s;
  }
  .btn-send:hover { background: #3a3730; }

  @media (max-width: 768px) {
    .contact-layout { grid-template-columns: 1fr; }
    .form-row-2     { grid-template-columns: 1fr; }
  }
</style>
<?php $view->endSection() ?>

<div class="contact-page">

  <div class="contact-hero">
    <div class="container">
      <span class="contact-hero-label">Get in Touch</span>
      <h1>We'd Love to Hear From You</h1>
      <p>Questions, feedback, or just want to say hello — we're here.</p>
    </div>
  </div>

  <div class="contact-body">
    <div class="container">
      <div class="contact-layout">

        <div class="contact-info">
          <h2>Let's talk fashion</h2>
          <p class="lead">Whether you need help with your order, want to collaborate, or simply want to share your style story — reach out anytime.</p>

          <div class="contact-item">
            <div class="contact-item-icon">✉</div>
            <div class="contact-item-body">
              <h4>Email</h4>
              <p>hello@clothyoutlet.com</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon">☎</div>
            <div class="contact-item-body">
              <h4>Phone</h4>
              <p>+1 (555) 001-0000</p>
              <p style="font-size:.78rem;color:#7a7570">Mon–Fri, 9am–6pm EST</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon">⊕</div>
            <div class="contact-item-body">
              <h4>Showroom</h4>
              <p>123 Fashion Ave, New York, NY 10001</p>
            </div>
          </div>
        </div>

        <div class="contact-form-card">
          <div class="contact-form-title">Send us a message</div>

          <form method="POST" action="<?= url('contact') ?>">
            <?= csrfField() ?>

            <div class="form-row-2">
              <div class="form-group">
                <label class="form-label" for="name">Your Name</label>
                <input class="form-input" type="text" id="name" name="name" required placeholder="Jane Doe">
              </div>
              <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email" name="email" required placeholder="you@example.com">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="subject">Subject</label>
              <input class="form-input" type="text" id="subject" name="subject" placeholder="Order question, feedback…">
            </div>

            <div class="form-group">
              <label class="form-label" for="message">Message</label>
              <textarea class="form-textarea" id="message" name="message" required placeholder="Tell us how we can help…"></textarea>
            </div>

            <button class="btn-send" type="submit">Send Message</button>
          </form>
        </div>

      </div>
    </div>
  </div>

</div>
