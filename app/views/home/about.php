<?php
/**
 * app/views/home/about.php
 * About us page.
 */
?>
<?php $view->startSection('head') ?>
<style>
  .about-page { padding-top: calc(72px + 0px); }
  .about-hero { background: #0a0a0a; padding: 5rem 0 4rem; text-align: center; color: #fff; }
  .about-hero-label { font-size: .7rem; letter-spacing: .28em; text-transform: uppercase; color: #c4a97a; display: block; margin-bottom: .75rem; }
  .about-hero h1 { font-family: Georgia, serif; font-size: clamp(2.5rem, 6vw, 4rem); font-weight: normal; }
  .about-hero p  { font-size: .95rem; color: rgba(255,255,255,.45); margin-top: .75rem; max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.7; }

  .about-body { padding: 5rem 0; }
  .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; margin-bottom: 5rem; }
  .about-grid.reverse { direction: rtl; }
  .about-grid.reverse > * { direction: ltr; }
  .about-visual { aspect-ratio: 4/3; border-radius: 10px; }
  .bg-stone { background: linear-gradient(145deg,#d4c4b0,#c4b09a,#b09080); }
  .bg-dark  { background: linear-gradient(145deg,#303845,#283040,#202830); }
  .about-text-label { font-size: .7rem; letter-spacing: .25em; text-transform: uppercase; color: #c4a97a; display: block; margin-bottom: .75rem; }
  .about-text h2 { font-family: Georgia, serif; font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: normal; margin-bottom: 1rem; }
  .about-text p  { font-size: .9rem; color: #7a7570; line-height: 1.8; margin-bottom: .9rem; }

  .values-section { background: #fafaf9; padding: 4rem 0; }
  .values-title { text-align: center; margin-bottom: 3rem; }
  .values-title h2 { font-family: Georgia, serif; font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: normal; }
  .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; }
  .value-card  { background: #fff; border: 1px solid #e8e6e2; border-radius: 8px; padding: 2rem; text-align: center; }
  .value-icon  { width: 52px; height: 52px; border: 1px solid rgba(0,0,0,.12); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin: 0 auto 1rem; }
  .value-card h3 { font-family: inherit; font-size: 1rem; font-weight: 600; margin-bottom: .5rem; }
  .value-card p  { font-size: .85rem; color: #7a7570; line-height: 1.7; }

  @media (max-width: 768px) {
    .about-grid   { grid-template-columns: 1fr; gap: 2rem; }
    .about-grid.reverse { direction: ltr; }
    .values-grid  { grid-template-columns: 1fr; }
  }
</style>
<?php $view->endSection() ?>

<div class="about-page">

  <div class="about-hero">
    <div class="container">
      <span class="about-hero-label">Our Story</span>
      <h1>Fashion with Purpose</h1>
      <p>We believe great style shouldn't compromise on ethics, quality, or the environment.</p>
    </div>
  </div>

  <div class="about-body">
    <div class="container">

      <div class="about-grid">
        <div class="about-visual bg-stone"></div>
        <div class="about-text">
          <span class="about-text-label">Who We Are</span>
          <h2>Born from a love of minimal fashion</h2>
          <p>Clothy Outlet was founded in 2020 with one simple belief: that clothing should feel as good as it looks. We curate pieces that are timeless, versatile, and crafted with genuine care.</p>
          <p>From our first collection of five wardrobe essentials, we've grown into a full lifestyle brand — but we've never lost sight of what matters: quality over quantity.</p>
        </div>
      </div>

      <div class="about-grid reverse">
        <div class="about-visual bg-dark"></div>
        <div class="about-text">
          <span class="about-text-label">Our Process</span>
          <h2>Made to last, not to be replaced</h2>
          <p>Every fabric is hand-selected from ethical suppliers. Every seam is reviewed by our quality team. We work with small-batch manufacturers who share our values and pay fair wages.</p>
          <p>The result? Pieces you'll reach for season after season, not just till the next trend cycle.</p>
        </div>
      </div>

    </div>
  </div>

  <div class="values-section">
    <div class="container">
      <div class="values-title"><h2>What We Stand For</h2></div>
      <div class="values-grid">
        <div class="value-card">
          <div class="value-icon">◈</div>
          <h3>Ethical Sourcing</h3>
          <p>We only partner with suppliers who meet our strict social and environmental standards.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">✦</div>
          <h3>Premium Quality</h3>
          <p>Natural fabrics, precise tailoring, and finishes that improve with every wash.</p>
        </div>
        <div class="value-card">
          <div class="value-icon">◉</div>
          <h3>Transparent Pricing</h3>
          <p>No markups for hype. You pay for the garment, not the brand tax.</p>
        </div>
      </div>
    </div>
  </div>

</div>
