<?php

/**
 * app/views/home/index.php
 * Landing page — fully self-contained (HTML + CSS + JS).
 * Bypasses the main layout so this file is the complete document.
 */
$view->setLayout('');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a0a0a">
    <title><?= defined('APP_NAME') ? e(APP_NAME) : 'Clothy Outlet' ?> — Modern Fashion</title>
    <meta name="description" content="Discover premium clothing at Clothy Outlet — minimal, modern, elegant.">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <!-- Preload above-fold product images -->
    <link rel="preload" as="image" type="image/jpeg" href="<?= asset('images/products/blazer.jpg') ?>" fetchpriority="high">
    <link rel="preload" as="image" type="image/jpeg" href="<?= asset('images/products/trousers.jpg') ?>" fetchpriority="high">
    <link rel="preload" as="image" type="image/jpeg" href="<?= asset('images/products/tee.jpg') ?>" fetchpriority="high">
    <link rel="preload" as="image" type="image/jpeg" href="<?= asset('images/products/dress.jpg') ?>" fetchpriority="high">
    <style>
        /* ============================================================
       CSS VARIABLES
       ============================================================ */
        :root {
            --clr-black: #0a0a0a;
            --clr-white: #ffffff;
            --clr-beige: #f5f0e8;
            --clr-beige-dk: #e8dfd0;
            --clr-gray-50: #fafaf9;
            --clr-gray-100: #f4f3f1;
            --clr-gray-200: #e8e6e2;
            --clr-gray-400: #b0aca6;
            --clr-gray-600: #7a7570;
            --clr-gray-800: #3a3730;
            --clr-accent: #c4a97a;
            --clr-accent-dk: #a88a5a;
            --clr-danger: #c45a5a;

            --ff-heading: Georgia, 'Times New Roman', serif;
            --ff-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;

            --fz-xs: 0.75rem;
            --fz-sm: 0.875rem;
            --fz-base: 1rem;
            --fz-md: 1.125rem;
            --fz-lg: 1.5rem;
            --fz-xl: 2rem;
            --fz-2xl: 2.5rem;
            --fz-3xl: 3.5rem;
            --fz-4xl: 5rem;

            --sp-xs: 0.25rem;
            --sp-sm: 0.5rem;
            --sp-md: 1rem;
            --sp-lg: 1.5rem;
            --sp-xl: 2rem;
            --sp-2xl: 3rem;
            --sp-3xl: 5rem;

            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 16px;
            --radius-full: 9999px;

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, .10);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, .14);
            --shadow-xl: 0 20px 60px rgba(0, 0, 0, .18);

            --transition: 0.3s ease;
            --transition-fast: 0.15s ease;

            --nav-height: 72px;
            --container-max: 1280px;
            --container-pad: clamp(1rem, 5vw, 2.5rem);
        }

        /* ============================================================
       RESET
       ============================================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: var(--ff-body);
            background: var(--clr-white);
            color: var(--clr-black);
            line-height: 1.65;
            overflow-x: hidden;
        }

        img {
            max-width: 100%;
            display: block;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color var(--transition-fast);
        }

        ul,
        ol {
            list-style: none;
        }

        button {
            cursor: pointer;
            border: none;
            background: none;
            font-family: inherit;
        }

        input,
        select,
        textarea {
            font-family: inherit;
            outline: none;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: var(--ff-heading);
            font-weight: normal;
            line-height: 1.2;
        }

        /* ============================================================
       UTILITIES
       ============================================================ */
        .container {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 var(--container-pad);
        }

        .section {
            padding: var(--sp-3xl) 0;
        }

        .section-label {
            display: block;
            font-size: var(--fz-xs);
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--clr-accent);
            margin-bottom: var(--sp-sm);
        }

        .section-title {
            font-size: clamp(var(--fz-xl), 4vw, var(--fz-2xl));
        }

        .section-link {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--clr-gray-600);
            transition: color var(--transition);
        }

        .section-link::after {
            content: '→';
        }

        .section-link:hover {
            color: var(--clr-black);
        }

        /* Scroll fade-in */
        .fade-in {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .6s ease, transform .6s ease;
        }

        .fade-in.s1 {
            transition-delay: .10s;
        }

        .fade-in.s2 {
            transition-delay: .20s;
        }

        .fade-in.s3 {
            transition-delay: .30s;
        }

        .fade-in.s4 {
            transition-delay: .40s;
        }

        .fade-in.s5 {
            transition-delay: .50s;
        }

        .fade-in.s6 {
            transition-delay: .60s;
        }

        .fade-in.s7 {
            transition-delay: .70s;
        }

        .fade-in.s8 {
            transition-delay: .80s;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.75rem;
            font-size: var(--fz-xs);
            letter-spacing: .12em;
            text-transform: uppercase;
            font-family: var(--ff-body);
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .13);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .btn:hover::after {
            opacity: 1;
        }

        .btn--primary {
            background: var(--clr-black);
            color: var(--clr-white);
        }

        .btn--primary:hover {
            background: var(--clr-gray-800);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn--outline {
            background: transparent;
            color: var(--clr-black);
            border: 1.5px solid var(--clr-black);
        }

        .btn--outline:hover {
            background: var(--clr-black);
            color: var(--clr-white);
        }

        .btn--ghost {
            background: transparent;
            color: var(--clr-white);
            border: 1.5px solid rgba(255, 255, 255, .55);
        }

        .btn--ghost:hover {
            background: rgba(255, 255, 255, .12);
            border-color: var(--clr-white);
        }

        .btn--accent {
            background: var(--clr-accent);
            color: var(--clr-white);
        }

        .btn--accent:hover {
            background: var(--clr-accent-dk);
            transform: translateY(-2px);
        }

        .btn--lg {
            padding: 1rem 2.25rem;
            font-size: var(--fz-sm);
        }

        .btn--sm {
            padding: .5rem 1.2rem;
            font-size: var(--fz-xs);
        }

        .btn--full {
            width: 100%;
            justify-content: center;
        }

        /* ============================================================
       NAVBAR
       ============================================================ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: var(--nav-height);
            background: transparent;
            transition: background var(--transition), box-shadow var(--transition);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 0 rgba(0, 0, 0, .07);
        }

        .nav-container {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 var(--container-pad);
            height: 100%;
            display: flex;
            align-items: center;
            gap: var(--sp-xl);
        }

        .nav-logo {
            font-family: var(--ff-heading);
            font-size: 1.4rem;
            font-weight: bold;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--clr-white);
            transition: color var(--transition-fast);
            flex-shrink: 0;
        }

        .navbar.scrolled .nav-logo {
            color: var(--clr-black);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: var(--sp-xl);
            margin-left: var(--sp-lg);
            flex: 1;
        }

        .nav-menu a {
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .8);
            position: relative;
            padding-bottom: 2px;
            transition: color var(--transition-fast);
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: currentColor;
            transition: width var(--transition);
        }

        .nav-menu a:hover {
            color: rgba(255, 255, 255, 1);
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .navbar.scrolled .nav-menu a {
            color: var(--clr-gray-800);
        }

        .navbar.scrolled .nav-menu a:hover {
            color: var(--clr-black);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: var(--sp-md);
            margin-left: auto;
        }

        .btn-cart {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: var(--clr-white);
            text-decoration: none;
            transition: color var(--transition-fast), transform var(--transition-fast);
        }

        .btn-cart svg {
            pointer-events: none;
        }

        .btn-cart:hover {
            transform: scale(1.1);
        }

        .navbar.scrolled .btn-cart {
            color: var(--clr-black);
        }

        .cart-badge {
            position: absolute;
            top: 2px;
            right: 1px;
            width: 16px;
            height: 16px;
            background: var(--clr-accent);
            color: var(--clr-white);
            font-size: 9px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0);
            transition: opacity var(--transition-fast), transform var(--transition-fast);
        }

        .cart-badge.active {
            opacity: 1;
            transform: scale(1);
        }

        .nav-btn-login {
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .45rem 1.1rem;
            border-radius: var(--radius-sm);
            color: rgba(255, 255, 255, .8);
            border: 1px solid rgba(255, 255, 255, .35);
            transition: all var(--transition-fast);
        }

        .nav-btn-login:hover {
            color: var(--clr-white);
            border-color: var(--clr-white);
        }

        .navbar.scrolled .nav-btn-login {
            color: var(--clr-gray-800);
            border-color: var(--clr-gray-200);
        }

        .navbar.scrolled .nav-btn-login:hover {
            color: var(--clr-black);
            border-color: var(--clr-black);
        }

        .nav-btn-register {
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .45rem 1.1rem;
            border-radius: var(--radius-sm);
            background: var(--clr-white);
            color: var(--clr-black);
            transition: all var(--transition-fast);
        }

        .nav-btn-register:hover {
            background: var(--clr-beige);
        }

        .navbar.scrolled .nav-btn-register {
            background: var(--clr-black);
            color: var(--clr-white);
        }

        .navbar.scrolled .nav-btn-register:hover {
            background: var(--clr-gray-800);
        }

        /* Hamburger */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 32px;
            height: 32px;
            padding: 2px;
            color: var(--clr-white);
        }

        .navbar.scrolled .nav-hamburger {
            color: var(--clr-black);
        }

        .nav-hamburger span {
            display: block;
            width: 100%;
            height: 1.5px;
            background: currentColor;
            transform-origin: center;
            transition: transform var(--transition), opacity var(--transition);
        }

        .nav-hamburger.open span:nth-child(1) {
            transform: translateY(6.5px) rotate(45deg);
        }

        .nav-hamburger.open span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        .nav-hamburger.open span:nth-child(3) {
            transform: translateY(-6.5px) rotate(-45deg);
        }

        /* Mobile menu overlay */
        .nav-mobile-menu {
            position: fixed;
            inset: 0;
            z-index: 999;
            background: var(--clr-black);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: var(--sp-xl);
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition);
        }

        .nav-mobile-menu.open {
            opacity: 1;
            pointer-events: auto;
        }

        .nav-mobile-menu a {
            font-family: var(--ff-heading);
            font-size: clamp(var(--fz-xl), 5vw, var(--fz-3xl));
            color: var(--clr-white);
            letter-spacing: .05em;
            transition: color var(--transition-fast);
        }

        .nav-mobile-menu a:hover {
            color: var(--clr-beige);
        }

        .nav-mobile-close {
            position: absolute;
            top: 1.5rem;
            right: var(--container-pad);
            color: rgba(255, 255, 255, .6);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color var(--transition-fast);
            background: none;
            border: none;
        }

        .nav-mobile-close:hover {
            color: var(--clr-white);
        }

        /* ============================================================
       HERO — Fullscreen Slideshow
       ============================================================ */
        .hero {
            position: relative;
            height: 100svh;
            min-height: 600px;
            overflow: hidden;
            background: var(--clr-black);
        }

        /* Slide stack */
        .hero-slides {
            position: absolute;
            inset: 0;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1.2s cubic-bezier(.4, 0, .2, 1);
            will-change: opacity;
        }

        .hero-slide.active {
            opacity: 1;
            z-index: 1;
        }

        .hero-slide-img {
            position: absolute;
            inset: -6%;
            background-size: cover;
            background-position: center top;
            will-change: transform;
            transition: transform .12s ease-out;
        }

        .hero-slide.active .hero-slide-img {
            animation: kenBurns 8s ease-out forwards;
        }

        @keyframes kenBurns {
            from {
                transform: scale(1.1);
            }

            to {
                transform: scale(1.01);
            }
        }

        /* Gradient overlays */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right, rgba(6, 6, 6, .9) 0%, rgba(6, 6, 6, .62) 50%, rgba(6, 6, 6, .18) 100%),
                linear-gradient(to top, rgba(6, 6, 6, .75) 0%, transparent 55%);
            z-index: 2;
            pointer-events: none;
        }

        /* Content panel */
        .hero-content {
            position: absolute;
            inset: 0;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: calc(var(--nav-height) + 3rem) var(--container-pad) 10rem;
            padding-left: max(var(--container-pad), calc((100vw - var(--container-max)) / 2 + var(--container-pad)));
            max-width: min(700px, 58vw);
        }

        .hero-tag {
            display: inline-block;
            font-size: var(--fz-xs);
            letter-spacing: .28em;
            text-transform: uppercase;
            color: var(--clr-accent);
            margin-bottom: var(--sp-md);
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .6s ease .1s, transform .6s ease .1s;
        }

        .hero-title {
            font-family: var(--ff-heading);
            font-size: clamp(2.8rem, 5.5vw, var(--fz-4xl));
            color: var(--clr-white);
            line-height: 1.04;
            margin-bottom: var(--sp-lg);
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .75s ease .25s, transform .75s ease .25s;
        }

        .hero-title em {
            font-style: italic;
            color: var(--clr-beige);
        }

        .hero-sub {
            font-size: var(--fz-md);
            color: rgba(255, 255, 255, .58);
            max-width: 400px;
            margin-bottom: var(--sp-2xl);
            line-height: 1.75;
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .7s ease .4s, transform .7s ease .4s;
        }

        .hero-actions {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: var(--sp-md);
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .6s ease .55s, transform .6s ease .55s;
        }

        /* Revealed state — toggled by JS on slide change */
        .hero-content.is-visible .hero-tag,
        .hero-content.is-visible .hero-title,
        .hero-content.is-visible .hero-sub,
        .hero-content.is-visible .hero-actions {
            opacity: 1;
            transform: translateY(0);
        }

        /* Prev / Next arrows */
        .hero-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            width: 52px;
            height: 52px;
            border: 1.5px solid rgba(255, 255, 255, .22);
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background var(--transition-fast), border-color var(--transition-fast), transform .2s;
        }

        .hero-arrow:hover {
            background: rgba(255, 255, 255, .18);
            border-color: rgba(255, 255, 255, .55);
            transform: translateY(-50%) scale(1.08);
        }

        .hero-arrow--prev {
            left: 1.75rem;
        }

        .hero-arrow--next {
            right: 1.75rem;
        }

        /* Slide dots */
        .hero-dots {
            position: absolute;
            bottom: 5rem;
            left: max(var(--container-pad), calc((100vw - var(--container-max)) / 2 + var(--container-pad)));
            z-index: 4;
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .hero-dot {
            width: 26px;
            height: 3px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .28);
            border: none;
            cursor: pointer;
            transition: width .35s ease, background .35s ease;
            padding: 0;
        }

        .hero-dot.active {
            width: 54px;
            background: var(--clr-accent);
        }

        /* Bottom progress line */
        .hero-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            background: var(--clr-accent);
            z-index: 5;
            width: 0%;
        }

        /* Scroll hint */
        .hero-scroll {
            position: absolute;
            bottom: 8.5rem;
            left: max(var(--container-pad), calc((100vw - var(--container-max)) / 2 + var(--container-pad)));
            display: flex;
            align-items: center;
            gap: .75rem;
            color: rgba(255, 255, 255, .32);
            font-size: var(--fz-xs);
            letter-spacing: .15em;
            text-transform: uppercase;
            z-index: 3;
        }

        .hero-scroll::before {
            content: '';
            display: block;
            width: 1px;
            height: 36px;
            background: rgba(255, 255, 255, .2);
            animation: scrollPulse 2.2s ease infinite;
        }

        @keyframes scrollPulse {

            0%,
            100% {
                opacity: .3;
                transform: scaleY(1);
            }

            50% {
                opacity: .9;
                transform: scaleY(1.35);
            }
        }

        /* Slide counter */
        .hero-counter {
            position: absolute;
            top: calc(var(--nav-height) + 2.5rem);
            right: max(var(--container-pad), calc((100vw - var(--container-max)) / 2 + var(--container-pad)));
            z-index: 4;
            display: flex;
            align-items: baseline;
            gap: .4rem;
            color: rgba(255, 255, 255, .35);
            font-size: var(--fz-xs);
            letter-spacing: .1em;
        }

        .hero-counter-current {
            font-size: 1.6rem;
            font-family: var(--ff-heading);
            color: rgba(255, 255, 255, .7);
            line-height: 1;
            transition: opacity .3s;
        }

        /* Stats bar */
        .hero-stats {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 4;
            background: rgba(8, 8, 8, .78);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-top: 1px solid rgba(255, 255, 255, .07);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .hero-stat {
            text-align: center;
            padding: 1.1rem 1.5rem;
            border-right: 1px solid rgba(255, 255, 255, .07);
        }

        .hero-stat:last-child {
            border-right: none;
        }

        .hero-stat-num {
            display: block;
            font-family: var(--ff-heading);
            font-size: var(--fz-xl);
            color: var(--clr-white);
            line-height: 1;
            margin-bottom: .2rem;
        }

        .hero-stat-lbl {
            display: block;
            font-size: var(--fz-xs);
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .35);
        }

        /* ============================================================
       MARQUEE
       ============================================================ */
        .marquee-strip {
            background: var(--clr-black);
            padding: .8rem 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-flex;
            animation: marquee 32s linear infinite;
        }

        .marquee-track span {
            font-size: var(--fz-xs);
            letter-spacing: .22em;
            text-transform: uppercase;
            padding: 0 2rem;
            color: rgba(255, 255, 255, .5);
        }

        .marquee-track strong {
            color: var(--clr-accent);
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* ============================================================
       PRODUCT CARD
       ============================================================ */
        .product-card {
            cursor: pointer;
            position: relative;
            background: var(--clr-white);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .product-card:hover {
            transform: translateY(-7px);
            box-shadow: var(--shadow-lg);
        }

        .product-card-img {
            aspect-ratio: 3/4;
            position: relative;
            overflow: hidden;
        }

        .product-card-img-inner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .55s ease;
        }

        .product-card:hover .product-card-img-inner {
            transform: scale(1.05);
        }

        /* Product image fills its container */
        .modal-img-main img,
        .modal-img-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: inherit;
        }

        .product-badge {
            position: absolute;
            top: .75rem;
            left: .75rem;
            z-index: 1;
            padding: .25rem .6rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            border-radius: var(--radius-sm);
        }

        .badge-new {
            background: var(--clr-black);
            color: var(--clr-white);
        }

        .badge-sale {
            background: var(--clr-danger);
            color: var(--clr-white);
        }

        .product-card-actions {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            padding: .6rem;
            z-index: 1;
            transition: bottom var(--transition);
        }

        .product-card:hover .product-card-actions {
            bottom: 0;
        }

        .product-card-actions .btn {
            width: 100%;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
            padding: .65rem;
        }

        .product-card-body {
            padding: 1rem 1rem .9rem;
        }

        .product-card-body h3 {
            font-family: var(--ff-body);
            font-size: var(--fz-sm);
            font-weight: 500;
            margin-bottom: .35rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card-price {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .price-current {
            font-size: var(--fz-base);
            font-weight: 700;
        }

        .price-original {
            font-size: var(--fz-sm);
            color: var(--clr-gray-400);
            text-decoration: line-through;
        }

        /* ============================================================
       FEATURED PRODUCTS
       ============================================================ */
        .section-featured {
            background: var(--clr-white);
        }

        .section-hdr-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: var(--sp-xl);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        /* Category pills */
        .category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: var(--sp-xl);
        }

        .category-pill {
            padding: .45rem 1.2rem;
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-full);
            color: var(--clr-gray-600);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .category-pill:hover,
        .category-pill.active {
            background: var(--clr-black);
            border-color: var(--clr-black);
            color: var(--clr-white);
        }

        /* ============================================================
       FEATURES STRIP
       ============================================================ */
        .features-strip {
            background: var(--clr-beige);
            padding: var(--sp-2xl) 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .75rem;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .feature-item h4 {
            font-family: var(--ff-body);
            font-size: var(--fz-sm);
            font-weight: 600;
        }

        .feature-item p {
            font-size: var(--fz-xs);
            color: var(--clr-gray-600);
        }

        /* ============================================================
       SHOP SECTION
       ============================================================ */
        .section-shop {
            background: var(--clr-gray-50);
        }

        .shop-layout {
            display: grid;
            grid-template-columns: 256px 1fr;
            gap: 2.5rem;
            align-items: start;
        }

        .shop-sidebar {
            background: var(--clr-white);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            border: 1px solid var(--clr-gray-200);
            position: sticky;
            top: calc(var(--nav-height) + 1.5rem);
        }

        .sidebar-title {
            font-family: var(--ff-body);
            font-size: var(--fz-xs);
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: var(--sp-lg);
            padding-bottom: var(--sp-md);
            border-bottom: 1px solid var(--clr-gray-200);
        }

        .filter-group {
            margin-bottom: var(--sp-xl);
        }

        .filter-group-title {
            font-size: var(--fz-xs);
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--clr-gray-600);
            margin-bottom: var(--sp-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .filter-group-title::after {
            content: '−';
            font-size: 1.1rem;
            font-weight: 300;
        }

        .filter-group.collapsed .filter-group-title::after {
            content: '+';
        }

        .filter-group-body {
            transition: max-height .3s ease, overflow .3s;
        }

        .filter-group.collapsed .filter-group-body {
            max-height: 0 !important;
            overflow: hidden;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .32rem 0;
            font-size: var(--fz-sm);
            color: var(--clr-gray-800);
            cursor: pointer;
            transition: color var(--transition-fast);
        }

        .filter-option:hover {
            color: var(--clr-black);
        }

        .filter-option input[type="checkbox"] {
            accent-color: var(--clr-black);
            width: 14px;
            height: 14px;
        }

        .size-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .45rem;
        }

        .size-btn {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--fz-xs);
            font-weight: 500;
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--transition-fast);
            background: var(--clr-white);
        }

        .size-btn:hover,
        .size-btn.active {
            background: var(--clr-black);
            border-color: var(--clr-black);
            color: var(--clr-white);
        }

        .price-range-wrap {
            padding: .5rem 0;
        }

        .price-range-hdr {
            display: flex;
            justify-content: space-between;
            font-size: var(--fz-xs);
            color: var(--clr-gray-600);
            margin-bottom: .75rem;
        }

        .price-val {
            font-weight: 700;
            color: var(--clr-black);
            font-size: var(--fz-sm);
        }

        .range-slider {
            width: 100%;
            height: 2px;
            background: var(--clr-gray-200);
            border-radius: var(--radius-full);
            outline: none;
            cursor: pointer;
            accent-color: var(--clr-black);
            -webkit-appearance: none;
            appearance: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            background: var(--clr-black);
            border-radius: 50%;
            cursor: pointer;
            transition: transform var(--transition-fast);
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.3);
        }

        .shop-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--sp-md);
            margin-bottom: var(--sp-xl);
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 340px;
        }

        .search-box input {
            width: 100%;
            padding: .7rem 1rem .7rem 2.6rem;
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-full);
            font-size: var(--fz-sm);
            background: var(--clr-white);
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        }

        .search-box input:focus {
            border-color: var(--clr-black);
            box-shadow: 0 0 0 3px rgba(10, 10, 10, .05);
        }

        .search-box::before {
            content: '⌕';
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            color: var(--clr-gray-400);
            pointer-events: none;
        }

        .sort-select {
            padding: .68rem 1rem;
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-full);
            font-size: var(--fz-sm);
            color: var(--clr-gray-800);
            background: var(--clr-white);
            cursor: pointer;
        }

        .sort-select:focus {
            border-color: var(--clr-black);
        }

        .results-count {
            font-size: var(--fz-xs);
            color: var(--clr-gray-600);
            letter-spacing: .05em;
        }

        /* ============================================================
       EDITORIAL STRIP
       ============================================================ */
        .editorial-strip {
            background: var(--clr-beige);
            padding: clamp(4rem, 10vw, 7rem) var(--container-pad);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .editorial-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(90deg,
                    transparent,
                    transparent 79px,
                    rgba(0, 0, 0, .04) 80px),
                repeating-linear-gradient(0deg,
                    transparent,
                    transparent 79px,
                    rgba(0, 0, 0, .04) 80px);
            pointer-events: none;
        }

        .editorial-strip-inner {
            position: relative;
            z-index: 1;
            max-width: 780px;
            margin: 0 auto;
        }

        .editorial-strip-eyebrow {
            display: inline-block;
            font-size: var(--fz-xs);
            letter-spacing: .25em;
            text-transform: uppercase;
            color: var(--clr-accent-dk);
            margin-bottom: 1.25rem;
        }

        .editorial-strip-heading {
            font-family: var(--ff-heading);
            font-size: clamp(2rem, 5vw, 3.75rem);
            line-height: 1.1;
            color: var(--clr-black);
            margin-bottom: 1.25rem;
        }

        .editorial-strip-heading em {
            font-style: italic;
            color: var(--clr-gray-600);
        }

        .editorial-strip-body {
            font-size: var(--fz-md);
            color: var(--clr-gray-600);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .editorial-strip-divider {
            width: 40px;
            height: 1px;
            background: var(--clr-accent);
            margin: 0 auto 2rem;
        }

        /* ============================================================
       NEWSLETTER
       ============================================================ */
        .newsletter {
            background: var(--clr-black);
            padding: var(--sp-3xl) 0;
            text-align: center;
        }

        .newsletter-inner {
            max-width: 560px;
            margin: 0 auto;
        }

        .newsletter-tag {
            display: block;
            font-size: var(--fz-xs);
            letter-spacing: .25em;
            text-transform: uppercase;
            color: var(--clr-accent);
            margin-bottom: var(--sp-md);
        }

        .newsletter h2 {
            font-size: clamp(var(--fz-lg), 4vw, var(--fz-2xl));
            color: var(--clr-white);
            margin-bottom: var(--sp-sm);
        }

        .newsletter p {
            font-size: var(--fz-sm);
            color: rgba(255, 255, 255, .45);
            margin-bottom: var(--sp-xl);
        }

        .newsletter-form {
            display: flex;
            max-width: 440px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: var(--radius-full);
            overflow: hidden;
            transition: border-color var(--transition);
        }

        .newsletter-form:focus-within {
            border-color: rgba(255, 255, 255, .4);
        }

        .newsletter-form input {
            flex: 1;
            padding: .85rem 1.25rem;
            background: transparent;
            color: var(--clr-white);
            font-size: var(--fz-sm);
            border: none;
        }

        .newsletter-form input::placeholder {
            color: rgba(255, 255, 255, .3);
        }

        .newsletter-form button {
            padding: .85rem 1.5rem;
            background: var(--clr-accent);
            color: var(--clr-white);
            font-size: var(--fz-xs);
            letter-spacing: .1em;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            border: none;
            white-space: nowrap;
            transition: background var(--transition-fast);
        }

        .newsletter-form button:hover {
            background: var(--clr-accent-dk);
        }

        /* ============================================================
       FOOTER
       ============================================================ */
        .site-footer {
            background: var(--clr-black);
            border-top: 1px solid rgba(255, 255, 255, .06);
            padding: 2.75rem 0 1.5rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr;
            gap: 2rem 3rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            font-family: var(--ff-heading);
            font-size: 1.2rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--clr-white);
            margin-bottom: .6rem;
            display: block;
        }

        .footer-desc {
            font-size: var(--fz-xs);
            color: rgba(255, 255, 255, .32);
            line-height: 1.65;
            max-width: 260px;
        }

        .footer-col-title {
            font-family: var(--ff-body);
            font-size: var(--fz-xs);
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--clr-white);
            margin-bottom: .9rem;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .footer-links a {
            font-size: var(--fz-xs);
            color: rgba(255, 255, 255, .38);
            transition: color var(--transition-fast);
        }

        .footer-links a:hover {
            color: rgba(255, 255, 255, .85);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .05);
            padding-top: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-copy {
            font-size: calc(var(--fz-xs) - 1px);
            color: rgba(255, 255, 255, .2);
            letter-spacing: .04em;
        }

        .footer-bottom-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .footer-legal {
            display: flex;
            gap: 1.25rem;
        }

        .footer-legal a {
            font-size: calc(var(--fz-xs) - 1px);
            color: rgba(255, 255, 255, .25);
            transition: color var(--transition-fast);
        }

        .footer-legal a:hover {
            color: rgba(255, 255, 255, .65);
        }

        .footer-social {
            display: flex;
            gap: .55rem;
        }

        .footer-social a {
            width: 28px;
            height: 28px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .38);
            font-size: .7rem;
            font-style: normal;
            transition: all var(--transition-fast);
        }

        .footer-social a:hover {
            border-color: var(--clr-accent);
            color: var(--clr-accent);
        }

        /* ============================================================
       QUICK VIEW MODAL
       ============================================================ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--sp-md);
            background: rgba(10, 10, 10, .55);
            backdrop-filter: blur(5px);
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition);
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: var(--clr-white);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 840px;
            max-height: 90vh;
            overflow-y: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
            transform: translateY(18px) scale(.98);
            transition: transform var(--transition);
        }

        .modal-overlay.open .modal-box {
            transform: translateY(0) scale(1);
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 10;
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, .9);
            border: 1px solid var(--clr-gray-200);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .modal-close:hover {
            background: var(--clr-black);
            color: var(--clr-white);
            border-color: var(--clr-black);
        }

        .modal-img-col {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            padding: 1.5rem;
            background: var(--clr-gray-50);
            border-radius: var(--radius-lg) 0 0 var(--radius-lg);
        }

        .modal-img-main {
            flex: 1;
            border-radius: var(--radius-md);
            aspect-ratio: 3/4;
            min-height: 200px;
            transition: background var(--transition);
        }

        .modal-img-thumbs {
            display: flex;
            gap: .5rem;
        }

        .modal-img-thumb {
            width: 60px;
            height: 75px;
            border-radius: var(--radius-sm);
            border: 2px solid transparent;
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color var(--transition-fast);
        }

        .modal-img-thumb.active {
            border-color: var(--clr-black);
        }

        .modal-img-thumb:hover {
            border-color: var(--clr-gray-400);
        }

        .modal-info-col {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .modal-category {
            font-size: var(--fz-xs);
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--clr-accent);
        }

        .modal-product-name {
            font-family: var(--ff-heading);
            font-size: var(--fz-xl);
        }

        .modal-product-price {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .modal-price-current {
            font-size: var(--fz-xl);
            font-weight: 700;
        }

        .modal-price-original {
            font-size: var(--fz-base);
            color: var(--clr-gray-400);
            text-decoration: line-through;
        }

        .modal-divider {
            height: 1px;
            background: var(--clr-gray-200);
        }

        .modal-label {
            font-size: var(--fz-xs);
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--clr-gray-600);
            margin-bottom: .55rem;
        }

        .modal-sizes {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .modal-size-btn {
            min-width: 44px;
            height: 44px;
            padding: 0 .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--fz-sm);
            font-weight: 500;
            font-family: var(--ff-body);
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-sm);
            cursor: pointer;
            background: var(--clr-white);
            transition: all var(--transition-fast);
        }

        .modal-size-btn:hover,
        .modal-size-btn.active {
            background: var(--clr-black);
            border-color: var(--clr-black);
            color: var(--clr-white);
        }

        .modal-qty {
            display: flex;
            align-items: center;
            border: 1px solid var(--clr-gray-200);
            border-radius: var(--radius-sm);
            width: fit-content;
            overflow: hidden;
        }

        .qty-btn {
            width: 40px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: var(--clr-white);
            border: none;
            cursor: pointer;
            font-family: var(--ff-body);
            transition: background var(--transition-fast);
        }

        .qty-btn:hover {
            background: var(--clr-gray-100);
        }

        .qty-value {
            width: 44px;
            text-align: center;
            font-size: var(--fz-sm);
            font-weight: 700;
            border-left: 1px solid var(--clr-gray-200);
            border-right: 1px solid var(--clr-gray-200);
        }

        .modal-add-cart {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        /* ============================================================
       TOAST
       ============================================================ */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            background: var(--clr-black);
            color: var(--clr-white);
            padding: .85rem 1.5rem;
            border-radius: var(--radius-md);
            font-size: var(--fz-sm);
            font-family: var(--ff-body);
            pointer-events: none;
            white-space: nowrap;
            transform: translateY(100px);
            opacity: 0;
            transition: transform .3s ease, opacity .3s ease;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* ============================================================
       RESPONSIVE
       ============================================================ */
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .hero-content {
                max-width: 90vw;
                padding-top: calc(var(--nav-height) + 2rem);
                padding-bottom: 5rem;
            }

            .hero-arrow--prev {
                left: .75rem;
            }

            .hero-arrow--next {
                right: .75rem;
            }

            .hero-counter {
                display: none;
            }

            .hero-stats {
                max-width: 100%;
            }

            .hero-title {
                font-size: clamp(2.2rem, 7vw, 3.2rem);
            }

            .shop-layout {
                grid-template-columns: 1fr;
            }

            .shop-sidebar {
                position: static;
            }

            .modal-box {
                grid-template-columns: 1fr;
                max-width: 460px;
            }

            .modal-img-col {
                border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            }
        }

        @media (max-width: 768px) {

            .nav-menu,
            .nav-btn-login,
            .nav-btn-register {
                display: none;
            }

            .nav-hamburger {
                display: flex;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .editorial-strip-heading {
                font-size: 2rem;
            }

            .section-hdr-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero-actions {
                flex-direction: column;
                flex-wrap: wrap;
            }

            .hero-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: .85rem;
            }

            .footer-bottom-right {
                flex-direction: row;
                gap: 1.25rem;
            }

            .features-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- ══════════════════════════════════════════════════════════
       MOBILE MENU
       ══════════════════════════════════════════════════════════ -->
    <div class="nav-mobile-menu" id="mobileMenu" role="dialog" aria-label="Mobile navigation">
        <button class="nav-mobile-close" id="mobileClose" aria-label="Close menu">✕</button>
        <a href="<?= url() ?>">Home</a>
        <a href="<?= url('products') ?>">Shop</a>
        <a href="#featured">New Arrivals</a>
    </div>

    <!-- ══════════════════════════════════════════════════════════
       NAVBAR
       ══════════════════════════════════════════════════════════ -->
    <header>
        <nav class="navbar" id="navbar" role="navigation" aria-label="Main navigation">
            <div class="nav-container">
                <a href="<?= url() ?>" class="nav-logo">Clothy</a>

                <ul class="nav-menu">
                    <li><a href="<?= url() ?>">Home</a></li>
                    <li><a href="<?= url('products') ?>">Shop</a></li>
                    <li><a href="#featured">New Arrivals</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="<?= url('cart') ?>" class="btn-cart" id="cartBtn" aria-label="Shopping cart">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                        <span class="cart-badge" id="cartBadge">0</span>
                    </a>

                    <?php if (class_exists('Session') && Session::isLoggedIn()): ?>
                        <a href="<?= url('account') ?>" class="nav-btn-login">Account</a>
                        <a href="<?= url('logout') ?>" class="nav-btn-register">Logout</a>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="nav-btn-login">Login</a>
                        <a href="<?= url('register') ?>" class="nav-btn-register">Register</a>
                    <?php endif; ?>

                    <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- ══════════════════════════════════════════════════════════
       HERO — Fullscreen Slideshow
       ══════════════════════════════════════════════════════════ -->
    <section class="hero" id="hero" aria-label="Hero slideshow">

        <!-- Slide backgrounds -->
        <div class="hero-slides" id="heroSlides">
            <div class="hero-slide active" data-index="0">
                <div class="hero-slide-img" style="background-image:url('<?= asset('images/hero-1.jpg') ?>')"></div>
            </div>
            <div class="hero-slide" data-index="1">
                <div class="hero-slide-img" style="background-image:url('<?= asset('images/hero-2.jpg') ?>')"></div>
            </div>
            <div class="hero-slide" data-index="2">
                <div class="hero-slide-img" style="background-image:url('<?= asset('images/hero-3.jpg') ?>')"></div>
            </div>
        </div>

        <!-- Gradient overlay -->
        <div class="hero-overlay"></div>

        <!-- Content -->
        <div class="hero-content is-visible" id="heroContent">
            <span class="hero-tag" id="heroTag">Spring / Summer 2026</span>
            <h1 class="hero-title" id="heroTitle">Wear Your<br><em>Story</em></h1>
            <p class="hero-sub" id="heroSub">Curated essentials for those who define their own style. From luxury basics to signature statements.</p>
            <div class="hero-actions">
                <a href="<?= url('products') ?>" class="btn btn--primary btn--lg">Shop Collection</a>
                <a href="#featured" class="btn btn--ghost btn--lg">New Arrivals</a>
            </div>
        </div>

        <!-- Scroll hint -->
        <div class="hero-scroll" aria-hidden="true">Scroll</div>

        <!-- Slide dots -->
        <div class="hero-dots" role="tablist" aria-label="Slides">
            <button class="hero-dot active" role="tab" aria-selected="true" aria-label="Slide 1" data-slide="0"></button>
            <button class="hero-dot" role="tab" aria-selected="false" aria-label="Slide 2" data-slide="1"></button>
            <button class="hero-dot" role="tab" aria-selected="false" aria-label="Slide 3" data-slide="2"></button>
        </div>

        <!-- Arrows -->
        <button class="hero-arrow hero-arrow--prev" id="heroPrev" aria-label="Previous slide">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
        </button>
        <button class="hero-arrow hero-arrow--next" id="heroNext" aria-label="Next slide">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6" />
            </svg>
        </button>

        <!-- Slide counter -->
        <div class="hero-counter" aria-hidden="true">
            <span class="hero-counter-current" id="heroCountCurrent">01</span>
            <span>/ 03</span>
        </div>

        <!-- Progress bar -->
        <div class="hero-progress" id="heroProgress"></div>

        <!-- Stats -->
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-num" data-count="500" data-suffix="+">0</span>
                <span class="hero-stat-lbl">Styles</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num" data-count="12" data-suffix="K">0</span>
                <span class="hero-stat-lbl">Customers</span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-num" data-count="98" data-suffix="%">0</span>
                <span class="hero-stat-lbl">Satisfaction</span>
            </div>
        </div>

    </section>

    <!-- ══════════════════════════════════════════════════════════
       MARQUEE STRIP
       ══════════════════════════════════════════════════════════ -->
    <div class="marquee-strip" aria-hidden="true">
        <div class="marquee-track">
            <?php $msg = '<span>New Arrivals <strong>·</strong> Free Shipping Over $150 <strong>·</strong> Spring Collection 2026 <strong>·</strong> Premium Quality <strong>·</strong> Exclusive Drops <strong>·</strong></span>';
            echo str_repeat($msg, 4); ?>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
       FEATURED PRODUCTS
       ══════════════════════════════════════════════════════════ -->
    <section class="section section-featured" id="featured">
        <div class="container">
            <div class="section-hdr-row fade-in">
                <div>
                    <span class="section-label">Handpicked for you</span>
                    <h2 class="section-title">New Arrivals</h2>
                </div>
                <a href="<?= url('products') ?>" class="section-link">View all</a>
            </div>

            <div class="category-pills fade-in">
                <button class="category-pill active" data-filter="all">All</button>
                <button class="category-pill" data-filter="women">Women</button>
                <button class="category-pill" data-filter="men">Men</button>
                <button class="category-pill" data-filter="unisex">Unisex</button>
                <button class="category-pill" data-filter="accessories">Accessories</button>
            </div>

            <?php
            $imgMap = [
                'pimg-1'  => 'blazer.jpg',
                'pimg-2'  => 'trousers.jpg',
                'pimg-3'  => 'tee.jpg',
                'pimg-4'  => 'dress.jpg',
                'pimg-5'  => 'cardigan.jpg',
                'pimg-6'  => 'shorts.jpg',
                'pimg-7'  => 'cami.jpg',
                'pimg-8'  => 'trench.jpg',
                'pimg-9'  => 'shirt.jpg',
                'pimg-10' => 'sweatshirt.jpg',
                'pimg-11' => 'denim.jpg',
                'pimg-12' => 'skirt.jpg',
            ];
            ?>
            <div class="products-grid" id="featuredGrid">
                <?php
                $_featList = array_slice($dbFeatured ?? [], 0, 8);
                $stagger   = 1;
                foreach ($_featList as $p):
                    $s       = 's' . min($stagger, 8);
                    $_price  = '$' . number_format((float) $p->price, 2);
                    $_orig   = $p->compare_price ? '$' . number_format((float) $p->compare_price, 2) : '';
                    $_badge  = $p->compare_price ? 'sale' : ($p->is_featured ? 'new' : '');
                    $_cat    = strtolower($p->category_name ?? 'all');
                    $_imgFile = $p->primary_image ? basename($p->primary_image) : 'blazer.jpg';
                    $_imgSrc  = productImg($p->primary_image ?? null);
                ?>
                    <article class="product-card fade-in <?= $s ?>"
                        data-id="<?= (int) $p->id ?>"
                        data-name="<?= htmlspecialchars($p->name) ?>"
                        data-price="<?= htmlspecialchars($_price) ?>"
                        data-orig="<?= htmlspecialchars($_orig) ?>"
                        data-img="<?= htmlspecialchars(basename(productImg($p->primary_image ?? null))) ?>"
                        data-category="<?= htmlspecialchars($_cat) ?>"
                        onclick="openQuickView(this)"
                        role="button" tabindex="0">
                        <div class="product-card-img">
                            <img class="product-card-img-inner"
                                src="<?= $_imgSrc ?>"
                                alt="<?= htmlspecialchars($p->name) ?>"
                                width="300" height="400"
                                loading="<?= $stagger <= 4 ? 'eager' : 'lazy' ?>"
                                decoding="async">
                            <?php if ($_badge): ?>
                                <span class="product-badge badge-<?= $_badge ?>"><?= ucfirst($_badge) ?></span>
                            <?php endif; ?>
                            <div class="product-card-actions">
                                <button class="btn btn--primary btn--sm"
                                    onclick="event.stopPropagation(); addToCart(this.closest('.product-card').dataset.name, this.closest('.product-card').dataset.id, 1)">
                                    Quick Add
                                </button>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <h3><?= htmlspecialchars($p->name) ?></h3>
                            <div class="product-card-price">
                                <span class="price-current"><?= $_price ?></span>
                                <?php if ($_orig): ?>
                                    <span class="price-original"><?= $_orig ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php $stagger++;
                endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════
       FEATURES TRUST STRIP
       ══════════════════════════════════════════════════════════ -->
    <div class="features-strip">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item fade-in">
                    <div class="feature-icon">✦</div>
                    <h4>Free Shipping</h4>
                    <p>On all orders over $150</p>
                </div>
                <div class="feature-item fade-in s1">
                    <div class="feature-icon">↺</div>
                    <h4>Easy Returns</h4>
                    <p>30-day hassle-free returns</p>
                </div>
                <div class="feature-item fade-in s2">
                    <div class="feature-icon">◈</div>
                    <h4>Premium Quality</h4>
                    <p>Ethically sourced fabrics</p>
                </div>
                <div class="feature-item fade-in s3">
                    <div class="feature-icon">◉</div>
                    <h4>Secure Payment</h4>
                    <p>100% secure checkout</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
       SHOP / COLLECTION SECTION
       ══════════════════════════════════════════════════════════ -->
    <section class="section section-shop" id="shop">
        <div class="container">
            <div class="section-hdr-row fade-in">
                <div>
                    <span class="section-label">Browse all</span>
                    <h2 class="section-title">The Collection</h2>
                </div>
                <a href="<?= url('products') ?>" class="section-link">Full Store</a>
            </div>

            <div class="shop-layout">

                <!-- ── Sidebar filters ── -->
                <aside class="shop-sidebar fade-in">
                    <div class="sidebar-title">Filters</div>

                    <div class="filter-group">
                        <div class="filter-group-title" onclick="toggleFilter(this)">Category</div>
                        <div class="filter-group-body">
                            <?php foreach (['Women', 'Men', 'Unisex', 'Accessories', 'Sale'] as $cat): ?>
                                <label class="filter-option"><input type="checkbox"> <?= $cat ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="filter-group-title" onclick="toggleFilter(this)">Size</div>
                        <div class="filter-group-body">
                            <div class="size-grid">
                                <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz): ?>
                                    <button class="size-btn" onclick="this.classList.toggle('active')"><?= $sz ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="filter-group-title" onclick="toggleFilter(this)">Price Range</div>
                        <div class="filter-group-body">
                            <div class="price-range-wrap">
                                <div class="price-range-hdr">
                                    <span>$0</span>
                                    <span class="price-val" id="priceDisplay">Up to $500</span>
                                    <span>$500</span>
                                </div>
                                <input type="range" class="range-slider" id="priceSlider"
                                    min="0" max="500" value="500"
                                    oninput="document.getElementById('priceDisplay').textContent = 'Up to $' + this.value">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn--primary btn--full" style="margin-top:.25rem;">Apply Filters</button>
                    <button class="btn btn--outline  btn--full" style="margin-top:.5rem; border-color:var(--clr-gray-200); color:var(--clr-gray-600);">Clear All</button>
                </aside>

                <!-- ── Products ── -->
                <div class="shop-main">
                    <div class="shop-toolbar fade-in">
                        <div class="search-box">
                            <input type="search" placeholder="Search styles…" aria-label="Search products">
                        </div>
                        <select class="sort-select" aria-label="Sort">
                            <option>Sort: Featured</option>
                            <option>Price: Low – High</option>
                            <option>Price: High – Low</option>
                            <option>Newest First</option>
                            <option>Best Sellers</option>
                        </select>
                        <span class="results-count">12 of 48 products</span>
                    </div>

                    <div class="products-grid" id="shopGrid">
                        <?php
                        $_shopList = $dbFeatured ?? [];
                        $_si = 1;
                        foreach ($_shopList as $p):
                            $s       = 's' . min($_si, 8);
                            $_price  = '$' . number_format((float) $p->price, 2);
                            $_orig   = $p->compare_price ? '$' . number_format((float) $p->compare_price, 2) : '';
                            $_badge  = $p->compare_price ? 'sale' : ($p->is_featured ? 'new' : '');
                            $_imgFile = $p->primary_image ? basename($p->primary_image) : 'blazer.jpg';
                            $_imgSrc  = productImg($p->primary_image ?? null);
                        ?>
                            <article class="product-card fade-in <?= $s ?>"
                                data-id="<?= (int) $p->id ?>"
                                data-name="<?= htmlspecialchars($p->name) ?>"
                                data-price="<?= htmlspecialchars($_price) ?>"
                                data-orig="<?= htmlspecialchars($_orig) ?>"
                                data-img="<?= htmlspecialchars(basename(productImg($p->primary_image ?? null))) ?>"
                                onclick="openQuickView(this)"
                                role="button" tabindex="0">
                                <div class="product-card-img">
                                    <img class="product-card-img-inner"
                                        src="<?= $_imgSrc ?>"
                                        alt="<?= htmlspecialchars($p->name) ?>"
                                        width="300" height="400"
                                        loading="lazy"
                                        decoding="async">
                                    <?php if ($_badge): ?>
                                        <span class="product-badge badge-<?= $_badge ?>"><?= ucfirst($_badge) ?></span>
                                    <?php endif; ?>
                                    <div class="product-card-actions">
                                        <button class="btn btn--primary btn--sm"
                                            onclick="event.stopPropagation(); addToCart(this.closest('.product-card').dataset.name, this.closest('.product-card').dataset.id, 1)">
                                            Quick Add
                                        </button>
                                    </div>
                                </div>
                                <div class="product-card-body">
                                    <h3><?= htmlspecialchars($p->name) ?></h3>
                                    <div class="product-card-price">
                                        <span class="price-current"><?= $_price ?></span>
                                        <?php if ($_orig): ?>
                                            <span class="price-original"><?= $_orig ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php $_si++;
                        endforeach; ?>
                    </div>

                    <div style="text-align:center; margin-top:2.5rem;">
                        <button class="btn btn--outline" style="min-width:200px;">Load More Products</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════
       EDITORIAL STRIP
       ══════════════════════════════════════════════════════════ -->
    <section class="editorial-strip" id="lookbook">
        <div class="editorial-strip-inner fade-in">
            <span class="editorial-strip-eyebrow">The Clothy Philosophy</span>
            <h2 class="editorial-strip-heading">Dress with intention.<br><em>Live with purpose.</em></h2>
            <div class="editorial-strip-divider"></div>
            <p class="editorial-strip-body">Every piece in our collection is chosen for its craft, its fit, and its staying power. We believe style isn't seasonal — it's a statement that endures.</p>
            <a href="<?= url('products') ?>" class="btn btn--primary">Explore the Collection</a>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════
       NEWSLETTER
       ══════════════════════════════════════════════════════════ -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-inner fade-in">
                <span class="newsletter-tag">Stay in the loop</span>
                <h2>Join the Clothy Community</h2>
                <p>First access to new collections, exclusive drops, and member-only offers.</p>
                <form class="newsletter-form" onsubmit="return handleNewsletter(event)">
                    <input type="email" placeholder="your@email.com" aria-label="Email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════════════
       FOOTER
       ══════════════════════════════════════════════════════════ -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">

                <div>
                    <a href="<?= url() ?>" class="footer-logo">Clothy</a>
                    <p class="footer-desc">Minimal fashion for the modern individual. Curated styles that define who you are.</p>
                </div>

                <div>
                    <h4 class="footer-col-title">Shop</h4>
                    <nav class="footer-links">
                        <a href="<?= url('products') ?>">New Arrivals</a>
                        <a href="<?= url('products') ?>">Women's Collection</a>
                        <a href="<?= url('products') ?>">Men's Collection</a>
                        <a href="<?= url('products') ?>">Accessories</a>
                        <a href="<?= url('products') ?>">Sale</a>
                    </nav>
                </div>

                <div>
                    <h4 class="footer-col-title">Help</h4>
                    <nav class="footer-links">
                        <a href="#">FAQ</a>
                        <a href="#">Shipping &amp; Returns</a>
                        <a href="#">Size Guide</a>
                        <a href="#">Track My Order</a>

                    </nav>
                </div>

            </div>

            <div class="footer-bottom">
                <p class="footer-copy">&copy; <?= date('Y') ?> <?= defined('APP_NAME') ? e(APP_NAME) : 'Clothy Outlet' ?>. All rights reserved.</p>
                <div class="footer-bottom-right">
                    <nav class="footer-legal">
                        <a href="#">Privacy</a>
                        <a href="#">Terms</a>
                        <a href="#">Cookies</a>
                    </nav>
                    <nav class="footer-social" aria-label="Social media">
                        <a href="#" aria-label="Instagram"><em>In</em></a>
                        <a href="#" aria-label="TikTok">Tk</a>
                        <a href="#" aria-label="Pinterest">Pt</a>
                        <a href="#" aria-label="YouTube">▶</a>
                    </nav>
                </div>
            </div>
        </div>
    </footer>

    <!-- ══════════════════════════════════════════════════════════
       QUICK VIEW MODAL
       ══════════════════════════════════════════════════════════ -->
    <div class="modal-overlay" id="quickViewModal"
        role="dialog" aria-modal="true" aria-label="Quick view"
        onclick="if(event.target===this) closeQuickView()">
        <div class="modal-box">
            <button class="modal-close" onclick="closeQuickView()" aria-label="Close">✕</button>

            <div class="modal-img-col">
                <div class="modal-img-main">
                    <img id="modalImgMainImg" src="" alt="" width="300" height="400" loading="lazy" decoding="async">
                </div>
                <div class="modal-img-thumbs" id="modalThumbs"></div>
            </div>

            <div class="modal-info-col">
                <span class="modal-category">Ready to Wear</span>
                <h2 class="modal-product-name" id="modalName">—</h2>
                <div class="modal-product-price">
                    <span class="modal-price-current" id="modalPrice">—</span>
                    <span class="modal-price-original" id="modalOrig"></span>
                </div>
                <div class="modal-divider"></div>

                <div>
                    <p class="modal-label">
                        Size
                        <a href="#" style="font-size:.7rem;font-weight:400;letter-spacing:0;text-transform:none;color:var(--clr-accent);margin-left:.5rem;">Size Guide</a>
                    </p>
                    <div class="modal-sizes" id="modalSizes">
                        <?php foreach (['XS', 'S', 'M', 'L', 'XL'] as $sz): ?>
                            <button class="modal-size-btn" onclick="selectModalSize(this)"><?= $sz ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <p class="modal-label">Quantity</p>
                    <div class="modal-qty">
                        <button class="qty-btn" onclick="changeQty(-1)" aria-label="Decrease">−</button>
                        <span class="qty-value" id="qtyValue">1</span>
                        <button class="qty-btn" onclick="changeQty(+1)" aria-label="Increase">+</button>
                    </div>
                </div>

                <div class="modal-divider"></div>

                <div class="modal-add-cart">
                    <button class="btn btn--primary btn--full btn--lg" onclick="addToCartFromModal()">Add to Cart</button>
                    <button class="btn btn--outline  btn--full">Add to Wishlist ♡</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
       TOAST
       ══════════════════════════════════════════════════════════ -->
    <div class="toast" id="toast"></div>

    <!-- ══════════════════════════════════════════════════════════
       JAVASCRIPT — ALL INLINE
       ══════════════════════════════════════════════════════════ -->
    <script>
        /* ── State ───────────────────────────────────────────── */
        var cartCount = 0;
        var modalQty = 1;
        var toastTimer;

        /* ── Navbar: solidify on scroll ─────────────────────── */
        var navbar = document.getElementById('navbar');
        window.addEventListener('scroll', function() {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        }, {
            passive: true
        });

        /* ── Mobile hamburger menu ───────────────────────────── */
        var hamburger = document.getElementById('hamburger');
        var mobileMenu = document.getElementById('mobileMenu');
        var mobileClose = document.getElementById('mobileClose');

        hamburger.addEventListener('click', function() {
            var open = mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('open', open);
            hamburger.setAttribute('aria-expanded', open);
            document.body.style.overflow = open ? 'hidden' : '';
        });

        function closeMobileMenu() {
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
        mobileClose.addEventListener('click', closeMobileMenu);
        mobileMenu.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', closeMobileMenu);
        });

        /* ── Scroll fade-in (IntersectionObserver) ───────────── */
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    io.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.fade-in').forEach(function(el) {
            io.observe(el);
        });

        /* ── Category pill filter ────────────────────────────── */
        document.querySelectorAll('.category-pill').forEach(function(pill) {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.category-pill').forEach(function(p) {
                    p.classList.remove('active');
                });
                this.classList.add('active');
                var filter = this.dataset.filter;
                document.querySelectorAll('#featuredGrid .product-card').forEach(function(card) {
                    card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
                });
            });
        });

        /* ── Filter sidebar toggle ───────────────────────────── */
        function toggleFilter(titleEl) {
            titleEl.parentElement.classList.toggle('collapsed');
        }

        /* ── Quick View Modal ────────────────────────────────── */
        var modal = document.getElementById('quickViewModal');

        function openQuickView(card) {
            var name = card.dataset.name || 'Product';
            var price = card.dataset.price || '';
            var orig = card.dataset.orig || '';
            var img = card.dataset.img || 'blazer.svg';
            modalProductId = parseInt(card.dataset.id || 0, 10);
            var _imgBase = '<?= asset('images/products/') ?>';

            document.getElementById('modalName').textContent = name;
            document.getElementById('modalPrice').textContent = price;

            var origEl = document.getElementById('modalOrig');
            origEl.textContent = orig;
            origEl.style.display = orig ? '' : 'none';

            document.getElementById('modalImgMainImg').src = _imgBase + img;
            document.getElementById('modalImgMainImg').alt = name;

            /* Build 3 thumbnails */
            var thumbs = document.getElementById('modalThumbs');
            var variants = [img, 'tee.jpg', 'shorts.jpg'];
            thumbs.innerHTML = '';
            variants.forEach(function(v, i) {
                var t = document.createElement('div');
                t.className = 'modal-img-thumb' + (i === 0 ? ' active' : '');
                var ti = document.createElement('img');
                ti.src = _imgBase + v;
                ti.alt = '';
                ti.width = 60;
                ti.height = 75;
                ti.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;border-radius:inherit;';
                t.appendChild(ti);
                t.addEventListener('click', function() {
                    thumbs.querySelectorAll('.modal-img-thumb').forEach(function(x) {
                        x.classList.remove('active');
                    });
                    t.classList.add('active');
                    document.getElementById('modalImgMainImg').src = _imgBase + v;
                });
                thumbs.appendChild(t);
            });

            /* Reset quantity and size */
            modalQty = 1;
            modalSize = 'M';
            document.getElementById('qtyValue').textContent = '1';
            document.querySelectorAll('.modal-size-btn').forEach(function(b, i) {
                b.classList.toggle('active', i === 2); /* default M */
            });

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeQuickView() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeQuickView();
        });

        /* ── Modal size & quantity ───────────────────────────── */
        var modalSize = 'M'; /* default size */

        function selectModalSize(btn) {
            document.querySelectorAll('.modal-size-btn').forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            modalSize = btn.textContent.trim();
        }

        function changeQty(delta) {
            modalQty = Math.max(1, Math.min(99, modalQty + delta));
            document.getElementById('qtyValue').textContent = modalQty;
        }

        /* ── Cart ────────────────────────────────────────────── */
        var _csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var _csrfToken = _csrfMeta ? _csrfMeta.getAttribute('content') : '';
        var modalProductId = 0;
        var defaultSize = '<?= isset($sizes) && !empty($sizes) ? e($sizes[0]) : 'M' ?>';

        function addToCart(name, id, qty, size) {
            if (!id) {
                showToast('Could not add item.');
                return;
            }
            qty = qty || 1;
            size = size || defaultSize;
            fetch('<?= url('cart/add') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'product_id=' + encodeURIComponent(id) +
                    '&quantity=' + encodeURIComponent(qty) +
                    '&size=' + encodeURIComponent(size) +
                    '&_csrf_token=' + encodeURIComponent(_csrfToken)
            }).then(function(res) {
                return res.json();
            }).then(function(data) {
                if (data.success) {
                    cartCount++;
                    var badge = document.getElementById('cartBadge');
                    badge.textContent = cartCount;
                    badge.classList.add('active');
                    showToast('Added \u2014 ' + name);
                } else {
                    showToast(data.message || 'Could not add item.');
                }
            }).catch(function() {
                showToast('Could not add item.');
            });
        }

        function addToCartFromModal() {
            var name = document.getElementById('modalName').textContent;
            closeQuickView();
            addToCart(name + (modalQty > 1 ? ' \u00d7' + modalQty : ''), modalProductId, modalQty, modalSize);
        }

        /* ── Toast ───────────────────────────────────────────── */
        function showToast(msg) {
            var t = document.getElementById('toast');
            clearTimeout(toastTimer);
            t.textContent = '✓  ' + msg;
            t.classList.add('show');
            toastTimer = setTimeout(function() {
                t.classList.remove('show');
            }, 3000);
        }

        /* ── Hero Slideshow ──────────────────────────────────── */
        (function() {
            var DURATION = 6000;
            var slides = Array.from(document.querySelectorAll('.hero-slide'));
            var dots = Array.from(document.querySelectorAll('.hero-dot'));
            var current = 0;
            var timer;
            var progress = document.getElementById('heroProgress');
            var countEl = document.getElementById('heroCountCurrent');
            var content = document.getElementById('heroContent');

            var slideData = [{
                    tag: 'Spring / Summer 2026',
                    title: 'Wear Your<br><em>Story</em>',
                    sub: 'Curated essentials for those who define their own style. From luxury basics to signature statements.'
                },
                {
                    tag: 'New Arrivals 2026',
                    title: 'Define Your<br><em>Aesthetic</em>',
                    sub: 'Premium fabrics, timeless silhouettes. Pieces crafted to last and look great beyond every season.'
                },
                {
                    tag: 'The Collection',
                    title: 'Dress With<br><em>Intention</em>',
                    sub: 'Every piece is chosen with purpose — minimal, elevated, and built for the modern wardrobe.'
                },
            ];

            function startProgress() {
                if (!progress) return;
                progress.style.transition = 'none';
                progress.style.width = '0%';
                void progress.offsetWidth;
                progress.style.transition = 'width ' + DURATION + 'ms linear';
                progress.style.width = '100%';
            }

            function goTo(idx) {
                slides[current].classList.remove('active');
                dots[current].classList.remove('active');
                dots[current].setAttribute('aria-selected', 'false');
                current = ((idx % slides.length) + slides.length) % slides.length;
                slides[current].classList.add('active');
                dots[current].classList.add('active');
                dots[current].setAttribute('aria-selected', 'true');
                if (countEl) countEl.textContent = String(current + 1).padStart(2, '0');

                var d = slideData[current];
                content.classList.remove('is-visible');
                document.getElementById('heroTag').textContent = d.tag;
                document.getElementById('heroTitle').innerHTML = d.title;
                document.getElementById('heroSub').textContent = d.sub;
                void content.offsetWidth;
                content.classList.add('is-visible');
                startProgress();
            }

            function startTimer() {
                clearInterval(timer);
                timer = setInterval(function() {
                    goTo(current + 1);
                }, DURATION);
            }

            startProgress();
            startTimer();

            document.getElementById('heroPrev').addEventListener('click', function() {
                goTo(current - 1);
                startTimer();
            });
            document.getElementById('heroNext').addEventListener('click', function() {
                goTo(current + 1);
                startTimer();
            });
            dots.forEach(function(dot) {
                dot.addEventListener('click', function() {
                    goTo(parseInt(this.dataset.slide));
                    startTimer();
                });
            });

            var hero = document.getElementById('hero');
            hero.addEventListener('mouseenter', function() {
                clearInterval(timer);
            });
            hero.addEventListener('mouseleave', function() {
                startTimer();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    goTo(current - 1);
                    startTimer();
                }
                if (e.key === 'ArrowRight') {
                    goTo(current + 1);
                    startTimer();
                }
            });

            /* Mouse parallax */
            hero.addEventListener('mousemove', function(e) {
                var r = hero.getBoundingClientRect();
                var cx = (e.clientX - r.left) / r.width - 0.5;
                var cy = (e.clientY - r.top) / r.height - 0.5;
                var img = slides[current].querySelector('.hero-slide-img');
                if (img) img.style.transform = 'scale(1.01) translate(' + (cx * -20) + 'px,' + (cy * -12) + 'px)';
            });
            hero.addEventListener('mouseleave', function() {
                var img = slides[current].querySelector('.hero-slide-img');
                if (img) img.style.transform = '';
            });

            /* Animated stat counters */
            var countersRun = false;

            function runCounters() {
                if (countersRun) return;
                countersRun = true;
                document.querySelectorAll('.hero-stat-num[data-count]').forEach(function(el) {
                    var target = parseInt(el.dataset.count);
                    var suffix = el.dataset.suffix || '';
                    var steps = 60;
                    var inc = target / steps;
                    var cnt = 0;
                    var t = setInterval(function() {
                        cnt += inc;
                        if (cnt >= target) {
                            cnt = target;
                            clearInterval(t);
                        }
                        el.textContent = Math.round(cnt) + suffix;
                    }, 1400 / steps);
                });
            }
            var obs = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting) {
                    runCounters();
                    obs.disconnect();
                }
            }, {
                threshold: 0.3
            });
            obs.observe(hero);

            /* Touch swipe */
            var tx = 0;
            hero.addEventListener('touchstart', function(e) {
                tx = e.touches[0].clientX;
            }, {
                passive: true
            });
            hero.addEventListener('touchend', function(e) {
                var dx = e.changedTouches[0].clientX - tx;
                if (Math.abs(dx) > 50) {
                    goTo(dx < 0 ? current + 1 : current - 1);
                    startTimer();
                }
            });
        })();

        /* ── Newsletter ──────────────────────────────────────── */
        function handleNewsletter(e) {
            e.preventDefault();
            var input = e.target.querySelector('input');
            showToast('Subscribed: ' + input.value);
            input.value = '';
            return false;
        }

        /* ── Keyboard accessibility on cards ─────────────────── */
        document.querySelectorAll('.product-card').forEach(function(card) {
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openQuickView(card);
                }
            });
        });
    </script>

</body>

</html>