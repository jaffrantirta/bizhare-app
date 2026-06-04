<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patungan Laundry — Koperasi Sari Sedana</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green:       #2D6A4F;
      --green-mid:   #40916C;
      --green-light: #52B788;
      --green-pale:  #D8F3DC;
      --green-bg:    #F0FAF5;
      --orange:      #F4A261;
      --orange-dark: #E8894A;
      --bg:          #FEFAF4;
      --dark:        #1A1A2E;
      --gray:        #6B7280;
      --shadow:      0 4px 24px rgba(45,106,79,.08);
      --shadow-lg:   0 8px 40px rgba(45,106,79,.16);
      --r:           20px;
      --r-sm:        12px;
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--dark);
      line-height: 1.6;
      overflow-x: hidden;
    }

    h1,h2,h3,h4 { font-family: 'DM Serif Display', serif; line-height: 1.2; }

    /* ── NAVBAR ─────────────────────────────── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      background: rgba(254,250,244,.95);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(45,106,79,.08);
      padding: 0 2rem;
      transition: box-shadow .3s;
    }
    .navbar.scrolled { box-shadow: 0 2px 20px rgba(45,106,79,.12); }

    .nav-inner {
      max-width: 1200px; margin: 0 auto;
      display: flex; align-items: center; justify-content: space-between;
      height: 72px;
    }

    .nav-logo { text-decoration: none; }
    .nav-logo-name {
      font-family: 'DM Serif Display', serif;
      font-size: 1.15rem; color: var(--green);
      display: block; line-height: 1.1;
    }
    .nav-logo-tag {
      font-size: .68rem; color: var(--orange);
      font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    }

    .nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; }
    .nav-links a {
      text-decoration: none; color: var(--dark);
      font-size: .9rem; font-weight: 500;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--green); }

    .btn {
      display: inline-block; padding: .6rem 1.4rem;
      border-radius: 999px; font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 700; font-size: .9rem; text-decoration: none;
      cursor: pointer; border: none; transition: all .2s;
    }
    .btn-orange {
      background: var(--orange); color: #fff;
    }
    .btn-orange:hover {
      background: var(--orange-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(244,162,97,.4);
    }
    .btn-ghost-dark {
      background: transparent; color: var(--green);
      border: 2px solid var(--green);
    }
    .btn-ghost-dark:hover { background: var(--green); color: #fff; }
    .btn-ghost-white {
      background: rgba(255,255,255,.1); color: #fff;
      border: 2px solid rgba(255,255,255,.4);
    }
    .btn-ghost-white:hover { background: rgba(255,255,255,.2); border-color: #fff; }
    .btn-lg { padding: .9rem 2rem; font-size: 1rem; }

    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      cursor: pointer; padding: 4px;
    }
    .hamburger span {
      display: block; width: 24px; height: 2px;
      background: var(--dark); border-radius: 2px; transition: all .3s;
    }
    .mobile-nav {
      display: none; position: fixed;
      top: 72px; left: 0; right: 0;
      background: rgba(254,250,244,.98);
      backdrop-filter: blur(12px);
      padding: 1.5rem 2rem;
      box-shadow: 0 8px 30px rgba(0,0,0,.1);
      z-index: 999; flex-direction: column; gap: 1rem;
    }
    .mobile-nav.open { display: flex; }
    .mobile-nav a {
      text-decoration: none; color: var(--dark);
      font-weight: 500; font-size: 1rem;
      padding: .5rem 0; border-bottom: 1px solid rgba(45,106,79,.08);
    }
    .mobile-nav a:last-child { border-bottom: none; }

    /* ── HERO ────────────────────────────────── */
    .hero {
      min-height: 100vh;
      background: linear-gradient(140deg, #1B4332 0%, #2D6A4F 55%, #40916C 100%);
      display: flex; align-items: center;
      position: relative; overflow: hidden;
      padding: 7rem 2rem 4rem;
    }
    .hero::before {
      content: '';
      position: absolute; inset: 0; pointer-events: none;
      background:
        radial-gradient(ellipse at 80% 15%, rgba(82,183,136,.25) 0%, transparent 55%),
        radial-gradient(ellipse at 10% 85%, rgba(27,67,50,.5) 0%, transparent 50%);
    }

    /* bubbles */
    .bubbles { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
    .bubble {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.14);
      animation: riseUp linear infinite;
    }
    @keyframes riseUp {
      0%   { transform: translateY(110vh) scale(.7); opacity: 0; }
      8%   { opacity: 1; }
      92%  { opacity: 1; }
      100% { transform: translateY(-15vh) scale(1.15); opacity: 0; }
    }
    .bubble:nth-child(1)  { width:40px;height:40px; left:5%;  animation-duration:13s; animation-delay:0s;   }
    .bubble:nth-child(2)  { width:20px;height:20px; left:13%; animation-duration:10s; animation-delay:1.8s; }
    .bubble:nth-child(3)  { width:65px;height:65px; left:23%; animation-duration:16s; animation-delay:3.2s; }
    .bubble:nth-child(4)  { width:30px;height:30px; left:36%; animation-duration:12s; animation-delay:.6s;  }
    .bubble:nth-child(5)  { width:50px;height:50px; left:50%; animation-duration:14s; animation-delay:2.4s; }
    .bubble:nth-child(6)  { width:22px;height:22px; left:63%; animation-duration:11s; animation-delay:4.2s; }
    .bubble:nth-child(7)  { width:48px;height:48px; left:73%; animation-duration:15s; animation-delay:1.1s; }
    .bubble:nth-child(8)  { width:15px;height:15px; left:83%; animation-duration:9s;  animation-delay:2.8s; }
    .bubble:nth-child(9)  { width:72px;height:72px; left:89%; animation-duration:17s; animation-delay:.9s;  }
    .bubble:nth-child(10) { width:35px;height:35px; left:42%; animation-duration:13s; animation-delay:3.7s; }
    .bubble:nth-child(11) { width:18px;height:18px; left:18%; animation-duration:11s; animation-delay:5.1s; }
    .bubble:nth-child(12) { width:55px;height:55px; left:58%; animation-duration:18s; animation-delay:1.4s; }

    .hero-inner { max-width:1200px; margin:0 auto; width:100%; position:relative; z-index:1; }
    .hero-content { max-width: 680px; }

    .hero-badge {
      display: inline-flex; align-items: center; gap: .5rem;
      background: rgba(244,162,97,.18);
      border: 1px solid rgba(244,162,97,.4);
      color: var(--orange); padding: .4rem 1rem; border-radius: 999px;
      font-size: .78rem; font-weight: 700;
      letter-spacing: .06em; text-transform: uppercase;
      margin-bottom: 1.5rem;
    }

    .hero h1 {
      font-size: clamp(2.1rem,5vw,3.8rem);
      color: #fff; margin-bottom: 1.2rem;
    }
    .hero h1 em { font-style: italic; color: var(--orange); }

    .hero-sub {
      font-size: 1.08rem; color: rgba(255,255,255,.85);
      margin-bottom: 2rem; max-width: 520px; line-height: 1.75;
    }

    .hero-ctas { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:3rem; }

    .trust-row { display:flex; flex-wrap:wrap; gap:.75rem; }
    .trust-badge {
      display: flex; align-items: center; gap: .5rem;
      background: rgba(255,255,255,.1);
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 999px; padding: .4rem 1rem;
      font-size: .82rem; font-weight: 600; color: #fff;
    }
    .trust-check {
      width: 18px; height: 18px; background: var(--orange);
      border-radius: 50%; display:flex; align-items:center; justify-content:center;
      font-size: .65rem; flex-shrink:0;
    }

    /* ── COMMON SECTION ──────────────────────── */
    section { padding: 5rem 2rem; }
    .container { max-width:1200px; margin:0 auto; }
    .section-label {
      font-size: .78rem; font-weight: 700; letter-spacing: .12em;
      text-transform: uppercase; color: var(--orange); margin-bottom: .75rem;
    }
    .section-title { font-size: clamp(1.8rem,3.5vw,2.7rem); margin-bottom: 1rem; }
    .section-sub { font-size:1.05rem; color:var(--gray); max-width:560px; line-height:1.75; }
    .section-hd { margin-bottom: 3.5rem; }
    .text-center { text-align:center; }
    .mx-auto { margin-left:auto; margin-right:auto; }

    /* ── PROBLEM ─────────────────────────────── */
    .prob-grid {
      display:grid; grid-template-columns:repeat(auto-fit,minmax(270px,1fr));
      gap:1.5rem; margin-bottom:2.5rem;
    }
    .prob-card {
      background:#fff; border-radius:var(--r); padding:2rem;
      box-shadow:var(--shadow); border:1px solid rgba(45,106,79,.06);
      position:relative; overflow:hidden; transition:all .3s;
    }
    .prob-card::before {
      content:''; position:absolute; top:0; left:0; right:0; height:4px;
      background:linear-gradient(90deg,var(--orange),var(--orange-dark));
    }
    .prob-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); }
    .prob-emoji { font-size:2rem; margin-bottom:1rem; display:block; }
    .prob-card h3 {
      font-family:'Plus Jakarta Sans',sans-serif;
      font-size:.98rem; font-weight:700; margin-bottom:.5rem; line-height:1.4;
    }
    .prob-card p { font-size:.88rem; color:var(--gray); line-height:1.65; }

    .prob-transition {
      text-align:center; font-size:1.05rem; font-weight:600;
      color:var(--green); background:var(--green-pale);
      border-radius:var(--r-sm); padding:1.25rem 2rem;
      max-width:600px; margin:0 auto;
    }

    /* ── HOW IT WORKS ────────────────────────── */
    .how-section { background:var(--green-bg); }
    .steps-grid {
      display:grid; grid-template-columns:repeat(4,1fr);
      gap:1.5rem; position:relative;
    }
    .steps-grid::before {
      content:''; position:absolute;
      top:2.7rem; left:12%; right:12%; height:2px;
      background:repeating-linear-gradient(
        90deg, var(--green-pale) 0,var(--green-pale) 8px,
        transparent 8px,transparent 16px
      );
      z-index:0;
    }
    .step-card {
      background:#fff; border-radius:var(--r); padding:2rem 1.5rem;
      text-align:center; box-shadow:var(--shadow);
      border:1px solid rgba(45,106,79,.08);
      position:relative; z-index:1; transition:all .3s;
    }
    .step-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); }
    .step-num {
      width:52px; height:52px;
      background:linear-gradient(135deg,var(--green),var(--green-mid));
      color:#fff; border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      font-family:'DM Serif Display',serif; font-size:1.4rem;
      margin:0 auto 1rem;
      box-shadow:0 4px 16px rgba(45,106,79,.3);
    }
    .step-icon { font-size:1.8rem; margin-bottom:.75rem; display:block; }
    .step-card h3 {
      font-family:'Plus Jakarta Sans',sans-serif;
      font-size:.93rem; font-weight:700; margin-bottom:.5rem; line-height:1.4;
    }
    .step-card p { font-size:.85rem; color:var(--gray); line-height:1.65; }

    /* ── WHY LAUNDRY ─────────────────────────── */
    .why-grid {
      display:grid; grid-template-columns:repeat(auto-fit,minmax(270px,1fr));
      gap:1.5rem;
    }
    .why-card {
      background:#fff; border-radius:var(--r); padding:2.5rem 2rem;
      box-shadow:var(--shadow); border:1px solid rgba(45,106,79,.06);
      transition:all .3s;
    }
    .why-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); border-color:var(--green); }
    .why-icon {
      width:56px; height:56px;
      background:linear-gradient(135deg,var(--green-pale),#B7E4C7);
      border-radius:var(--r-sm);
      display:flex; align-items:center; justify-content:center;
      font-size:1.6rem; margin-bottom:1.25rem;
    }
    .why-card h3 { font-size:1.2rem; margin-bottom:.35rem; }
    .why-stat {
      font-family:'DM Serif Display',serif;
      font-size:2.4rem; color:var(--green);
      display:block; margin-bottom:.5rem; line-height:1;
    }
    .why-card p { font-size:.9rem; color:var(--gray); line-height:1.7; }

    /* ── CALCULATOR ──────────────────────────── */
    .calc-section {
      background:linear-gradient(140deg,#1B4332 0%,#2D6A4F 100%);
      position:relative; overflow:hidden;
    }
    .calc-section::after {
      content:''; position:absolute;
      top:-40%; right:-15%; width:500px; height:500px;
      border-radius:50%; background:rgba(82,183,136,.1); pointer-events:none;
    }
    .calc-section .section-title { color:#fff; }
    .calc-section .section-sub   { color:rgba(255,255,255,.7); }

    .calc-wrap {
      background:rgba(255,255,255,.07);
      backdrop-filter:blur(10px);
      border:1px solid rgba(255,255,255,.12);
      border-radius:var(--r); padding:2.5rem;
      max-width:680px; margin:0 auto;
      position:relative; z-index:1;
    }

    .calc-label {
      display:block; font-size:.9rem; font-weight:600;
      color:rgba(255,255,255,.9); margin-bottom:.75rem;
    }
    .calc-input-row {
      display:flex; align-items:center;
      background:rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.22);
      border-radius:var(--r-sm); overflow:hidden;
      margin-bottom:1rem;
    }
    .calc-prefix {
      padding:0 1rem; font-weight:700;
      color:var(--orange); font-size:1rem; white-space:nowrap;
    }
    .calc-input {
      flex:1; background:transparent; border:none; outline:none;
      color:#fff; font-family:'Plus Jakarta Sans',sans-serif;
      font-size:1.2rem; font-weight:700; padding:1rem .5rem;
    }
    .calc-input::placeholder { color:rgba(255,255,255,.35); }

    .calc-slider {
      width:100%; -webkit-appearance:none; appearance:none;
      height:6px; border-radius:3px;
      background:rgba(255,255,255,.2); outline:none; margin-bottom:2rem;
    }
    .calc-slider::-webkit-slider-thumb {
      -webkit-appearance:none; width:22px; height:22px;
      border-radius:50%; background:var(--orange); cursor:pointer;
      box-shadow:0 2px 8px rgba(244,162,97,.55);
    }
    .calc-slider::-moz-range-thumb {
      width:22px; height:22px; border:none;
      border-radius:50%; background:var(--orange); cursor:pointer;
    }

    .calc-results { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem; }
    .calc-res {
      background:rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.12);
      border-radius:var(--r-sm); padding:1.25rem; text-align:center;
    }
    .calc-res.highlight { background:var(--orange); border-color:var(--orange); }
    .calc-res-label {
      font-size:.72rem; font-weight:700; color:rgba(255,255,255,.65);
      text-transform:uppercase; letter-spacing:.06em;
      display:block; margin-bottom:.5rem;
    }
    .calc-res.highlight .calc-res-label { color:rgba(255,255,255,.85); }
    .calc-res-val {
      font-family:'DM Serif Display',serif;
      font-size:1.25rem; color:#fff; display:block; line-height:1.2;
    }
    .calc-note {
      font-size:.77rem; color:rgba(255,255,255,.48);
      font-style:italic; text-align:center; line-height:1.6;
    }

    /* ── MEMBER BENEFITS ─────────────────────── */
    .benefits-grid {
      display:grid; grid-template-columns:1fr 1fr;
      gap:4rem; align-items:center;
    }
    .benefits-list { list-style:none; display:flex; flex-direction:column; gap:1rem; }
    .benefit-item {
      display:flex; align-items:flex-start; gap:1rem;
      padding:1rem 1.25rem; background:#fff;
      border-radius:var(--r-sm); box-shadow:var(--shadow);
      border:1px solid rgba(45,106,79,.08); transition:all .2s;
    }
    .benefit-item:hover { border-color:var(--green); transform:translateX(4px); }
    .benefit-check {
      width:28px; height:28px; flex-shrink:0; margin-top:2px;
      background:linear-gradient(135deg,var(--green),var(--green-mid));
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      color:#fff; font-size:.75rem;
    }
    .benefit-text h4 {
      font-family:'Plus Jakarta Sans',sans-serif;
      font-size:.93rem; font-weight:700; margin-bottom:.2rem;
    }
    .benefit-text p { font-size:.84rem; color:var(--gray); line-height:1.55; }

    .vis-card {
      background:linear-gradient(135deg,var(--green),var(--green-mid));
      border-radius:var(--r); padding:2.5rem;
      text-align:center; color:#fff;
      box-shadow:0 20px 60px rgba(45,106,79,.25);
    }
    .vis-icon {
      width:80px; height:80px; background:rgba(255,255,255,.15);
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      font-size:2.5rem; margin:0 auto 1.5rem;
    }
    .vis-card h3 { font-size:1.4rem; color:#fff; margin-bottom:.4rem; }
    .vis-card > p { font-size:.9rem; color:rgba(255,255,255,.8); margin-bottom:1.5rem; }
    .lic-badge {
      display:inline-block;
      background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3);
      border-radius:999px; padding:.4rem 1rem;
      font-size:.8rem; font-weight:600; color:#fff;
    }
    .stat-row { display:flex; gap:1rem; margin-top:1.5rem; }
    .stat-box {
      flex:1; background:rgba(255,255,255,.1);
      border-radius:var(--r-sm); padding:1rem; text-align:center;
    }
    .stat-val { font-family:'DM Serif Display',serif; font-size:1.6rem; color:var(--orange); }
    .stat-lbl { font-size:.72rem; color:rgba(255,255,255,.65); display:block; margin-top:.2rem; }

    /* ── FAQ ─────────────────────────────────── */
    .faq-section { background:var(--green-bg); }
    .faq-list { max-width:740px; margin:0 auto; display:flex; flex-direction:column; gap:1rem; }
    .faq-item {
      background:#fff; border-radius:var(--r-sm);
      box-shadow:var(--shadow); border:1px solid rgba(45,106,79,.08); overflow:hidden;
    }
    .faq-q {
      padding:1.25rem 1.5rem; display:flex;
      justify-content:space-between; align-items:center;
      cursor:pointer; font-weight:700; font-size:.95rem; color:var(--dark);
      transition:background .2s; gap:1rem;
    }
    .faq-q:hover { background:var(--green-bg); }
    .faq-icon {
      width:28px; height:28px; border-radius:50%;
      background:var(--green-pale); color:var(--green);
      display:flex; align-items:center; justify-content:center;
      font-size:1.2rem; flex-shrink:0;
      transition:transform .3s, background .2s;
    }
    .faq-item.open .faq-icon { transform:rotate(45deg); background:var(--green); color:#fff; }
    .faq-a { max-height:0; overflow:hidden; transition:max-height .4s ease; }
    .faq-item.open .faq-a { max-height:400px; }
    .faq-a-inner {
      padding:0 1.5rem 1.25rem;
      font-size:.9rem; color:var(--gray); line-height:1.75;
    }

    /* ── FINAL CTA ───────────────────────────── */
    .cta-section { background:var(--bg); }
    .cta-box {
      background:linear-gradient(135deg,var(--orange) 0%,var(--orange-dark) 100%);
      border-radius:28px; padding:4rem 3rem;
      max-width:760px; margin:0 auto;
      position:relative; overflow:hidden; text-align:center;
    }
    .cta-box::before {
      content:''; position:absolute; top:-30%; right:-10%;
      width:300px; height:300px; border-radius:50%;
      background:rgba(255,255,255,.1); pointer-events:none;
    }
    .cta-box::after {
      content:''; position:absolute; bottom:-40%; left:-5%;
      width:250px; height:250px; border-radius:50%;
      background:rgba(255,255,255,.07); pointer-events:none;
    }
    .cta-box h2 {
      font-size:clamp(1.8rem,4vw,2.7rem);
      color:#fff; margin-bottom:1rem; position:relative; z-index:1;
    }
    .cta-box p {
      color:rgba(255,255,255,.9); font-size:1.05rem;
      margin:0 auto 2rem; max-width:460px; line-height:1.7;
      position:relative; z-index:1;
    }
    .cta-btns { display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; position:relative; z-index:1; }
    .btn-white { background:#fff; color:var(--orange); font-weight:800; }
    .btn-white:hover { background:#FFF8F0; transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.15); }
    .btn-outline-white { background:transparent; color:#fff; border:2px solid rgba(255,255,255,.55); }
    .btn-outline-white:hover { background:rgba(255,255,255,.15); border-color:#fff; }
    .cta-note { text-align:center; font-size:.85rem; color:var(--gray); margin-top:1.5rem; line-height:1.7; }

    /* ── FOOTER ──────────────────────────────── */
    footer { background:var(--dark); color:rgba(255,255,255,.65); padding:3rem 2rem; }
    .footer-inner {
      max-width:1200px; margin:0 auto;
      display:grid; grid-template-columns:2fr 1fr 1fr; gap:3rem;
    }
    .footer-brand h3 { font-family:'DM Serif Display',serif; font-size:1.25rem; color:#fff; margin-bottom:.2rem; }
    .footer-tag { font-size:.75rem; color:var(--orange); font-weight:700; margin-bottom:1rem; display:block; }
    .footer-brand p { font-size:.85rem; line-height:1.7; max-width:300px; }
    .footer-col h4 {
      font-family:'Plus Jakarta Sans',sans-serif;
      font-size:.8rem; font-weight:700; color:#fff;
      margin-bottom:1rem; text-transform:uppercase; letter-spacing:.06em;
    }
    .footer-col ul { list-style:none; display:flex; flex-direction:column; gap:.5rem; }
    .footer-col ul li a {
      text-decoration:none; color:rgba(255,255,255,.55);
      font-size:.9rem; transition:color .2s;
    }
    .footer-col ul li a:hover { color:var(--orange); }
    .footer-btm {
      border-top:1px solid rgba(255,255,255,.08);
      margin-top:2.5rem; padding-top:1.5rem;
      text-align:center; font-size:.78rem; color:rgba(255,255,255,.35);
      line-height:1.8;
    }

    /* ── FADE-IN ANIMATION ───────────────────── */
    .fi { opacity:0; transform:translateY(22px); transition:opacity .6s,transform .6s; }
    .fi.vis { opacity:1; transform:translateY(0); }

    /* ── RESPONSIVE ──────────────────────────── */
    @media(max-width:900px) {
      .steps-grid { grid-template-columns:repeat(2,1fr); }
      .steps-grid::before { display:none; }
      .benefits-grid { grid-template-columns:1fr; gap:2rem; }
      .footer-inner { grid-template-columns:1fr 1fr; }
      .footer-brand { grid-column:1/-1; }
    }
    @media(max-width:640px) {
      .nav-links,.nav-cta { display:none; }
      .hamburger { display:flex; }
      section { padding:3.5rem 1.5rem; }
      .steps-grid { grid-template-columns:1fr; }
      .calc-results { grid-template-columns:1fr; }
      .footer-inner { grid-template-columns:1fr; }
      .cta-box { padding:2.5rem 1.5rem; }
      .hero { padding:6rem 1.5rem 3rem; }
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ─────────────────────────────── -->
<nav class="navbar" id="navbar">
  <div class="nav-inner">
    <a href="#" class="nav-logo">
      <span class="nav-logo-name">Koperasi Sari Sedana</span>
      <span class="nav-logo-tag">Usaha Bersama, Untung Bersama</span>
    </a>
    <ul class="nav-links">
      <li><a href="#cara-kerja">Cara Kerja</a></li>
      <li><a href="#keuntungan">Keuntungan</a></li>
      <li><a href="#simulasi">Simulasi</a></li>
      <li><a href="#faq">FAQ</a></li>
    </ul>
    <a href="#daftar" class="btn btn-orange nav-cta">Daftar Sekarang</a>
    <div class="hamburger" id="hamburger" aria-label="Menu" role="button" tabindex="0">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

<div class="mobile-nav" id="mobileNav">
  <a href="#cara-kerja">Cara Kerja</a>
  <a href="#keuntungan">Keuntungan</a>
  <a href="#simulasi">Simulasi</a>
  <a href="#faq">FAQ</a>
  <a href="#daftar" class="btn btn-orange" style="text-align:center;margin-top:.5rem">Daftar Sekarang</a>
</div>

<!-- ── HERO ───────────────────────────────── -->
<section class="hero" id="hero">
  <div class="bubbles" aria-hidden="true">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
  </div>
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge">🧺 Eksklusif Anggota Koperasi &middot; Program Perdana</div>
      <h1>Patungan Buka Laundry.<br><em>Untungnya Kita Bagi Bersama.</em></h1>
      <p class="hero-sub">
        Anggota Koperasi Sari Sedana kini bisa ikut mendirikan outlet laundry bersama dan menikmati
        bagi hasil dari keuntungan bisnis nyata — setiap kuartal, transparan, aman.
      </p>
      <div class="hero-ctas">
        <a href="#daftar" class="btn btn-orange btn-lg">Ikut Patungan Sekarang</a>
        <a href="#cara-kerja" class="btn btn-ghost-white btn-lg">Pelajari Dulu</a>
      </div>
      <div class="trust-row">
        <div class="trust-badge"><span class="trust-check">✓</span>Khusus Anggota Koperasi</div>
        <div class="trust-badge"><span class="trust-check">✓</span>Bisnis Laundry Nyata</div>
        <div class="trust-badge"><span class="trust-check">✓</span>Estimasi 8–12% / Kuartal</div>
        <div class="trust-badge"><span class="trust-check">✓</span>Modal Mulai Rp 1,5 Juta</div>
      </div>
    </div>
  </div>
</section>

<!-- ── PROBLEM ────────────────────────────── -->
<section>
  <div class="container">
    <div class="section-hd">
      <p class="section-label">Masalah yang Sering Kita Rasakan</p>
      <h2 class="section-title">Punya Uang, Tapi Bingung<br>Mau Diapain?</h2>
    </div>
    <div class="prob-grid">
      <div class="prob-card fi">
        <span class="prob-emoji">🏦</span>
        <h3>Ditabung di bank, bunganya kecil banget</h3>
        <p>Bunga tabungan biasa 1–2% per tahun, sementara inflasi lebih tinggi. Nilai uangmu nyata-nyata berkurang tiap tahun.</p>
      </div>
      <div class="prob-card fi">
        <span class="prob-emoji">😰</span>
        <h3>Mau buka usaha sendiri, tapi modalnya besar dan risikonya tinggi</h3>
        <p>Sewa tempat, beli mesin, rekrut karyawan — belum apa-apa sudah habis puluhan juta. Belum lagi risiko kalau gagal sendirian.</p>
      </div>
      <div class="prob-card fi">
        <span class="prob-emoji">🚫</span>
        <h3>Investasi online banyak yang nggak jelas dan nggak bisa dipercaya</h3>
        <p>Robot trading, deposito abal-abal, saham gorengan — siapa yang bisa jamin? Banyak yang ujung-ujungnya kabur membawa uang anggota.</p>
      </div>
    </div>
    <div class="prob-transition fi">
      💡 Makanya kita bikin ini — bareng-bareng, aman, nyata.
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS ───────────────────────── -->
<section class="how-section" id="cara-kerja">
  <div class="container">
    <div class="section-hd text-center">
      <p class="section-label">Cara Kerja</p>
      <h2 class="section-title">Sesederhana Ini Cara Kerjanya</h2>
      <p class="section-sub mx-auto">Empat langkah mudah untuk mulai berinvestasi bersama anggota koperasi.</p>
    </div>
    <div class="steps-grid">
      <div class="step-card fi">
        <div class="step-num">1</div>
        <span class="step-icon">🪪</span>
        <h3>Pastikan Kamu Anggota Koperasi</h3>
        <p>Program ini eksklusif hanya untuk anggota resmi Koperasi Konsumen Sari Sedana yang aktif dan terdaftar.</p>
      </div>
      <div class="step-card fi">
        <div class="step-num">2</div>
        <span class="step-icon">💰</span>
        <h3>Pilih Jumlah Modal yang Kamu Sertakan</h3>
        <p>Tentukan nominal investasimu, minimal Rp 1.500.000. Semakin besar modal, semakin besar bagi hasilmu.</p>
      </div>
      <div class="step-card fi">
        <div class="step-num">3</div>
        <span class="step-icon">📲</span>
        <h3>Lakukan Pembayaran</h3>
        <p>Transfer via bank atau scan QRIS ke rekening resmi koperasi. Kirim bukti pembayaran ke admin untuk konfirmasi.</p>
      </div>
      <div class="step-card fi">
        <div class="step-num">4</div>
        <span class="step-icon">📊</span>
        <h3>Terima Laporan &amp; Bagi Hasil Tiap Kuartal</h3>
        <p>Setiap 3 bulan kamu terima laporan keuangan lengkap dan bagi hasil dari keuntungan nyata bisnis laundry.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── WHY LAUNDRY ────────────────────────── -->
<section id="keuntungan">
  <div class="container">
    <div class="section-hd">
      <p class="section-label">Kenapa Bisnis Ini?</p>
      <h2 class="section-title">Kenapa Bisnis Laundry?</h2>
      <p class="section-sub">Bukan bisnis asal pilih. Ada alasan kuat mengapa laundry menjadi pilihan terbaik untuk dipatungan bersama.</p>
    </div>
    <div class="why-grid">
      <div class="why-card fi">
        <div class="why-icon">📈</div>
        <h3>Permintaan Stabil</h3>
        <span class="why-stat">365 hari</span>
        <p>Laundry adalah kebutuhan harian yang tidak terpengaruh tren musiman. Selama orang pakai baju, bisnis ini jalan terus.</p>
      </div>
      <div class="why-card fi">
        <div class="why-icon">🧮</div>
        <h3>Modal Terukur</h3>
        <span class="why-stat">≤ Rp 80 Jt</span>
        <p>Biaya operasional laundry dapat diprediksi dengan baik — tidak seperti bisnis F&amp;B yang penuh variabel tak terduga.</p>
      </div>
      <div class="why-card fi">
        <div class="why-icon">⚡</div>
        <h3>Cepat Balik Modal</h3>
        <span class="why-stat">12–18 Bln</span>
        <p>Bisnis laundry skala komunitas umumnya mencapai BEP dalam 12–18 bulan, jauh lebih cepat dari banyak jenis usaha lain.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── CALCULATOR ─────────────────────────── -->
<section class="calc-section" id="simulasi">
  <div class="container">
    <div class="section-hd text-center">
      <p class="section-label">Simulasi Investasi</p>
      <h2 class="section-title">Kira-Kira Dapat Berapa?</h2>
      <p class="section-sub mx-auto" style="color:rgba(255,255,255,.72)">Masukkan jumlah modal yang ingin kamu sertakan dan lihat estimasi hasilnya secara langsung.</p>
    </div>

    <div class="calc-wrap">
      <label class="calc-label" for="modalInput">Jumlah Modal Saya</label>
      <div class="calc-input-row">
        <span class="calc-prefix">Rp</span>
        <input type="number" id="modalInput" class="calc-input"
               value="1500000" min="1500000" step="500000"
               placeholder="1.500.000" aria-label="Jumlah modal">
      </div>
      <input type="range" id="modalSlider" class="calc-slider"
             min="1500000" max="20000000" step="500000" value="1500000"
             aria-label="Slider jumlah modal">

      <div class="calc-results">
        <div class="calc-res">
          <span class="calc-res-label">Bagi Hasil / Kuartal</span>
          <span class="calc-res-val" id="resKuartal">Rp 150.000</span>
        </div>
        <div class="calc-res">
          <span class="calc-res-label">Estimasi Per Tahun</span>
          <span class="calc-res-val" id="resTahun">Rp 600.000</span>
        </div>
        <div class="calc-res highlight">
          <span class="calc-res-label">Total Kembali (1 Tahun)</span>
          <span class="calc-res-val" id="resTotal">Rp 2.100.000</span>
        </div>
      </div>

      <p class="calc-note">
        * Angka di atas adalah ilustrasi berdasarkan estimasi kinerja bisnis (10% per kuartal).
        Bagi hasil aktual bergantung pada keuntungan nyata usaha laundry.
        Bukan merupakan jaminan imbal hasil.
      </p>
    </div>
  </div>
</section>

<!-- ── MEMBER BENEFITS ────────────────────── -->
<section>
  <div class="container">
    <div class="section-hd">
      <p class="section-label">Hak Anggota Koperasi</p>
      <h2 class="section-title">Kenapa Harus Anggota Koperasi?</h2>
      <p class="section-sub">Bukan sekadar investasi — kamu punya hak dan perlindungan penuh sebagai anggota koperasi resmi yang diakui negara.</p>
    </div>
    <div class="benefits-grid">
      <ul class="benefits-list">
        <li class="benefit-item fi">
          <div class="benefit-check">✓</div>
          <div class="benefit-text">
            <h4>Hak Suara dalam Rapat Anggota</h4>
            <p>Kamu bisa ikut menentukan arah bisnis dan kebijakan koperasi setiap tahunnya — bukan sekadar investor pasif.</p>
          </div>
        </li>
        <li class="benefit-item fi">
          <div class="benefit-check">✓</div>
          <div class="benefit-text">
            <h4>Laporan Keuangan Transparan</h4>
            <p>Setiap kuartal kamu terima laporan lengkap: pemasukan, pengeluaran, laba bersih, dan bagi hasil.</p>
          </div>
        </li>
        <li class="benefit-item fi">
          <div class="benefit-check">✓</div>
          <div class="benefit-text">
            <h4>Dilindungi Regulasi Koperasi Resmi</h4>
            <p>Beroperasi di bawah izin resmi dan diawasi Dinas Koperasi — bukan platform abal-abal atau investasi ilegal.</p>
          </div>
        </li>
        <li class="benefit-item fi">
          <div class="benefit-check">✓</div>
          <div class="benefit-text">
            <h4>Prioritas Program Investasi Berikutnya</h4>
            <p>Anggota pendiri round pertama ini mendapat prioritas di program perluasan bisnis selanjutnya.</p>
          </div>
        </li>
        <li class="benefit-item fi">
          <div class="benefit-check">✓</div>
          <div class="benefit-text">
            <h4>Mekanisme Exit Modal yang Transparan</h4>
            <p>Pengembalian modal diatur dengan jelas dalam perjanjian anggota — ada jalur yang terstruktur dan adil.</p>
          </div>
        </li>
      </ul>

      <div class="fi">
        <div class="vis-card">
          <div class="vis-icon">🤝</div>
          <h3>Koperasi Konsumen<br>Sari Sedana</h3>
          <p>Resmi beroperasi di bawah payung hukum koperasi Indonesia. Bukan pinjol, bukan MLM, bukan platform investasi ilegal.</p>
          <span class="lic-badge">✓ Terdaftar &amp; Berlisensi Resmi</span>
          <div class="stat-row">
            <div class="stat-box">
              <div class="stat-val">100%</div>
              <span class="stat-lbl">Transparan</span>
            </div>
            <div class="stat-box">
              <div class="stat-val">Nyata</div>
              <span class="stat-lbl">Bisnis Fisik</span>
            </div>
            <div class="stat-box">
              <div class="stat-val">Aman</div>
              <span class="stat-lbl">Berlisensi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── FAQ ────────────────────────────────── -->
<section class="faq-section" id="faq">
  <div class="container">
    <div class="section-hd text-center">
      <p class="section-label">Pertanyaan Umum</p>
      <h2 class="section-title">Ada yang Mau Ditanyakan?</h2>
      <p class="section-sub mx-auto">Kami jawab dulu yang paling sering ditanya.</p>
    </div>
    <div class="faq-list">

      <div class="faq-item">
        <div class="faq-q">
          Apakah saya harus jadi anggota koperasi dulu untuk ikut?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Ya, program ini memang eksklusif untuk anggota Koperasi Sari Sedana. Jika kamu belum menjadi anggota, kamu bisa mendaftar terlebih dahulu melalui pengurus koperasi, kemudian mengikuti program investasi ini. Hubungi admin kami untuk panduan lengkapnya.
          </div>
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-q">
          Berapa lama periode investasinya?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Program investasi ini dirancang untuk jangka menengah, minimal 1 tahun. Bagi hasil dibayarkan setiap kuartal (3 bulan sekali). Ketentuan lebih lanjut tentang periode dan mekanisme exit modal diatur dalam perjanjian resmi anggota yang akan ditandatangani bersama.
          </div>
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-q">
          Bagaimana cara saya memantau perkembangan bisnis?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Setiap kuartal kamu menerima laporan keuangan lengkap yang menunjukkan pemasukan, pengeluaran, dan laba bersih. Sebagai anggota koperasi, kamu juga berhak hadir dalam Rapat Anggota Tahunan (RAT) untuk melihat laporan secara langsung dan mengajukan pertanyaan.
          </div>
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-q">
          Apakah bagi hasil 8–12% itu dijamin?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Angka 8–12% per kuartal adalah estimasi berdasarkan proyeksi kinerja bisnis laundry yang realistis. Bagi hasil aktual bergantung pada keuntungan nyata yang dihasilkan. Ini bukan deposito dengan return tetap — ini investasi dalam bisnis nyata. Jika bisnis untung besar, bagi hasilmu bisa lebih tinggi. Jika sedang ada tantangan, bisa lebih rendah.
          </div>
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-q">
          Bagaimana cara pembayaran modalnya?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Pembayaran dapat dilakukan melalui transfer bank ke rekening resmi koperasi, atau menggunakan QRIS yang akan kami sediakan. Setelah transfer, kirim bukti pembayaran ke admin koperasi untuk diproses dan mendapat konfirmasi resmi beserta dokumen perjanjian anggota investor.
          </div>
        </div>
      </div>

      <div class="faq-item">
        <div class="faq-q">
          Bagaimana jika bisnis laundrynya merugi di satu kuartal?
          <div class="faq-icon">+</div>
        </div>
        <div class="faq-a">
          <div class="faq-a-inner">
            Dalam sistem koperasi, anggota berbagi risiko dan keuntungan secara bersama-sama. Jika bisnis mengalami kerugian di satu kuartal, bagi hasil di periode tersebut mungkin lebih kecil atau ditangguhkan. Modal pokokmu tetap tercatat sebagai penyertaan anggota dan akan dikembalikan sesuai mekanisme yang telah disepakati di awal.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── FINAL CTA ───────────────────────────── -->
<section class="cta-section" id="daftar">
  <div class="container">
    <div class="cta-box">
      <h2>Siap Gabung Bareng Kami?</h2>
      <p>Jangan lewatkan kesempatan jadi bagian dari founding round bisnis laundry komunitas ini. Kursi terbatas khusus untuk anggota koperasi.</p>
      <div class="cta-btns">
        <a href="#" class="btn btn-white btn-lg">💬 Hubungi via WhatsApp</a>
        <a href="#cara-kerja" class="btn btn-outline-white btn-lg">Pelajari Lagi</a>
      </div>
    </div>
    <p class="cta-note">
      📋 Atau hubungi pengurus koperasi langsung untuk informasi pendaftaran.<br>
      Pembayaran via Transfer Bank atau QRIS · Konfirmasi dalam 1×24 jam kerja
    </p>
  </div>
</section>

<!-- ── FOOTER ──────────────────────────────── -->
<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <h3>Koperasi Sari Sedana</h3>
      <span class="footer-tag">Usaha Bersama, Untung Bersama</span>
      <p>Koperasi Konsumen Sari Sedana adalah koperasi resmi yang berdedikasi untuk meningkatkan kesejahteraan anggota melalui program usaha bersama yang nyata, transparan, dan berkeadilan.</p>
    </div>
    <div class="footer-col">
      <h4>Program</h4>
      <ul>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#keuntungan">Kenapa Laundry</a></li>
        <li><a href="#simulasi">Simulasi Bagi Hasil</a></li>
        <li><a href="#daftar">Daftar Sekarang</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Informasi</h4>
      <ul>
        <li><a href="#faq">FAQ</a></li>
        <li><a href="#">Syarat &amp; Ketentuan</a></li>
        <li><a href="#">Kebijakan Privasi</a></li>
        <li><a href="#">Hubungi Kami</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-btm">
    <p>© 2024 Koperasi Konsumen Sari Sedana. Terdaftar dan berlisensi resmi.</p>
    <p>Investasi memiliki risiko. Bagi hasil tidak dijamin. Pastikan kamu memahami mekanisme investasi sebelum berpartisipasi.</p>
  </div>
</footer>

<script>
  // Navbar scroll shadow
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });

  // Hamburger
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  hamburger.addEventListener('click', () => mobileNav.classList.toggle('open'));
  mobileNav.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => mobileNav.classList.remove('open'));
  });

  // Scroll fade-in
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('vis'); });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.fi').forEach(el => io.observe(el));

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - 80, behavior: 'smooth' });
      }
    });
  });

  // Calculator
  const modalInput  = document.getElementById('modalInput');
  const modalSlider = document.getElementById('modalSlider');
  const resKuartal  = document.getElementById('resKuartal');
  const resTahun    = document.getElementById('resTahun');
  const resTotal    = document.getElementById('resTotal');

  const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

  function recalc(val) {
    const m = Math.max(1500000, Number(val) || 1500000);
    resKuartal.textContent = fmt(m * 0.10);
    resTahun.textContent   = fmt(m * 0.40);
    resTotal.textContent   = fmt(m * 1.40);
  }

  modalInput.addEventListener('input', e => {
    const v = Number(e.target.value);
    if (v >= 1500000) modalSlider.value = Math.min(v, 20000000);
    recalc(e.target.value);
  });

  modalSlider.addEventListener('input', e => {
    modalInput.value = e.target.value;
    recalc(e.target.value);
  });

  recalc(1500000);

  // FAQ accordion
  document.querySelectorAll('.faq-q').forEach(q => {
    q.addEventListener('click', () => {
      const item   = q.parentElement;
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });
</script>
</body>
</html>
