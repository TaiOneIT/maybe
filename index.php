<?php
session_start();

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Read optional status messages from query params (after redirect from process-brief.php)
$status = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

// Simple helper to escape output
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SiteGate Rwanda — Gateway to Advanced Digital Solutions & Enterprise Systems</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Meta -->
  <meta name="description" content="SiteGate Rwanda — Custom E-Commerce platforms, Investment systems, Online Marketplaces, and News/Media Portals. Based in Kigali, Rwanda.">

  <style>
    :root {
      --rwanda-blue: #00AEEF;
      --rwanda-yellow: #F4C542;
      --rwanda-green: #1DBF73;
      --rwanda-navy: #0A2540;
      --rwanda-navy-2: #13315C;
      --rwanda-dark: #071b22;
      --glass-dark: rgba(13, 39, 61, 0.74);
      --panel-dark: rgba(17, 46, 73, 0.9);
      --panel-light: rgba(255,255,255,0.84);
      --text-dark: #F3F4F6;
      --text-light: #0f172a;
      --muted-dark: #dfeaf8;
      --muted-light: #475569;
      --line-dark: rgba(244, 197, 66, 0.22);
      --line-light: rgba(15,23,42,0.1);
    }

    html { scroll-behavior: smooth; }
    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(circle at top, rgba(0,174,239,0.2), transparent 30%),
                  radial-gradient(circle at bottom, rgba(29,191,115,0.12), transparent 24%),
                  linear-gradient(180deg, #0c2d48 0%, #0f3a59 25%, #0a2540 100%);
      color: var(--text-dark);
      transition: background 0.25s ease, color 0.25s ease;
    }
    body.bg-white {
      background: linear-gradient(180deg, #f7faf9 0%, #ecf7f6 30%, #eef7ff 100%);
      color: var(--text-light);
    }

    a { transition: all 0.2s ease; }
    .glass {
      background: rgba(10, 37, 64, 0.72);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(244, 197, 66, 0.2);
    }
    body.bg-white .glass {
      background: rgba(255,255,255,0.72);
      border-color: var(--line-light);
    }
    .card {
      background: linear-gradient(180deg, rgba(19,49,92,0.9), rgba(10,37,64,0.82));
      border: 1px solid rgba(244, 197, 66, 0.18);
      box-shadow: 0 20px 45px rgba(3, 12, 22, 0.22);
    }
    body.bg-white .card {
      background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.88));
      border-color: rgba(15,23,42,0.08);
      box-shadow: 0 20px 35px rgba(15, 23, 42, 0.08);
    }
    .tech-tag { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.04); }
    .section-kicker {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 9999px;
      background: rgba(0, 174, 239, 0.08); border: 1px solid rgba(244, 197, 66, 0.25); color: #dbeafe; font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase;
    }
    body.bg-white .section-kicker {
      background: rgba(14,165,233,0.08);
      color: #0369a1;
      border-color: rgba(14,165,233,0.18);
    }
    .metric-card {
      background: linear-gradient(180deg, rgba(19,49,92,0.9), rgba(10,37,64,0.82));
      border: 1px solid rgba(244,197,66,0.16); border-radius: 1rem;
    }
    body.bg-white .metric-card {
      background: rgba(255,255,255,0.9);
      border-color: rgba(15,23,42,0.05);
    }
    .timeline-dot::before { content: ''; width: 10px; height: 10px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 5px rgba(34,197,94,0.15); }
    .team-photo {
      height: 280px; width: 100%; object-fit: cover; filter: saturate(1.1) contrast(1.05);
    }
    .nav-link.active { color: #7dd3fc; }
    body.bg-white .nav-link.active { color: #0369a1; }

    body.bg-white .text-white,
    body.bg-white .text-slate-100,
    body.bg-white .text-slate-200,
    body.bg-white .text-slate-300,
    body.bg-white .text-slate-400,
    body.bg-white .text-slate-500,
    body.bg-white .text-slate-600,
    body.bg-white .text-slate-700,
    body.bg-white .text-slate-800,
    body.bg-white .text-slate-900 {
      color: #0f172a !important;
    }
    body.bg-white .text-slate-500,
    body.bg-white .text-slate-400 { color: #475569 !important; }
    body.bg-white .border-slate-700,
    body.bg-white .border-slate-800,
    body.bg-white .border-white\/5,
    body.bg-white .border-slate-600,
    body.bg-white .border-slate-500 {
      border-color: rgba(15, 23, 42, 0.12) !important;
    }
    body.bg-white .bg-slate-950\/70,
    body.bg-white .bg-slate-900\/70,
    body.bg-white .bg-slate-950\/60,
    body.bg-white .bg-slate-900,
    body.bg-white .bg-slate-950,
    body.bg-white .bg-slate-800 {
      background-color: rgba(255,255,255,0.9) !important;
    }
    body.bg-white input,
    body.bg-white textarea,
    body.bg-white select {
      background: rgba(255,255,255,0.82) !important;
      color: #0f172a !important;
      border-color: rgba(15, 23, 42, 0.12) !important;
    }
    body.bg-white input::placeholder,
    body.bg-white textarea::placeholder {
      color: #64748b !important;
    }

    .app-loader {
      position: fixed;
      inset: 0;
      z-index: 100;
      display: grid;
      place-items: center;
      background: radial-gradient(circle at center, rgba(244,197,66,0.15), rgba(0,174,239,0.12), rgba(10,37,64,0.97) 48%, rgba(7,18,28,1) 100%);
      transition: opacity 0.75s ease, visibility 0.75s ease;
    }
    .app-loader.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
    .app-loader-shell {
      position: relative;
      width: 180px;
      height: 180px;
      display: grid;
      place-items: center;
    }
    .app-loader-ring,
    .app-loader-ring::before,
    .app-loader-ring::after {
      position: absolute;
      content: "";
      border-radius: 9999px;
      inset: 0;
      border: 2px solid rgba(255,255,255,0.18);
    }
    .app-loader-ring {
      animation: rotateLogo 3s linear infinite;
      border-top-color: rgba(27,171,107,0.9);
      border-right-color: rgba(96,165,250,0.9);
      box-shadow: 0 0 30px rgba(96,165,250,0.25);
    }
    .app-loader-ring::before {
      inset: 16px;
      border-top-color: rgba(214,40,40,0.85);
      border-left-color: rgba(250,204,21,0.7);
      animation: pulseGlow 2s ease-in-out infinite alternate;
    }
    .app-loader-ring::after {
      inset: 34px;
      border-color: rgba(255,255,255,0.25);
    }
    .app-loader-mark {
      position: relative;
      width: 90px;
      height: 90px;
      display: grid;
      place-items: center;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #fff 0%, #fff5c7 12%, var(--rwanda-yellow) 26%, #1DBF73 58%, #00AEEF 100%);
      border: 3px solid rgba(255,255,255,0.9);
      box-shadow: 0 0 0 10px rgba(244,197,66,0.12), 0 0 40px rgba(0,174,239,0.25);
      font-size: 2rem;
      font-weight: 900;
      color: white;
      letter-spacing: -0.08em;
      text-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    .app-loader-name {
      position: absolute;
      bottom: -52px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.72rem;
      letter-spacing: 0.24em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.92);
      white-space: nowrap;
    }
    @keyframes rotateLogo { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes pulseGlow { from { transform: scale(0.96); opacity: 0.7; } to { transform: scale(1.03); opacity: 1; } }

    .brand-mark {
      width: 52px; height: 52px; border-radius: 9999px;
      background: radial-gradient(circle at 30% 30%, #fff 0%, #fce68a 10%, var(--rwanda-yellow) 26%, #1DBF73 56%, #00AEEF 100%);
      border: 3px solid rgba(255,255,255,0.75);
      display: grid; place-items: center;
      box-shadow: 0 0 0 7px rgba(244,197,66,0.18), 0 0 38px rgba(0,174,239,0.28);
      position: relative;
      overflow: hidden;
      font-weight: 900;
      font-size: 1.1rem;
      letter-spacing: -0.08em;
      color: #fff;
      text-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    .brand-mark::before,
    .brand-mark::after {
      content: ""; position: absolute; inset: -20%; border-radius: 50%; border: 1px solid rgba(255,255,255,0.35);
      animation: spinSoft 18s linear infinite;
    }
    .brand-letter {
      position: relative;
      z-index: 1;
      transform: translateY(-1px);
    }
    .brand-mark::after {
      inset: -32%; border-color: rgba(214,40,40,0.3); animation-direction: reverse; animation-duration: 26s;
    }
    .brand-mark svg { width: 28px; height: 28px; position: relative; z-index: 1; }

    .hero-shell {
      position: relative;
      min-height: 540px;
      display: grid;
      place-items: center;
      overflow: hidden;
    }
    .hero-orbit {
      position: absolute; inset: 0;
      background-image: radial-gradient(circle, rgba(14,165,233,0.18) 0, rgba(14,165,233,0.06) 1px, transparent 1px);
      background-size: 28px 28px;
      mask-image: radial-gradient(circle at center, black 34%, transparent 84%);
      opacity: 0.9;
    }
    .hero-visual {
      position: relative; width: min(460px, 90vw); height: min(460px, 84vw); display: grid; place-items: center;
    }
    .hero-planet {
      position: absolute; width: 296px; height: 296px; border-radius: 50%;
      background: radial-gradient(circle at 36% 28%, rgba(255,255,255,0.92), rgba(255,255,255,0.10) 22%, rgba(14,165,233,0.36) 29%, rgba(27,171,107,0.28) 60%, rgba(214,40,40,0.22) 100%);
      border: 1px solid rgba(255,255,255,0.14);
      box-shadow: 0 30px 80px rgba(14,165,233,0.18), 0 0 60px rgba(214,40,40,0.16), inset 0 0 28px rgba(255,255,255,0.15);
      display: grid; place-items: center;
      animation: floatPlanet 6s ease-in-out infinite alternate;
    }
    .hero-planet::before,
    .hero-planet::after {
      content: ""; position: absolute; inset: 18px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.28);
      transform: rotate(-12deg);
    }
    .hero-planet::after {
      inset: 38px; border-color: rgba(255,255,255,0.18);
      transform: rotate(18deg);
    }
    .logo-core {
      width: 170px; height: 170px; border-radius: 50%;
      background: radial-gradient(circle at 30% 25%, #f8fafc 0%, #a7f3d0 16%, var(--rwanda-green) 26%, var(--rwanda-blue) 52%, #07131b 100%);
      border: 4px solid rgba(255,255,255,0.75);
      box-shadow: 0 0 0 10px rgba(27,171,107,0.14), 0 0 50px rgba(14,165,233,0.35);
      position: relative; display: grid; place-items: center; z-index: 2;
    }
    .logo-core svg { width: 88px; height: 88px; }
    .ring-left, .ring-right {
      position: absolute; width: 420px; height: 420px; border-radius: 50%;
      border: 2px solid rgba(255,255,255,0.1); border-top-color: rgba(27,171,107,0.8); border-bottom-color: rgba(214,40,40,0.7);
      transform: rotate(28deg);
      filter: blur(0.1px);
    }
    .ring-left { animation: rotateLeft 18s linear infinite; }
    .ring-right { transform: rotate(-28deg) scale(0.94); border-top-color: rgba(14,165,233,0.85); border-left-color: rgba(214,40,40,0.8); animation: rotateRight 16s linear infinite; }
    .hero-glow {
      position: absolute; width: 100%; height: 100%; border-radius: 50%;
      background: radial-gradient(circle, rgba(214,40,40,0.18), rgba(14,165,233,0.16) 25%, transparent 62%);
      filter: blur(28px);
      z-index: 0;
    }
    @keyframes rotateLeft { from { transform: rotate(0deg) scale(1); } to { transform: rotate(360deg) scale(1); } }
    @keyframes rotateRight { from { transform: rotate(-10deg) scale(0.94); } to { transform: rotate(-370deg) scale(0.94); } }
    @keyframes floatPlanet { 0% { transform: translateY(0px) scale(0.99); } 100% { transform: translateY(-12px) scale(1.02); } }
    @keyframes spinSoft { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    body.theme-gold {
      color: #30240f;
      background: radial-gradient(circle at 50% 0%, rgba(255, 231, 137, 0.42), transparent 38%),
                  linear-gradient(180deg, #edc52f 0%, #e4b91e 52%, #dcae18 100%);
    }
    body.theme-gold .glass {
      background: rgba(229, 184, 27, 0.97);
      border-color: rgba(101, 77, 12, 0.22);
      box-shadow: 0 8px 28px rgba(91, 65, 0, 0.08);
    }
    body.theme-gold .card {
      background: linear-gradient(145deg, rgba(255, 252, 242, 0.98), rgba(255, 246, 217, 0.96));
      border-color: rgba(101, 77, 12, 0.16);
      box-shadow: 0 18px 42px rgba(91, 65, 0, 0.12);
    }
    body.theme-gold .metric-card {
      background: rgba(255, 249, 232, 0.9);
      border-color: rgba(101, 77, 12, 0.16);
    }
    body.theme-gold .section-kicker {
      color: #6b4f00;
      background: rgba(255, 249, 232, 0.72);
      border-color: rgba(101, 77, 12, 0.2);
    }
    body.theme-gold .nav-link.active { color: #654900; }
    body.theme-gold [class~="text-white"],
    body.theme-gold [class~="text-slate-100"],
    body.theme-gold [class~="text-slate-200"],
    body.theme-gold [class~="text-slate-300"],
    body.theme-gold [class~="text-slate-700"],
    body.theme-gold [class~="text-slate-800"],
    body.theme-gold [class~="text-slate-900"] { color: #30240f !important; }
    body.theme-gold [class~="text-slate-400"],
    body.theme-gold [class~="text-slate-500"],
    body.theme-gold [class~="text-slate-600"] { color: #65552f !important; }
    body.theme-gold [class~="text-[#7dd3fc]"],
    body.theme-gold [class~="text-[#86efac]"],
    body.theme-gold [class~="text-[#9ae6b4]"],
    body.theme-gold [class~="text-[#fca5a5]"],
    body.theme-gold [class~="text-[#facc15]"],
    body.theme-gold [class~="text-[#f87171]"] { color: #765600 !important; }
    body.theme-gold [class~="text-green-100"],
    body.theme-gold [class~="text-red-100"] { color: #30240f !important; }
    body.theme-gold [class~="bg-slate-950/70"],
    body.theme-gold [class~="bg-slate-900/70"],
    body.theme-gold [class~="bg-slate-950/60"],
    body.theme-gold [class~="bg-slate-900"],
    body.theme-gold [class~="bg-slate-950"],
    body.theme-gold [class~="bg-slate-800"],
    body.theme-gold [class~="bg-[#0b1f1a]"],
    body.theme-gold [class~="bg-[#0f172a]"],
    body.theme-gold [class~="bg-[#022c22]"],
    body.theme-gold [class~="bg-[#082f49]"],
    body.theme-gold [class~="bg-[#3f1d1d]"],
    body.theme-gold [class~="bg-green-900/50"],
    body.theme-gold [class~="bg-red-900/50"] { background: rgba(255, 249, 232, 0.92) !important; }
    body.theme-gold [class~="bg-[#0ea5e9]/20"],
    body.theme-gold [class~="bg-[#22c55e]/20"],
    body.theme-gold [class~="bg-[#f87171]/20"] { background-color: rgba(212, 154, 0, 0.14) !important; }
    body.theme-gold [class~="border-slate-700"],
    body.theme-gold [class~="border-slate-800"],
    body.theme-gold [class~="border-white/5"],
    body.theme-gold [class~="border-slate-600"],
    body.theme-gold [class~="border-slate-500"],
    body.theme-gold [class~="border-[#1bab6b]"],
    body.theme-gold [class~="border-[#22c55e]/40"],
    body.theme-gold [class~="border-[#0ea5e9]/40"],
    body.theme-gold [class~="border-[#f87171]/40"],
    body.theme-gold [class~="border-green-700"],
    body.theme-gold [class~="border-red-700"] { border-color: rgba(101, 77, 12, 0.2) !important; }
    body.theme-gold input,
    body.theme-gold textarea,
    body.theme-gold select {
      background: rgba(255, 253, 247, 0.96) !important;
      color: #30240f !important;
      border-color: rgba(101, 77, 12, 0.24) !important;
    }
    body.theme-gold input::placeholder,
    body.theme-gold textarea::placeholder { color: #82734f !important; }
    body.theme-gold .primary-action {
      background: linear-gradient(135deg, #a87300, #d49a00 60%, #e4ad0b);
      color: #fffdf5;
      box-shadow: 0 10px 24px rgba(109, 76, 0, 0.2);
    }
    body.theme-gold .primary-action:hover { background: linear-gradient(135deg, #8f6200, #bd8700 60%, #d49a00); }
    body.theme-gold .nav-link:hover,
    body.theme-gold footer a:hover,
    body.theme-gold [class~="hover:text-[#7dd3fc]"]:hover { color: #765600 !important; }
    body.theme-gold [class~="hover:border-[#7dd3fc]"]:hover,
    body.theme-gold [class~="hover:bg-[#1bab6b]"]:hover { border-color: #a87300 !important; background-color: #a87300 !important; color: #fffdf5 !important; }
    body.theme-gold footer {
      background: rgba(226, 177, 23, 0.24);
      border-color: rgba(101, 77, 12, 0.2);
    }
    body.theme-gold .app-loader {
      background: radial-gradient(circle at center, #ffeb9c 0%, #f2ca3c 58%, #e9bb22 100%);
    }
    body.theme-gold .app-loader-ring {
      border-top-color: #fff9e8;
      border-right-color: #936a00;
      box-shadow: 0 0 30px rgba(116, 82, 0, 0.18);
    }
    body.theme-gold .app-loader-ring::before {
      border-top-color: rgba(255,255,255,0.9);
      border-left-color: rgba(147,106,0,0.7);
    }
    body.theme-gold .app-loader-ring::after { border-color: rgba(147,106,0,0.35); }
    body.theme-gold .app-loader-mark,
    body.theme-gold .brand-mark,
    body.theme-gold .logo-core {
      background: radial-gradient(circle at 30% 25%, #fffdf5 0%, #ffe787 42%, #d49a00 100%);
      color: #654900;
      text-shadow: none;
      border-color: rgba(255,255,255,0.88);
      box-shadow: 0 0 0 8px rgba(255,255,255,0.22), 0 0 32px rgba(116,82,0,0.16);
    }
    body.theme-gold .app-loader-name { color: #493600; }
    body.theme-gold .brand-mark::before { border-color: rgba(255,255,255,0.65); }
    body.theme-gold .brand-mark::after { border-color: rgba(101,77,12,0.24); }
    body.theme-gold .hero-orbit {
      background-image: radial-gradient(circle, rgba(116,82,0,0.16) 0, rgba(116,82,0,0.05) 1px, transparent 1px);
    }
    body.theme-gold .hero-planet {
      background: radial-gradient(circle at 36% 28%, rgba(255,255,255,0.96), rgba(255,239,177,0.82) 35%, rgba(234,190,47,0.76) 68%, rgba(176,122,0,0.42) 100%);
      border-color: rgba(255,255,255,0.58);
      box-shadow: 0 30px 80px rgba(116,82,0,0.16), 0 0 60px rgba(255,237,158,0.3), inset 0 0 28px rgba(255,255,255,0.36);
    }
    body.theme-gold .hero-planet::before { border-color: rgba(101,77,12,0.22); }
    body.theme-gold .hero-planet::after { border-color: rgba(101,77,12,0.14); }
    body.theme-gold .ring-left,
    body.theme-gold .ring-right {
      border-color: rgba(101,77,12,0.14);
      border-top-color: rgba(255,255,255,0.85);
      border-bottom-color: rgba(147,106,0,0.65);
    }
    body.theme-gold .ring-right { border-left-color: rgba(255,255,255,0.82); }
    body.theme-gold .hero-glow {
      background: radial-gradient(circle, rgba(255,231,137,0.55), rgba(147,106,0,0.12) 35%, transparent 68%);
    }
    body.theme-gold .primary-action,
    body.theme-gold .nav-link,
    body.theme-gold header { color: #30240f; }
    body.theme-gold .team-photo { filter: saturate(0.92) contrast(1.02); }
    body.theme-gold input[type="checkbox"] { accent-color: #a87300; }
    body.theme-gold .timeline-dot::before {
      background: #a87300;
      box-shadow: 0 0 0 5px rgba(168,115,0,0.14);
    }

    @media (max-width: 767px) {
      .brand-mark { width: 42px; height: 42px; }
      .team-photo { height: 240px; }
      .hero-shell { min-height: 420px; }
      .hero-planet { width: 220px; height: 220px; }
      .logo-core { width: 130px; height: 130px; }
      .ring-left, .ring-right { width: 300px; height: 300px; }
    }
  </style>
</head>
<body class="theme-gold min-h-screen antialiased">

<div id="appLoader" class="app-loader" aria-live="polite" aria-label="Loading SiteGate Rwanda">
  <div class="app-loader-shell">
    <div class="app-loader-ring"></div>
    <div class="app-loader-mark">SG</div>
    <div class="app-loader-name">SiteGate Rwanda</div>
  </div>
</div>

<header class="fixed w-full z-40 top-0 left-0">
  <nav class="glass py-3 px-4 sm:px-8 flex items-center justify-between">
    <a href="#home" class="flex items-center gap-3 group">
      <div class="brand-mark"><span class="brand-letter">SG</span></div>
      <div class="flex flex-col leading-tight">
        <span class="font-black text-xl tracking-tight">SiteGate <span class="text-[#7dd3fc]">Rwanda</span></span>
        <span class="text-[10px] uppercase tracking-[0.22em]">Digital Innovation</span>
      </div>
    </a>

    <div class="hidden md:flex items-center gap-7 text-sm">
      <a class="nav-link hover:text-[#7dd3fc]" href="#home">Home</a>
      <a class="nav-link hover:text-[#7dd3fc]" href="#about">About</a>
      <a class="nav-link hover:text-[#7dd3fc]" href="#services">Services</a>
      <a class="nav-link hover:text-[#7dd3fc]" href="#career">Career</a>
      <a class="nav-link hover:text-[#7dd3fc]" href="#team">Team</a>
      <a class="nav-link hover:text-[#7dd3fc]" href="#contact">Contact</a>
      <a href="downloads/sitegate-rwanda-mobile-app.zip" download class="px-3 py-1.5 rounded-md border border-[#1bab6b] bg-[#0b1f1a] text-[#9ae6b4] font-medium hover:bg-[#1bab6b] hover:text-[#04140f]">Download App</a>
    </div>

    <div class="md:hidden flex items-center gap-2">
      <a href="#contact" class="px-2.5 py-2 rounded-md border border-[#1bab6b] bg-[#0b1f1a] text-[#9ae6b4] text-xs font-medium">App</a>
      <button id="mobileBtn" class="p-2 rounded-md hover:bg-white/5" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </nav>

  <div id="mobileMenu" class="hidden md:hidden glass px-4 py-4 border-t border-white/5">
    <a class="block py-2 nav-link" href="#home">Home</a>
    <a class="block py-2 nav-link" href="#about">About</a>
    <a class="block py-2 nav-link" href="#services">Services</a>
    <a class="block py-2 nav-link" href="#career">Career</a>
    <a class="block py-2 nav-link" href="#team">Team</a>
    <a class="block py-2 nav-link" href="#contact">Contact</a>
    <a class="block py-2 mt-2 px-3 py-2 rounded-md border border-[#1bab6b] text-[#9ae6b4]" href="downloads/sitegate-rwanda-mobile-app.zip" download>Download App</a>
  </div>
</header>

<main class="pt-28">
  <section id="home" class="relative overflow-hidden">
    <div class="hero-orbit"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 lg:py-18">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div class="relative z-10">
          <span class="section-kicker">Rwanda • East Africa • Digital Growth</span>
          <h1 class="mt-6 text-4xl sm:text-5xl xl:text-6xl font-black leading-tight tracking-tight">
            Building clean, modern digital systems for ambitious business growth.
          </h1>
          <p class="mt-5 max-w-xl text-lg">
            SiteGate Rwanda creates premium websites, e-commerce experiences, business portals, and digital products designed for trust, visibility, and measurable results in Rwanda and beyond.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="#contact" class="primary-action inline-flex items-center px-6 py-3 rounded-xl font-bold">Request a Quote</a>
            <a href="#services" class="inline-flex items-center px-6 py-3 rounded-xl border border-slate-700 text-slate-100 hover:border-[#7dd3fc] hover:text-[#7dd3fc]">Explore Services</a>
          </div>

          <div class="mt-8 grid grid-cols-3 gap-4 max-w-lg">
            <div class="metric-card p-4 text-center">
              <div class="text-2xl font-black text-[#7dd3fc]" data-target="124">0</div>
              <div class="text-xs uppercase tracking-[0.15em]">Projects</div>
            </div>
            <div class="metric-card p-4 text-center">
              <div class="text-2xl font-black text-[#86efac]" data-target="18">0</div>
              <div class="text-xs uppercase tracking-[0.15em]">Services</div>
            </div>
            <div class="metric-card p-4 text-center">
              <div class="text-2xl font-black text-[#fca5a5]" data-target="36">0</div>
              <div class="text-xs uppercase tracking-[0.15em]">Clients</div>
            </div>
          </div>
        </div>

        <div class="hero-shell relative z-10">
          <div class="hero-glow"></div>
          <div class="hero-visual">
            <div class="ring-left"></div>
            <div class="ring-right"></div>
            <div class="hero-planet">
              <div class="logo-core" aria-label="SiteGate Rwanda logo">
                <span class="brand-letter" style="font-size: 2.8rem; line-height: 1;">SG</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-4 sm:px-8 py-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="card p-6 rounded-2xl">
        <div class="text-[#7dd3fc] text-2xl font-black">01</div>
        <h3 class="mt-3 text-xl font-semibold text-white">Strategy-first design</h3>
        <p class="mt-2 text-slate-300">We align visuals, messaging, and user journey with your real business goals before building.</p>
      </div>
      <div class="card p-6 rounded-2xl">
        <div class="text-[#86efac] text-2xl font-black">02</div>
        <h3 class="mt-3 text-xl font-semibold text-white">Built for Rwanda</h3>
        <p class="mt-2 text-slate-300">From mobile money to local market realities, our systems are shaped for the way East African businesses operate.</p>
      </div>
      <div class="card p-6 rounded-2xl">
        <div class="text-[#fca5a5] text-2xl font-black">03</div>
        <h3 class="mt-3 text-xl font-semibold text-white">Scalable execution</h3>
        <p class="mt-2 text-slate-300">Our solutions are designed to grow with your business, team and customers as demand increases.</p>
      </div>
    </div>
  </section>

  <section id="about" class="max-w-7xl mx-auto px-4 sm:px-8 py-16">
    <div class="grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <span class="section-kicker">About us</span>
        <h2 class="mt-5 text-3xl sm:text-4xl font-black text-white">We turn business needs into polished digital experiences.</h2>
        <p class="mt-4 text-slate-300 text-lg">
          SiteGate Rwanda is a digital product and web systems partner helping startups, SMEs, investors, and growing brands launch professional online platforms that are secure, responsive, and built for conversion.
        </p>
        <p class="mt-4 text-slate-300">
          Our work combines product thinking, modern UX, system architecture, and local market understanding to deliver platforms that users trust and businesses can scale.
        </p>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="card p-4 rounded-2xl">
            <div class="text-[#7dd3fc] font-bold text-sm uppercase tracking-[0.18em]">Mission</div>
            <p class="mt-3 text-slate-300">To help organizations in Rwanda and beyond build digital systems that create growth, trust and efficiency.</p>
          </div>
          <div class="card p-4 rounded-2xl">
            <div class="text-[#86efac] font-bold text-sm uppercase tracking-[0.18em]">Vision</div>
            <p class="mt-3 text-slate-300">To be the go-to digital transformation partner for businesses building the future of East Africa.</p>
          </div>
        </div>
      </div>

      <div class="card rounded-3xl p-6 lg:p-8">
        <div class="grid gap-5">
          <div class="rounded-2xl border border-slate-700 bg-slate-900/70 p-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#0ea5e9]/20 flex items-center justify-center text-[#7dd3fc] font-black">A</div>
              <div>
                <div class="font-semibold text-white">Agile processes</div>
                <div class="text-sm text-slate-400">Design, build, launch, iterate</div>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-slate-700 bg-slate-900/70 p-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#22c55e]/20 flex items-center justify-center text-[#86efac] font-black">S</div>
              <div>
                <div class="font-semibold text-white">Secure foundations</div>
                <div class="text-sm text-slate-400">CSRF protection, validation, and clean architecture</div>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-slate-700 bg-slate-900/70 p-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#f87171]/20 flex items-center justify-center text-[#fca5a5] font-black">L</div>
              <div>
                <div class="font-semibold text-white">Local market expertise</div>
                <div class="text-sm text-slate-400">Tailored for Rwanda and East Africa</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="services" class="max-w-7xl mx-auto px-4 sm:px-8 py-16">
    <div class="flex items-end justify-between gap-4 flex-wrap">
      <div>
        <span class="section-kicker">Services</span>
        <h2 class="mt-5 text-3xl sm:text-4xl font-black text-white">Digital services built to scale business outcomes.</h2>
      </div>
      <p class="max-w-xl text-slate-300">We deliver websites, portals, payment-ready systems, and digital infrastructure that support traction, trust, and measurable growth.</p>
    </div>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
      <div class="card p-6 rounded-2xl service-card" data-title="Website & Brand Experience Design" data-desc="Clean, conversion-focused websites that help businesses explain value clearly, build trust, and turn visitors into customers. We design a strong visual identity, UX flow, and messaging strategy that match the company’s ambition." data-tech="UX, UI, Branding, SEO">
        <div class="text-[#7dd3fc] text-3xl">●</div>
        <h3 class="mt-4 text-xl font-semibold text-white">Website & Brand Experience</h3>
        <p class="mt-3 text-slate-300">High-impact websites for companies wanting a stronger online identity and better conversion.</p>
      </div>
      <div class="card p-6 rounded-2xl service-card" data-title="E-commerce & Local Payments" data-desc="Robust e-commerce systems that support local payments, inventory management, orders, and user journeys tailored for products and services in Rwanda and East Africa." data-tech="PHP, MySQL, Payment APIs, JS">
        <div class="text-[#86efac] text-3xl">●</div>
        <h3 class="mt-4 text-xl font-semibold text-white">E-commerce & Payments</h3>
        <p class="mt-3 text-slate-300">Storefronts, shopping flows, and digital sales systems built for performance and trust.</p>
      </div>
      <div class="card p-6 rounded-2xl service-card" data-title="Business Portals & Dashboards" data-desc="Custom dashboards, investor portals, internal systems, and management views that centralize data and improve operational visibility." data-tech="Laravel, PHP, MySQL, Analytics">
        <div class="text-[#fca5a5] text-3xl">●</div>
        <h3 class="mt-4 text-xl font-semibold text-white">Portals & Dashboards</h3>
        <p class="mt-3 text-slate-300">Operational tools for investment, reporting, sales, and management teams.</p>
      </div>
      <div class="card p-6 rounded-2xl service-card" data-title="Media, CMS & Content Platforms" data-desc="Flexible and fast publishing systems for media houses, content businesses, and digital brands that need structure, editorial workflows, and SEO support." data-tech="CMS, SEO, Publishing, CMS Architecture">
        <div class="text-[#facc15] text-3xl">●</div>
        <h3 class="mt-4 text-xl font-semibold text-white">CMS & Publishing</h3>
        <p class="mt-3 text-slate-300">Content systems for editorial teams, newsrooms, businesses and digital publications.</p>
      </div>
    </div>

    <div class="mt-12 card rounded-3xl p-6 lg:p-8">
      <div class="grid lg:grid-cols-2 gap-8 items-center">
        <div>
          <div class="text-sm uppercase tracking-[0.2em] text-[#7dd3fc]">Selected project overview</div>
          <h3 class="mt-3 text-2xl font-bold text-white">Recent delivery snapshots</h3>
          <p class="mt-3 text-slate-300">Our project mix combines online retail, investor systems, and publishing experiences tailored to fast-moving businesses across Rwanda.</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="rounded-2xl border border-slate-700 bg-slate-950/60 p-4">
            <div class="text-2xl font-black text-[#7dd3fc]">12+</div>
            <div class="mt-2 text-sm text-slate-400">Web platforms launched</div>
          </div>
          <div class="rounded-2xl border border-slate-700 bg-slate-950/60 p-4">
            <div class="text-2xl font-black text-[#86efac]">8</div>
            <div class="mt-2 text-sm text-slate-400">Marketplace or commerce products</div>
          </div>
          <div class="rounded-2xl border border-slate-700 bg-slate-950/60 p-4">
            <div class="text-2xl font-black text-[#fca5a5]">4</div>
            <div class="mt-2 text-sm text-slate-400">Investor or dashboard systems</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="career" class="max-w-7xl mx-auto px-4 sm:px-8 py-16">
    <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-8 items-start">
      <div>
        <span class="section-kicker">Career</span>
        <h2 class="mt-5 text-3xl sm:text-4xl font-black text-white">Join a team building the next generation of digital business.</h2>
        <p class="mt-4 text-slate-300 text-lg">We are growing a team of thinkers, designers, developers, and marketers who care about quality, speed, and outcomes.</p>

        <div class="mt-8 space-y-5">
          <div class="card p-5 rounded-2xl">
            <div class="flex items-center justify-between gap-4 flex-wrap">
              <div>
                <div class="text-sm uppercase tracking-[0.2em] text-[#7dd3fc]">Full-time</div>
                <h3 class="mt-2 text-xl font-semibold text-white">Senior Full-Stack Developer</h3>
              </div>
              <span class="px-3 py-1 rounded-full border border-[#22c55e]/40 bg-[#022c22] text-[#86efac] text-xs uppercase tracking-[0.15em]">Kigali</span>
            </div>
            <p class="mt-3 text-slate-300">Build modern web products, manage architecture decisions, and help the team ship clean, scalable systems.</p>
          </div>
          <div class="card p-5 rounded-2xl">
            <div class="flex items-center justify-between gap-4 flex-wrap">
              <div>
                <div class="text-sm uppercase tracking-[0.2em] text-[#86efac]">Contract</div>
                <h3 class="mt-2 text-xl font-semibold text-white">UI/UX Designer</h3>
              </div>
              <span class="px-3 py-1 rounded-full border border-[#0ea5e9]/40 bg-[#082f49] text-[#7dd3fc] text-xs uppercase tracking-[0.15em]">Hybrid</span>
            </div>
            <p class="mt-3 text-slate-300">Design premium experiences for businesses, with a strong eye for conversion, clarity and product storytelling.</p>
          </div>
          <div class="card p-5 rounded-2xl">
            <div class="flex items-center justify-between gap-4 flex-wrap">
              <div>
                <div class="text-sm uppercase tracking-[0.2em] text-[#fca5a5]">Part-time</div>
                <h3 class="mt-2 text-xl font-semibold text-white">Digital Marketing Specialist</h3>
              </div>
              <span class="px-3 py-1 rounded-full border border-[#f87171]/40 bg-[#3f1d1d] text-[#fca5a5] text-xs uppercase tracking-[0.15em]">Remote</span>
            </div>
            <p class="mt-3 text-slate-300">Drive lead generation, campaign performance and digital growth for our product and client portfolio.</p>
          </div>
        </div>
      </div>

      <div class="card rounded-3xl p-6">
        <div class="text-sm uppercase tracking-[0.2em] text-[#7dd3fc]">Why join us</div>
        <div class="mt-6 space-y-5">
          <div class="flex gap-4">
            <div class="timeline-dot mt-1 w-5 h-5"></div>
            <div>
              <h4 class="font-semibold text-white">Meaningful product work</h4>
              <p class="text-slate-300">Build solutions that influence real business outcomes and user trust.</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="timeline-dot mt-1 w-5 h-5"></div>
            <div>
              <h4 class="font-semibold text-white">Creative freedom</h4>
              <p class="text-slate-300">Work on strategic digital experiences with room to innovate and improve.</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="timeline-dot mt-1 w-5 h-5"></div>
            <div>
              <h4 class="font-semibold text-white">Growth culture</h4>
              <p class="text-slate-300">Learn from market-facing delivery and collaborate across design, technology, and marketing.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="team" class="max-w-7xl mx-auto px-4 sm:px-8 py-16">
    <div class="text-center max-w-3xl mx-auto">
      <span class="section-kicker">Our team</span>
      <h2 class="mt-5 text-3xl sm:text-4xl font-black text-white">Experienced leaders with a clear product vision.</h2>
    </div>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
      <article class="card rounded-3xl overflow-hidden">
        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80" alt="CEO portrait" class="team-photo">
        <div class="p-6">
          <div class="text-[#7dd3fc] uppercase tracking-[0.15em] text-xs">CEO</div>
          <h3 class="mt-2 text-2xl font-bold text-white">Eric Nshuti</h3>
          <p class="mt-2 text-slate-300">Leads strategy, partnerships, and digital growth programs across business sectors.</p>
          <div class="mt-4 text-sm text-slate-400">+250 788 200 100<br>ceo@sitegate.rw</div>
        </div>
      </article>

      <article class="card rounded-3xl overflow-hidden">
        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=900&q=80" alt="Co-founder portrait" class="team-photo">
        <div class="p-6">
          <div class="text-[#86efac] uppercase tracking-[0.15em] text-xs">Co-Founder</div>
          <h3 class="mt-2 text-2xl font-bold text-white">Mireille Uwase</h3>
          <p class="mt-2 text-slate-300">Shapes product direction and customer experience across enterprise and startup projects.</p>
          <div class="mt-4 text-sm text-slate-400">+250 788 300 200<br>partners@sitegate.rw</div>
        </div>
      </article>

      <article class="card rounded-3xl overflow-hidden">
        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=900&q=80" alt="Operations portrait" class="team-photo">
        <div class="p-6">
          <div class="text-[#fca5a5] uppercase tracking-[0.15em] text-xs">Operations Manager</div>
          <h3 class="mt-2 text-2xl font-bold text-white">Patrick Mugisha</h3>
          <p class="mt-2 text-slate-300">Coordinates delivery, quality assurance, and client communication across all engagements.</p>
          <div class="mt-4 text-sm text-slate-400">+250 788 400 300<br>ops@sitegate.rw</div>
        </div>
      </article>
    </div>
  </section>

  <section id="contact" class="max-w-7xl mx-auto px-4 sm:px-8 py-16 pb-24">
    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-8">
      <div class="card rounded-3xl p-6 lg:p-8">
        <span class="section-kicker">Contact</span>
        <h2 class="mt-5 text-3xl font-black text-white">Tell us what you need, and we’ll reach out soon.</h2>
        <p class="mt-4 text-slate-300">Whether you need a new website, a business portal, or digital growth support, we’re ready to help you map the right solution.</p>

        <div class="mt-8 space-y-4 text-slate-300">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-[#0ea5e9]/20 text-[#7dd3fc] font-black flex items-center justify-center">E</div>
            <div>
              <div class="font-semibold text-white">Email</div>
              <a href="mailto:hello@sitegate.rw" class="text-[#7dd3fc]">hello@sitegate.rw</a>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-[#22c55e]/20 text-[#86efac] font-black flex items-center justify-center">P</div>
            <div>
              <div class="font-semibold text-white">Phone</div>
              <a href="tel:+250788000000" class="text-[#86efac]">+250 788 000 000</a>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-[#f87171]/20 text-[#fca5a5] font-black flex items-center justify-center">L</div>
            <div>
              <div class="font-semibold text-white">Location</div>
              <div>Kigali, Rwanda</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card rounded-3xl p-6 lg:p-8">
        <h3 class="text-2xl font-bold text-white">Project brief</h3>
        <p class="mt-2 text-slate-300">Share your challenge, timeline and goals. We use this to prepare a suitable proposal.</p>

        <?php if ($status === 'success'): ?>
          <div class="mt-4 p-4 rounded-xl bg-green-900/50 border border-green-700 text-green-100">
            <?= e($message ?: 'Thanks! Your inquiry has been received. We will be in touch soon.') ?>
          </div>
        <?php elseif ($status === 'error'): ?>
          <div class="mt-4 p-4 rounded-xl bg-red-900/50 border border-red-700 text-red-100">
            <?= e($message ?: 'There was an error submitting your inquiry. Please try again later.') ?>
          </div>
        <?php endif; ?>

        <form id="briefForm" action="process-brief.php" method="post" class="mt-6 grid gap-4">
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input required maxlength="100" name="name" placeholder="Full name" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white placeholder:text-slate-400" />
            <input required type="email" maxlength="150" name="email" placeholder="Email address" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white placeholder:text-slate-400" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input name="company" maxlength="150" placeholder="Company name" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white placeholder:text-slate-400" />
            <input name="phone" maxlength="40" placeholder="Phone number" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white placeholder:text-slate-400" />
          </div>

          <div class="flex flex-wrap gap-3 text-sm text-slate-300">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="services[]" value="ecommerce" class="rounded"> Website</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="services[]" value="investment" class="rounded"> Portal</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="services[]" value="marketplace" class="rounded"> Marketplace</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="services[]" value="cms" class="rounded"> CMS</label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select name="budget" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white">
              <option value="">Estimated budget</option>
              <option value="under_5k">Under $5,000</option>
              <option value="5k_20k">$5,000 - $20,000</option>
              <option value="20k_100k">$20,000 - $100,000</option>
              <option value="100k_plus">$100,000+</option>
            </select>
            <select name="timeline" class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white">
              <option value="">Timeline</option>
              <option value="1_3_months">1 - 3 months</option>
              <option value="3_6_months">3 - 6 months</option>
              <option value="6_12_months">6 - 12 months</option>
            </select>
          </div>

          <textarea name="message" maxlength="5000" rows="5" placeholder="Share the problem, your goals, your timeline, and what you need from us." class="p-3 rounded-xl bg-slate-950/70 border border-slate-700 text-white placeholder:text-slate-400"></textarea>

          <div class="flex items-center gap-4 flex-wrap">
            <button type="submit" class="primary-action px-5 py-3 rounded-xl font-bold">Send Inquiry</button>
            <button type="reset" class="px-4 py-3 rounded-xl border border-slate-700 text-slate-200">Reset</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<footer class="border-t border-slate-800 bg-slate-950/60">
  <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 grid md:grid-cols-3 gap-6">
    <div>
      <div class="flex items-center gap-3">
        <div class="brand-mark" style="width: 38px; height: 38px; font-size: 0.9rem;"><span class="brand-letter">SG</span></div>
        <div>
          <div class="font-black text-white">SiteGate Rwanda</div>
          <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400">Digital systems</div>
        </div>
      </div>
      <p class="mt-4 text-slate-400">Professional websites, portals and digital platforms shaped for the realities of Rwanda and East Africa.</p>
    </div>

    <div>
      <h4 class="font-semibold text-white">Quick links</h4>
      <ul class="mt-3 space-y-2 text-slate-400">
        <li><a href="#home" class="hover:text-[#7dd3fc]">Home</a></li>
        <li><a href="#about" class="hover:text-[#7dd3fc]">About</a></li>
        <li><a href="#services" class="hover:text-[#7dd3fc]">Services</a></li>
        <li><a href="#team" class="hover:text-[#7dd3fc]">Team</a></li>
      </ul>
    </div>

    <div>
      <h4 class="font-semibold text-white">Contact</h4>
      <ul class="mt-3 space-y-2 text-slate-400">
        <li><a href="mailto:hello@sitegate.rw" class="hover:text-[#7dd3fc]">hello@sitegate.rw</a></li>
        <li><a href="tel:+250788000000" class="hover:text-[#7dd3fc]">+250 788 000 000</a></li>
        <li>Kigali, Rwanda</li>
      </ul>
    </div>
  </div>

  <div class="border-t border-slate-800 py-5 text-center text-slate-500 text-sm">
    &copy; <?= date('Y') ?> SiteGate Rwanda. All rights reserved.
  </div>
</footer>

<script>
  const mobileBtn = document.getElementById('mobileBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  mobileBtn && mobileBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });

  const body = document.body;
  const appLoader = document.getElementById('appLoader');

  window.addEventListener('load', () => {
    setTimeout(() => {
      if (appLoader) {
        appLoader.classList.add('hidden');
      }
      body.classList.add('loaded');
    }, 1400);
  });
  const navLinks = document.querySelectorAll('.nav-link');
  const sections = [...document.querySelectorAll('main section[id], footer')].filter(Boolean);
  const setActiveNav = () => {
    const current = sections.findLast(section => window.scrollY + 120 >= section.offsetTop);
    navLinks.forEach(link => {
      const active = current && link.getAttribute('href') === '#' + current.id;
      link.classList.toggle('active', !!active);
    });
  };
  window.addEventListener('scroll', setActiveNav);
  setActiveNav();

  const counters = document.querySelectorAll('[data-target]');
  counters.forEach(counter => {
    const target = Number(counter.getAttribute('data-target')) || 0;
    let current = 0;
    const duration = 1400;
    const stepTime = Math.max(20, Math.floor(duration / target));
    const timer = setInterval(() => {
      current += Math.ceil(target / (duration / stepTime));
      if (current >= target) {
        counter.textContent = target;
        clearInterval(timer);
      } else {
        counter.textContent = current;
      }
    }, stepTime);
  });

  const serviceCards = document.querySelectorAll('.service-card');
  const serviceModal = document.getElementById('serviceModal');
  const closeServiceModal = document.getElementById('closeServiceModal');
  const closeModal2 = document.getElementById('closeModal2');
  const serviceTitle = document.getElementById('serviceTitle');
  const serviceDesc = document.getElementById('serviceDesc');
  const serviceTech = document.getElementById('serviceTech');

  if (serviceModal) {
    serviceCards.forEach(card => {
      card.addEventListener('click', () => {
        if (serviceTitle) serviceTitle.textContent = card.dataset.title || '';
        if (serviceDesc) serviceDesc.textContent = card.dataset.desc || '';
        if (serviceTech) serviceTech.textContent = card.dataset.tech || '';
        serviceModal.classList.remove('hidden');
      });
    });

    [closeServiceModal, closeModal2].forEach(btn => {
      btn && btn.addEventListener('click', () => {
        serviceModal.classList.add('hidden');
      });
    });
  }

  const briefForm = document.getElementById('briefForm');
  briefForm && briefForm.addEventListener('submit', (e) => {
    const token = briefForm.querySelector('input[name="csrf_token"]');
    if (!token || token.value.length < 10) {
      e.preventDefault();
      alert('Security token missing: please reload the page and try again.');
    }
  });
</script>
</body>
</html>
