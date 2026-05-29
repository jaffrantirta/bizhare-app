<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="mybisnis — Investasi nyata pada bisnis terverifikasi, bagi hasil transparan, di bawah naungan Koperasi Sari Sedana." />
  <title>mybisnis — Investasi Nyata, Bisnis Bisa Dilihat</title>

  <link rel="icon" type="image/jpeg" href="/logo.jpg" />
  <link rel="apple-touch-icon" href="/logo.jpg" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    /* ─── Variables ─────────────────────────────────────────────────────────── */
    :root {
      --green-deep:   #1B4332;
      --green-mid:    #2D6A4F;
      --green-light:  #52B788;
      --green-pale:   #D8F3DC;
      --gold:         #D4A017;
      --gold-light:   #F0C040;
      --gold-pale:    #FDF3D0;
      --bg:           #FAF7F2;
      --bg-alt:       #F3EDE4;
      --text:         #1A1A1A;
      --text-muted:   #5A5A5A;
      --white:        #FFFFFF;
      --border:       #DDD5C8;
      --radius-sm:    8px;
      --radius-md:    14px;
      --radius-lg:    24px;
      --shadow-sm:    0 2px 8px rgba(0,0,0,.07);
      --shadow-md:    0 6px 24px rgba(0,0,0,.10);
      --shadow-lg:    0 16px 48px rgba(0,0,0,.13);
      --font-head:    'Playfair Display', Georgia, serif;
      --font-body:    'Source Sans 3', system-ui, sans-serif;
      --transition:   .3s ease;
    }

    /* ─── Reset & Base ──────────────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; font-size: 16px; }
    body {
      font-family: var(--font-body);
      background: var(--bg);
      color: var(--text);
      line-height: 1.7;
      overflow-x: hidden;
    }
    img { display: block; max-width: 100%; }
    a   { color: inherit; text-decoration: none; }
    ul  { list-style: none; }

    /* ─── Typography helpers ─────────────────────────────────────────────────── */
    .section-label {
      display: inline-block;
      font-family: var(--font-body);
      font-size: .8rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: .75rem;
    }
    h1, h2, h3, h4 { font-family: var(--font-head); line-height: 1.25; }
    .section-title {
      font-size: clamp(1.75rem, 3.5vw, 2.5rem);
      color: var(--green-deep);
      margin-bottom: 1rem;
    }
    .section-sub {
      font-size: 1.05rem;
      color: var(--text-muted);
      max-width: 56ch;
    }

    /* ─── Layout ─────────────────────────────────────────────────────────────── */
    .container {
      width: 100%;
      max-width: 1160px;
      margin: 0 auto;
      padding: 0 1.25rem;
    }
    section { padding: 5rem 0; }

    /* ─── Buttons ────────────────────────────────────────────────────────────── */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .8rem 1.75rem;
      border-radius: 50px;
      font-family: var(--font-body);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      border: 2px solid transparent;
      transition: var(--transition);
      white-space: nowrap;
    }
    .btn-primary { background: var(--gold); color: var(--green-deep); }
    .btn-primary:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .btn-ghost { background: transparent; border-color: var(--white); color: var(--white); }
    .btn-ghost:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }
    .btn-ghost-dark { background: transparent; border-color: var(--green-deep); color: var(--green-deep); }
    .btn-ghost-dark:hover { background: var(--green-pale); transform: translateY(-2px); }

    /* ─── Scroll animations ──────────────────────────────────────────────────── */
    .fade-in {
      opacity: 0;
      transform: translateY(28px);
      transition: opacity .65s ease, transform .65s ease;
    }
    .fade-in.visible { opacity: 1; transform: none; }

    .stagger > * {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .55s ease, transform .55s ease;
    }
    .stagger.visible > *:nth-child(1) { opacity:1; transform:none; transition-delay:.05s; }
    .stagger.visible > *:nth-child(2) { opacity:1; transform:none; transition-delay:.15s; }
    .stagger.visible > *:nth-child(3) { opacity:1; transform:none; transition-delay:.25s; }
    .stagger.visible > *:nth-child(4) { opacity:1; transform:none; transition-delay:.35s; }
    .stagger.visible > *:nth-child(5) { opacity:1; transform:none; transition-delay:.45s; }
    .stagger.visible > *:nth-child(6) { opacity:1; transform:none; transition-delay:.55s; }

    /* ═══════════════════════════════════════════════════════════════════════════
       NAVBAR
    ═══════════════════════════════════════════════════════════════════════════ */
    #navbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 900;
      background: transparent;
      transition: background var(--transition), box-shadow var(--transition);
    }
    #navbar.scrolled { background: var(--green-deep); box-shadow: var(--shadow-md); }

    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }
    .nav-logo { display: flex; align-items: center; gap: .75rem; }
    .nav-logo-mark {
      width: 42px; height: 42px;
      border-radius: 10px;
      overflow: hidden;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .nav-logo-mark img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .nav-logo-text { color: var(--white); }
    .nav-logo-text strong { display: block; font-family: var(--font-head); font-size: 1rem; line-height: 1.1; }
    .nav-logo-text span   { display: block; font-size: .72rem; opacity: .75; }

    .nav-links { display: flex; align-items: center; gap: 2rem; }
    .nav-links a {
      color: rgba(255,255,255,.85);
      font-size: .95rem;
      font-weight: 500;
      transition: color var(--transition);
    }
    .nav-links a:hover { color: var(--gold); }
    .nav-cta { margin-left: 1rem; }

    .nav-toggle {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      cursor: pointer;
      padding: .25rem;
    }
    .nav-toggle span {
      display: block;
      width: 24px; height: 2px;
      background: var(--white);
      border-radius: 2px;
      transition: var(--transition);
    }
    .nav-mobile {
      display: none;
      flex-direction: column;
      background: var(--green-deep);
      padding: 1rem 1.25rem 1.5rem;
    }
    .nav-mobile.open { display: flex; }
    .nav-mobile a {
      color: rgba(255,255,255,.9);
      padding: .65rem 0;
      border-bottom: 1px solid rgba(255,255,255,.1);
      font-size: 1rem;
    }
    .nav-mobile .btn { margin-top: 1rem; align-self: flex-start; }

    @media (max-width: 780px) {
      .nav-links, .nav-cta { display: none; }
      .nav-toggle { display: flex; }
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       HERO
    ═══════════════════════════════════════════════════════════════════════════ */
    #hero {
      min-height: 100svh;
      background:
        linear-gradient(160deg, rgba(27,67,50,.93) 0%, rgba(27,67,50,.80) 60%, rgba(45,106,79,.70) 100%),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600'%3E%3Crect fill='%231B4332' width='800' height='600'/%3E%3Ccircle fill='%232D6A4F' cx='200' cy='150' r='220'/%3E%3Ccircle fill='%2352B788' cx='600' cy='420' r='160' opacity='.35'/%3E%3C/svg%3E")
        center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding-top: 68px;
    }
    .hero-content { padding: 5rem 0 4rem; max-width: 700px; }
    .hero-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      background: rgba(212,160,23,.18);
      border: 1px solid rgba(212,160,23,.4);
      color: var(--gold-light);
      font-size: .82rem;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: .35rem .9rem;
      border-radius: 50px;
      margin-bottom: 1.5rem;
    }
    #hero h1 {
      font-size: clamp(2rem, 5vw, 3.4rem);
      color: var(--white);
      margin-bottom: 1.25rem;
      line-height: 1.2;
    }
    #hero h1 em { color: var(--gold); font-style: normal; }
    .hero-sub {
      font-size: 1.1rem;
      color: rgba(255,255,255,.82);
      max-width: 55ch;
      margin-bottom: 2.25rem;
    }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 3rem; }
    .hero-badges  { display: flex; flex-wrap: wrap; gap: 1rem; }
    .badge {
      display: flex;
      align-items: center;
      gap: .45rem;
      background: rgba(255,255,255,.1);
      border: 1px solid rgba(255,255,255,.2);
      color: rgba(255,255,255,.9);
      font-size: .85rem;
      padding: .4rem .9rem;
      border-radius: 50px;
      backdrop-filter: blur(6px);
    }
    .badge-icon {
      width: 18px; height: 18px;
      background: var(--gold);
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: .65rem;
      color: var(--green-deep);
      font-weight: 700;
      flex-shrink: 0;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       HOW IT WORKS
    ═══════════════════════════════════════════════════════════════════════════ */
    #cara-kerja { background: var(--white); }
    .how-header { text-align: center; margin-bottom: 3.5rem; }
    .how-header .section-sub { margin: 0 auto; }

    .steps {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
      position: relative;
    }
    .steps::before {
      content: '';
      position: absolute;
      top: 36px;
      left: calc(12.5% + 28px);
      right: calc(12.5% + 28px);
      height: 2px;
      background: linear-gradient(90deg, var(--green-light), var(--gold));
      z-index: 0;
    }
    .step { text-align: center; position: relative; z-index: 1; }
    .step-num {
      width: 72px; height: 72px;
      border-radius: 50%;
      background: var(--green-deep);
      color: var(--white);
      font-family: var(--font-head);
      font-size: 1.6rem;
      font-weight: 700;
      display: grid;
      place-items: center;
      margin: 0 auto 1.25rem;
      box-shadow: 0 0 0 6px var(--green-pale);
      position: relative;
    }
    .step-num::after {
      content: attr(data-icon);
      position: absolute;
      bottom: -4px; right: -4px;
      width: 24px; height: 24px;
      background: var(--gold);
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: .9rem;
    }
    .step h3 { font-size: 1.05rem; color: var(--green-deep); margin-bottom: .5rem; }
    .step p  { font-size: .92rem; color: var(--text-muted); }

    @media (max-width: 780px) { .steps { grid-template-columns: 1fr 1fr; } .steps::before { display: none; } }
    @media (max-width: 480px) { .steps { grid-template-columns: 1fr; } }

    /* ═══════════════════════════════════════════════════════════════════════════
       WHY CHOOSE US
    ═══════════════════════════════════════════════════════════════════════════ */
    #keunggulan { background: var(--bg-alt); }
    .features-header { text-align: center; margin-bottom: 3rem; }
    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }
    .feature-card {
      background: var(--white);
      border-radius: var(--radius-md);
      padding: 2rem 1.75rem;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
      transition: transform var(--transition), box-shadow var(--transition);
    }
    .feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .feature-icon {
      width: 52px; height: 52px;
      border-radius: var(--radius-sm);
      background: var(--green-pale);
      display: grid;
      place-items: center;
      font-size: 1.5rem;
      margin-bottom: 1.1rem;
    }
    .feature-card h3 { font-size: 1.05rem; color: var(--green-deep); margin-bottom: .5rem; }
    .feature-card p  { font-size: .92rem; color: var(--text-muted); }

    @media (max-width: 780px) { .features-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px) { .features-grid { grid-template-columns: 1fr; } }

    /* ═══════════════════════════════════════════════════════════════════════════
       INVESTMENT SIMULATOR
    ═══════════════════════════════════════════════════════════════════════════ */
    #simulasi { background: var(--green-deep); }
    .sim-header { text-align: center; margin-bottom: 3rem; }
    .sim-header .section-label { color: var(--gold); }
    .sim-header .section-title { color: var(--white); }
    .sim-header .section-sub   { color: rgba(255,255,255,.7); margin: 0 auto; }

    .sim-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2.5rem 3rem;
      max-width: 760px;
      margin: 0 auto;
      box-shadow: var(--shadow-lg);
    }
    .sim-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 2rem;
    }
    .sim-field label {
      display: block;
      font-weight: 600;
      font-size: .9rem;
      color: var(--green-deep);
      margin-bottom: .6rem;
    }
    .sim-field input[type=range] {
      width: 100%;
      accent-color: var(--green-deep);
      cursor: pointer;
      height: 6px;
    }
    .sim-field select {
      width: 100%;
      padding: .7rem 1rem;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      font-family: var(--font-body);
      font-size: .95rem;
      background: var(--bg);
      color: var(--text);
      outline: none;
      cursor: pointer;
      transition: border-color var(--transition);
    }
    .sim-field select:focus { border-color: var(--green-mid); }
    .sim-amount-display {
      font-family: var(--font-head);
      font-size: 1.5rem;
      color: var(--green-deep);
      font-weight: 700;
      margin-top: .35rem;
    }
    .sim-result {
      background: var(--green-pale);
      border-radius: var(--radius-md);
      padding: 1.5rem 2rem;
      border-left: 4px solid var(--green-light);
      margin-bottom: 1rem;
    }
    .sim-result-label {
      font-size: .85rem;
      font-weight: 600;
      color: var(--green-mid);
      margin-bottom: .35rem;
      text-transform: uppercase;
      letter-spacing: .07em;
    }
    .sim-result-value {
      font-family: var(--font-head);
      font-size: 2rem;
      font-weight: 700;
      color: var(--green-deep);
    }
    .sim-result-sub { font-size: .88rem; color: var(--text-muted); margin-top: .2rem; }
    .sim-disclaimer { font-size: .8rem; color: var(--text-muted); text-align: center; font-style: italic; }

    @media (max-width: 600px) {
      .sim-card { padding: 1.75rem 1.25rem; }
      .sim-row  { grid-template-columns: 1fr; gap: 1.25rem; }
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       REFERRAL
    ═══════════════════════════════════════════════════════════════════════════ */
    #referral { background: var(--bg); }
    .referral-inner {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      align-items: center;
    }
    .referral-text .section-sub { margin-bottom: 2rem; }

    .referral-levels { display: flex; flex-direction: column; gap: .6rem; margin-bottom: 2rem; }
    .ref-level { display: flex; align-items: center; gap: .9rem; }
    .ref-level-bar {
      flex: 1;
      height: 10px;
      border-radius: 5px;
      background: var(--green-pale);
      position: relative;
      overflow: hidden;
    }
    .ref-level-bar::after {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      border-radius: 5px;
      transition: width .8s ease;
    }
    .ref-level[data-lv="1"] .ref-level-bar::after { width:100%; background: var(--green-deep); }
    .ref-level[data-lv="2"] .ref-level-bar::after { width:78%;  background: var(--green-mid); }
    .ref-level[data-lv="3"] .ref-level-bar::after { width:58%;  background: var(--green-light); }
    .ref-level[data-lv="4"] .ref-level-bar::after { width:40%;  background: #95D5B2; }
    .ref-level[data-lv="5"] .ref-level-bar::after { width:24%;  background: #B7E4C7; }

    .ref-level-label { font-size: .85rem; font-weight: 600; color: var(--green-deep); min-width: 56px; }
    .ref-level-desc  { font-size: .82rem; color: var(--text-muted); min-width: 90px; }

    /* tree */
    .referral-tree { display: flex; flex-direction: column; align-items: center; gap: .5rem; }
    .tree-row { display: flex; gap: .4rem; justify-content: center; }
    .tree-node {
      width: 36px; height: 36px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: .7rem;
      font-weight: 700;
      color: var(--white);
      flex-shrink: 0;
    }
    .tree-node.you  { background: var(--gold); color: var(--green-deep); width:48px; height:48px; font-size:.8rem; }
    .tree-node.lv1  { background: var(--green-deep); }
    .tree-node.lv2  { background: var(--green-mid);   width:30px; height:30px; font-size:.65rem; }
    .tree-node.lv3  { background: var(--green-light); width:26px; height:26px; font-size:.6rem; }
    .tree-node.lv4  { background: #95D5B2; color:var(--green-deep); width:22px; height:22px; font-size:.55rem; }
    .tree-node.lv5  { background: #B7E4C7; color:var(--green-deep); width:18px; height:18px; font-size:.5rem; }
    .tree-connector { width:2px; height:14px; background: var(--border); margin: 0 auto; }

    .tree-card {
      background: var(--white);
      border-radius: var(--radius-md);
      padding: 2rem;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
    }
    .tree-label { text-align:center; font-size:.78rem; color:var(--text-muted); margin-top:.5rem; }

    @media (max-width: 780px) { .referral-inner { grid-template-columns: 1fr; gap: 2.5rem; } }

    /* ═══════════════════════════════════════════════════════════════════════════
       TRUST & LEGAL
    ═══════════════════════════════════════════════════════════════════════════ */
    #tentang { background: var(--bg-alt); }
    .trust-inner { text-align: center; }
    .koperasi-badge {
      display: inline-flex;
      align-items: center;
      gap: 1rem;
      background: var(--white);
      border: 2px solid var(--green-pale);
      border-radius: var(--radius-md);
      padding: 1.25rem 2rem;
      margin: 2rem auto;
    }
    .koperasi-logo {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: var(--green-deep);
      display: grid;
      place-items: center;
      font-family: var(--font-head);
      font-size: 1rem;
      font-weight: 700;
      color: var(--gold);
      flex-shrink: 0;
    }
    .koperasi-info strong { display:block; font-family:var(--font-head); font-size:1.1rem; color:var(--green-deep); }
    .koperasi-info span   { font-size:.85rem; color:var(--text-muted); }
    .trust-pillars {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      margin-top: 2.5rem;
    }
    .trust-pill {
      display: flex;
      align-items: center;
      gap: .6rem;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 50px;
      padding: .6rem 1.25rem;
      font-size: .9rem;
      font-weight: 500;
      color: var(--green-deep);
      box-shadow: var(--shadow-sm);
    }
    .trust-pill .icon { font-size: 1.1rem; }

    /* ═══════════════════════════════════════════════════════════════════════════
       FAQ
    ═══════════════════════════════════════════════════════════════════════════ */
    #faq { background: var(--white); }
    .faq-header { text-align: center; margin-bottom: 3rem; }
    .faq-list   { max-width: 760px; margin: 0 auto; }
    .faq-item   { border-bottom: 1px solid var(--border); }
    .faq-q {
      width: 100%;
      background: none;
      border: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.3rem 0;
      font-family: var(--font-body);
      font-size: 1rem;
      font-weight: 600;
      color: var(--green-deep);
      cursor: pointer;
      text-align: left;
      gap: 1rem;
    }
    .faq-icon {
      width: 28px; height: 28px;
      border-radius: 50%;
      background: var(--green-pale);
      display: grid;
      place-items: center;
      flex-shrink: 0;
      color: var(--green-deep);
      font-size: 1.1rem;
      font-weight: 700;
      transition: var(--transition);
    }
    .faq-item.open .faq-icon { background: var(--green-deep); color: var(--white); transform: rotate(45deg); }
    .faq-a {
      overflow: hidden;
      max-height: 0;
      transition: max-height .4s ease, padding .3s ease;
    }
    .faq-item.open .faq-a { max-height: 300px; }
    .faq-a p {
      padding-bottom: 1.25rem;
      color: var(--text-muted);
      font-size: .97rem;
      line-height: 1.75;
    }

    /* ═══════════════════════════════════════════════════════════════════════════
       CTA BANNER
    ═══════════════════════════════════════════════════════════════════════════ */
    #cta-banner {
      background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%);
      padding: 5rem 0;
      text-align: center;
    }
    #cta-banner h2 {
      font-size: clamp(1.75rem, 4vw, 2.75rem);
      color: var(--white);
      margin-bottom: 1rem;
    }
    #cta-banner h2 em { color: var(--gold); font-style: normal; }
    .cta-sub   { font-size:1rem; color:rgba(255,255,255,.75); max-width:50ch; margin:0 auto 2.5rem; }
    .cta-legal { margin-top:1.5rem; font-size:.78rem; color:rgba(255,255,255,.5); }

    /* ═══════════════════════════════════════════════════════════════════════════
       FOOTER
    ═══════════════════════════════════════════════════════════════════════════ */
    footer { background: #0D2B1E; color: rgba(255,255,255,.7); padding: 3rem 0 2rem; }
    .footer-inner {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: flex-start;
      gap: 2rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid rgba(255,255,255,.1);
      margin-bottom: 1.5rem;
    }
    .footer-brand .tagline { font-size:.85rem; color:rgba(255,255,255,.5); margin-top:.35rem; max-width:28ch; }
    .footer-links { display:flex; flex-direction:column; gap:.6rem; }
    .footer-links a { color:rgba(255,255,255,.65); font-size:.9rem; transition:color var(--transition); }
    .footer-links a:hover { color: var(--gold); }
    .footer-bottom { font-size:.8rem; color:rgba(255,255,255,.4); text-align:center; }
  </style>
</head>
<body>

  <!-- ── NAVBAR ──────────────────────────────────────────────────── -->
  <nav id="navbar" aria-label="Navigasi utama">
    <div class="container">
      <div class="nav-inner">
        <a href="#" class="nav-logo" aria-label="mybisnis beranda">
          <div class="nav-logo-mark"><img src="/logo.jpg" alt="mybisnis logo" /></div>
          <div class="nav-logo-text">
            <strong>mybisnis</strong>
            <span>Investasi Nyata, Hasil Pasti</span>
          </div>
        </a>
        <ul class="nav-links" role="list">
          <li><a href="#cara-kerja">Cara Kerja</a></li>
          <li><a href="#keunggulan">Keunggulan</a></li>
          <li><a href="#simulasi">Simulasi</a></li>
          <li><a href="#tentang">Tentang Kami</a></li>
        </ul>
        <a href="#cta-banner" class="btn btn-primary nav-cta">Mulai Investasi</a>
        <button class="nav-toggle" id="navToggle" aria-label="Buka menu" aria-expanded="false" aria-controls="navMobile">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
    <div class="nav-mobile" id="navMobile" role="menu">
      <a href="#cara-kerja" role="menuitem">Cara Kerja</a>
      <a href="#keunggulan" role="menuitem">Keunggulan</a>
      <a href="#simulasi"   role="menuitem">Simulasi</a>
      <a href="#tentang"    role="menuitem">Tentang Kami</a>
      <a href="#cta-banner" class="btn btn-primary">Mulai Investasi</a>
    </div>
  </nav>

  <!-- ── HERO ────────────────────────────────────────────────────── -->
  <section id="hero" aria-label="Hero">
    <div class="container">
      <div class="hero-content fade-in">
        <div class="hero-eyebrow">🌱 Koperasi Sari Sedana &mdash; Berizin Resmi</div>
        <h1>Investasi Nyata.<br />Bisnis Bisa <em>Dilihat.</em><br />Untung Dibagi Bersama.</h1>
        <p class="hero-sub">
          Danai bisnis lokal yang terverifikasi secara fisik dan nikmati bagi hasil yang transparan setiap periode.
          Mulai dari Rp&nbsp;375.000, cicilan tersedia, tanpa perlu keahlian finansial.
        </p>
        <div class="hero-actions">
          <a href="#cta-banner" class="btn btn-primary">✦ Mulai Sekarang</a>
          <a href="#cara-kerja" class="btn btn-ghost">Pelajari Cara Kerja →</a>
        </div>
        <div class="hero-badges" role="list">
          <div class="badge" role="listitem"><div class="badge-icon" aria-hidden="true">✓</div>Terdaftar Resmi Koperasi</div>
          <div class="badge" role="listitem"><div class="badge-icon" aria-hidden="true">✓</div>Bisnis Terverifikasi Fisik</div>
          <div class="badge" role="listitem"><div class="badge-icon" aria-hidden="true">✓</div>Bagi Hasil Transparan</div>
          <div class="badge" role="listitem"><div class="badge-icon" aria-hidden="true">✓</div>Mulai dari Rp375.000</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── HOW IT WORKS ────────────────────────────────────────────── -->
  <section id="cara-kerja" aria-labelledby="how-title">
    <div class="container">
      <div class="how-header fade-in">
        <span class="section-label">Cara Kerja</span>
        <h2 class="section-title" id="how-title">Investasi Semudah 4 Langkah</h2>
        <p class="section-sub">Tidak perlu latar belakang keuangan. Proses kami sederhana, transparan, dan bisa dilakukan dari genggaman tangan.</p>
      </div>
      <div class="steps stagger">
        <div class="step">
          <div class="step-num" data-icon="👤">1</div>
          <h3>Daftar &amp; Verifikasi Identitas</h3>
          <p>Buat akun, lengkapi data diri, dan unggah KTP atau SIM untuk proses KYC yang cepat.</p>
        </div>
        <div class="step">
          <div class="step-num" data-icon="🏪">2</div>
          <h3>Pilih Bisnis yang Ingin Didanai</h3>
          <p>Telusuri daftar bisnis lokal terverifikasi. Kunjungi langsung jika ingin lebih yakin.</p>
        </div>
        <div class="step">
          <div class="step-num" data-icon="💳">3</div>
          <h3>Lakukan Pembayaran</h3>
          <p>Bayar via transfer bank atau bayar via GoPay. Bisa cicil 1&ndash;12 bulan sesuai kemampuan.</p>
        </div>
        <div class="step">
          <div class="step-num" data-icon="💰">4</div>
          <h3>Terima Bagi Hasil Setiap Periode</h3>
          <p>Bagi hasil masuk otomatis ke saldo akun setiap periode. Tarik kapan saja.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ── WHY CHOOSE US ───────────────────────────────────────────── -->
  <section id="keunggulan" aria-labelledby="features-title">
    <div class="container">
      <div class="features-header fade-in">
        <span class="section-label">Keunggulan</span>
        <h2 class="section-title" id="features-title">Kenapa Pilih mybisnis?</h2>
        <p class="section-sub">Kami berbeda karena bisnis yang Anda danai nyata, bisa dikunjungi, dan dikelola oleh komunitas lokal.</p>
      </div>
      <div class="features-grid stagger">
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">🏭</div>
          <h3>Bisnis Nyata, Bisa Dicek Langsung</h3>
          <p>Bukan investasi virtual. Setiap bisnis diverifikasi fisik oleh tim kami sebelum dibuka untuk pendanaan.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">📈</div>
          <h3>Bagi Hasil Rutin</h3>
          <p>Terima porsi keuntungan bisnis secara bulanan, triwulan, atau per siklus usaha langsung ke saldo Anda.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">🛡️</div>
          <h3>Aman &amp; Legal</h3>
          <p>Beroperasi di bawah naungan <strong>Koperasi Sari Sedana</strong> dengan izin resmi dan pengawasan regulasi.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">📅</div>
          <h3>Cicilan Fleksibel</h3>
          <p>Investasi bisa dicicil 1 hingga 12 bulan. Mulai kecil, kembangkan sesuai kemampuan finansial Anda.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">🤝</div>
          <h3>Bonus Referral 5 Level</h3>
          <p>Ajak teman berinvestasi dan dapatkan reward otomatis hingga 5 level jaringan referral Anda.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon" aria-hidden="true">📱</div>
          <h3>Pantau via Aplikasi</h3>
          <p>Lihat portofolio, riwayat bagi hasil, jadwal cicilan, dan notifikasi real-time kapan saja.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ── INVESTMENT SIMULATOR ────────────────────────────────────── -->
  <section id="simulasi" aria-labelledby="sim-title">
    <div class="container">
      <div class="sim-header fade-in">
        <span class="section-label">Simulasi Keuntungan</span>
        <h2 class="section-title" id="sim-title">Hitung Estimasi Bagi Hasil Anda</h2>
        <p class="section-sub">Geser slider untuk melihat potensi keuntungan berdasarkan jumlah investasi dan periode bagi hasil.</p>
      </div>
      <div class="sim-card fade-in">
        <div class="sim-row">
          <div class="sim-field">
            <label for="simAmount">Jumlah Investasi</label>
            <div class="sim-amount-display" id="simAmountDisplay">Rp 1.500.000</div>
            <input type="range" id="simAmount" min="375000" max="15000000" step="375000" value="1500000" aria-label="Jumlah investasi" />
            <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-muted);margin-top:.35rem;">
              <span>Rp375.000</span><span>Rp15.000.000</span>
            </div>
          </div>
          <div class="sim-field">
            <label for="simPeriod">Periode Bagi Hasil</label>
            <select id="simPeriod" aria-label="Periode bagi hasil">
              <option value="0.06">Bulanan (est. 6% / bulan)</option>
              <option value="0.07" selected>Per 3 Bulan (est. 7% / periode)</option>
              <option value="0.09">Per 6 Bulan (est. 9% / periode)</option>
            </select>
          </div>
        </div>
        <div class="sim-result" aria-live="polite">
          <div class="sim-result-label">Estimasi Bagi Hasil per Periode</div>
          <div class="sim-result-value" id="simResultValue">Rp 90.000 – Rp 120.000</div>
          <div class="sim-result-sub" id="simResultSub">dari investasi <strong>Rp 1.500.000</strong> per <strong>3 bulan</strong></div>
        </div>
        <p class="sim-disclaimer">* Ilustrasi ini bukan jaminan keuntungan. Bagi hasil aktual bergantung pada kinerja bisnis yang didanai.</p>
      </div>
    </div>
  </section>

  <!-- ── REFERRAL ─────────────────────────────────────────────────── -->
  <section id="referral" aria-labelledby="ref-title">
    <div class="container">
      <div class="referral-inner">
        <div class="referral-text fade-in">
          <span class="section-label">Program Referral</span>
          <h2 class="section-title" id="ref-title">Ajak Teman, Sama-Sama Untung</h2>
          <p class="section-sub">
            Bagikan kode referral Anda dan dapatkan reward otomatis setiap kali orang dalam jaringan Anda menyelesaikan investasi pertama — hingga 5 level ke bawah.
          </p>
          <div class="referral-levels" aria-label="Tingkat reward referral">
            <div class="ref-level" data-lv="1"><span class="ref-level-label">Level 1</span><div class="ref-level-bar"></div><span class="ref-level-desc">Reward penuh</span></div>
            <div class="ref-level" data-lv="2"><span class="ref-level-label">Level 2</span><div class="ref-level-bar"></div><span class="ref-level-desc">Reward besar</span></div>
            <div class="ref-level" data-lv="3"><span class="ref-level-label">Level 3</span><div class="ref-level-bar"></div><span class="ref-level-desc">Reward sedang</span></div>
            <div class="ref-level" data-lv="4"><span class="ref-level-label">Level 4</span><div class="ref-level-bar"></div><span class="ref-level-desc">Reward bonus</span></div>
            <div class="ref-level" data-lv="5"><span class="ref-level-label">Level 5</span><div class="ref-level-bar"></div><span class="ref-level-desc">Reward tambahan</span></div>
          </div>
          <a href="#cta-banner" class="btn btn-ghost-dark">Dapatkan Kode Referral Anda →</a>
        </div>
        <div class="fade-in">
          <div class="tree-card" aria-label="Visualisasi pohon referral">
            <div class="referral-tree">
              <div class="tree-row"><div class="tree-node you">ANDA</div></div>
              <div class="tree-connector" aria-hidden="true"></div>
              <div class="tree-row">
                <div class="tree-node lv1">L1</div>
                <div class="tree-node lv1">L1</div>
                <div class="tree-node lv1">L1</div>
              </div>
              <div class="tree-connector" aria-hidden="true"></div>
              <div class="tree-row">
                <div class="tree-node lv2">L2</div><div class="tree-node lv2">L2</div>
                <div class="tree-node lv2">L2</div><div class="tree-node lv2">L2</div>
                <div class="tree-node lv2">L2</div>
              </div>
              <div class="tree-connector" aria-hidden="true"></div>
              <div class="tree-row">
                <div class="tree-node lv3">L3</div><div class="tree-node lv3">L3</div>
                <div class="tree-node lv3">L3</div><div class="tree-node lv3">L3</div>
                <div class="tree-node lv3">L3</div><div class="tree-node lv3">L3</div>
              </div>
              <div class="tree-connector" aria-hidden="true"></div>
              <div class="tree-row">
                <div class="tree-node lv4">L4</div><div class="tree-node lv4">L4</div>
                <div class="tree-node lv4">L4</div><div class="tree-node lv4">L4</div>
                <div class="tree-node lv4">L4</div><div class="tree-node lv4">L4</div>
                <div class="tree-node lv4">L4</div>
              </div>
              <div class="tree-connector" aria-hidden="true"></div>
              <div class="tree-row">
                <div class="tree-node lv5">L5</div><div class="tree-node lv5">L5</div>
                <div class="tree-node lv5">L5</div><div class="tree-node lv5">L5</div>
                <div class="tree-node lv5">L5</div><div class="tree-node lv5">L5</div>
                <div class="tree-node lv5">L5</div><div class="tree-node lv5">L5</div>
              </div>
            </div>
            <p class="tree-label">Reward otomatis dari 5 level jaringan referral Anda</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── TRUST & LEGAL ────────────────────────────────────────────── -->
  <section id="tentang" aria-labelledby="trust-title">
    <div class="container">
      <div class="trust-inner fade-in">
        <span class="section-label">Legalitas &amp; Kepercayaan</span>
        <h2 class="section-title" id="trust-title">Investasi Anda Terlindungi secara Hukum</h2>
        <p class="section-sub" style="margin:0 auto 1rem;">Kami beroperasi secara transparan dan sah di bawah pengawasan lembaga resmi negara.</p>
        <div class="koperasi-badge" role="region" aria-label="Informasi Koperasi Sari Sedana">
          <div class="koperasi-logo" aria-hidden="true">KSS</div>
          <div class="koperasi-info">
            <strong>Koperasi Sari Sedana</strong>
            <span>Terdaftar dan berizin resmi &bull; Di bawah pengawasan Kemenkop</span>
          </div>
        </div>
        <div class="trust-pillars" role="list">
          <div class="trust-pill" role="listitem"><span class="icon" aria-hidden="true">🔒</span> KYC Wajib untuk Semua Investor</div>
          <div class="trust-pill" role="listitem"><span class="icon" aria-hidden="true">📜</span> Legalitas Terjamin oleh Koperasi</div>
          <div class="trust-pill" role="listitem"><span class="icon" aria-hidden="true">📊</span> Laporan Keuangan Transparan</div>
          <div class="trust-pill" role="listitem"><span class="icon" aria-hidden="true">🏢</span> Bisnis Terverifikasi Fisik</div>
          <div class="trust-pill" role="listitem"><span class="icon" aria-hidden="true">💳</span> Pembayaran via Bank &amp; GoPay</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── FAQ ──────────────────────────────────────────────────────── -->
  <section id="faq" aria-labelledby="faq-title">
    <div class="container">
      <div class="faq-header fade-in">
        <span class="section-label">FAQ</span>
        <h2 class="section-title" id="faq-title">Pertanyaan yang Sering Diajukan</h2>
      </div>
      <div class="faq-list fade-in">
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Apakah investasi di mybisnis aman?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Ya. mybisnis beroperasi di bawah naungan Koperasi Sari Sedana yang terdaftar dan berizin resmi. Setiap investor wajib melalui proses KYC sebelum bisa berinvestasi. Semua bisnis telah melalui verifikasi fisik oleh tim kami sebelum dibuka untuk pendanaan.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Bagaimana cara melihat bisnis yang sedang didanai?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Setiap bisnis memiliki profil lengkap termasuk lokasi dan informasi usaha. Karena ini bisnis fisik lokal, Anda bahkan bisa mengunjungi langsung untuk memastikan keberadaannya sebelum memutuskan berinvestasi.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Kapan saya menerima bagi hasil?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Jadwal bagi hasil bergantung pada periode yang ditetapkan untuk masing-masing bisnis — bisa bulanan, per 3 bulan, atau per siklus usaha. Bagi hasil langsung masuk ke saldo aplikasi dan bisa ditarik kapan saja setelah dikonfirmasi admin.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Apakah investasi bisa dicicil?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Ya! Anda bisa memilih metode cicilan 1 hingga 12 bulan. Investasi menjadi aktif setelah pembayaran pertama dikonfirmasi, dan cicilan berikutnya mengikuti jadwal yang ditetapkan.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Bagaimana cara menarik saldo saya?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Buka menu Penarikan di aplikasi, masukkan jumlah yang ingin ditarik (minimum Rp 100.000), pilih rekening bank tujuan, dan ajukan permintaan. Tim kami memproses penarikan dalam 1–3 hari kerja.</p></div>
        </div>
        <div class="faq-item">
          <button class="faq-q" aria-expanded="false">
            Apa itu sistem referral dan bagaimana cara kerjanya?
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-a"><p>Setiap anggota mendapat kode referral unik. Ketika seseorang mendaftar menggunakan kode Anda dan menyelesaikan setoran awal, Anda otomatis mendapat reward. Sistem mendukung hingga 5 level — reward juga datang dari orang yang diajak teman Anda, dan seterusnya.</p></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── CTA BANNER ───────────────────────────────────────────────── -->
  <section id="cta-banner" aria-labelledby="cta-title">
    <div class="container fade-in" style="text-align:center;">
      <span class="section-label" style="color:var(--gold);">Bergabung Sekarang</span>
      <h2 id="cta-title">Siap Mulai Investasi <em>Hari Ini?</em></h2>
      <p class="cta-sub">Ribuan investor lokal sudah mempercayakan dananya kepada mybisnis. Giliran Anda membuat uang bekerja untuk masa depan.</p>
      <a href="#" class="btn btn-primary" style="font-size:1.1rem;padding:1rem 2.25rem;">✦ Daftar Sekarang — Gratis</a>
      <p class="cta-legal">Investasi mengandung risiko. Bagi hasil sesuai kinerja bisnis. Tidak ada jaminan keuntungan tetap. Terdaftar di bawah Koperasi Sari Sedana.</p>
    </div>
  </section>

  <!-- ── FOOTER ────────────────────────────────────────────────────── -->
  <footer>
    <div class="container">
      <div class="footer-inner">
        <div class="footer-brand">
          <div class="nav-logo">
            <div class="nav-logo-mark"><img src="/logo.jpg" alt="mybisnis logo" /></div>
            <div class="nav-logo-text"><strong>mybisnis</strong></div>
          </div>
          <p class="tagline">Investasi nyata pada bisnis lokal yang bisa Anda lihat dan verifikasi sendiri.</p>
        </div>
        <nav class="footer-links" aria-label="Tautan kaki halaman">
          <a href="#">Kebijakan Privasi</a>
          <a href="#">Syarat &amp; Ketentuan</a>
          <a href="#">Hubungi Kami</a>
          <a href="#faq">FAQ</a>
        </nav>
      </div>
      <p class="footer-bottom">&copy; 2025 mybisnis. Di bawah naungan Koperasi Sari Sedana. Semua hak dilindungi.</p>
    </div>
  </footer>

  <!-- ── JAVASCRIPT ────────────────────────────────────────────────── -->
  <script>
    // Navbar scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });

    // Mobile nav toggle
    const navToggle = document.getElementById('navToggle');
    const navMobile = document.getElementById('navMobile');
    navToggle.addEventListener('click', () => {
      const open = navMobile.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', String(open));
    });
    navMobile.querySelectorAll('a').forEach(a =>
      a.addEventListener('click', () => {
        navMobile.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      })
    );

    // Scroll-triggered animations
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    document.querySelectorAll('.fade-in, .stagger').forEach(el => observer.observe(el));

    // Investment calculator
    const simAmount  = document.getElementById('simAmount');
    const simPeriod  = document.getElementById('simPeriod');
    const simDisplay = document.getElementById('simAmountDisplay');
    const simValue   = document.getElementById('simResultValue');
    const simSub     = document.getElementById('simResultSub');
    const periodLabels = { '0.06': 'bulan', '0.07': '3 bulan', '0.09': '6 bulan' };

    function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }
    function calcSim() {
      const amount = parseInt(simAmount.value, 10);
      const rate   = parseFloat(simPeriod.value);
      const label  = periodLabels[simPeriod.value] || 'periode';
      simDisplay.textContent = fmt(amount);
      simValue.textContent   = fmt(Math.round(amount * rate * 0.8)) + ' – ' + fmt(Math.round(amount * rate * 1.1));
      simSub.innerHTML       = 'dari investasi <strong>' + fmt(amount) + '</strong> per <strong>' + label + '</strong>';
    }
    simAmount.addEventListener('input', calcSim);
    simPeriod.addEventListener('change', calcSim);
    calcSim();

    // FAQ accordion
    document.querySelectorAll('.faq-q').forEach(btn => {
      btn.addEventListener('click', () => {
        const item   = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(i => {
          i.classList.remove('open');
          i.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) { item.classList.add('open'); btn.setAttribute('aria-expanded', 'true'); }
      });
    });

    // Active nav highlight on scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-links a');
    const secObserver = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          const id = e.target.getAttribute('id');
          navLinks.forEach(a => {
            a.style.color = a.getAttribute('href') === '#' + id ? 'var(--gold)' : '';
          });
        }
      });
    }, { threshold: 0.4 });
    sections.forEach(s => secObserver.observe(s));
  </script>
</body>
</html>
