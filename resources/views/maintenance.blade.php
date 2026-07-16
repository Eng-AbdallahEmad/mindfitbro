<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>الموقع تحت الصيانة — MindFitBro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:   #174DAD;
            --accent:    #EAB308;
            --dark-bg:   #0d1f4a;
            --mid-bg:    #1a3fa0;
            --light-bg:  #3a68c8;
        }

        html, body {
            width: 100%; height: 100%; overflow: hidden;
            font-family: 'Cairo', sans-serif;
            background: var(--dark-bg);
        }

        /* ── Background ── */
        .bg-wrap {
            position: fixed; inset: 0; z-index: 0;
            background: radial-gradient(ellipse 80% 70% at 50% 50%,
                var(--light-bg) 0%, var(--mid-bg) 55%, var(--dark-bg) 100%);
            overflow: hidden;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: .25;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .orb-1 { width: 600px; height: 600px; background: #5b87e0; top: -200px; right: -150px; animation-duration: 14s; }
        .orb-2 { width: 400px; height: 400px; background: var(--accent); bottom: -120px; left: -100px; animation-duration: 10s; animation-delay: -5s; }
        .orb-3 { width: 300px; height: 300px; background: #174DAD; top: 40%; left: 55%; animation-duration: 16s; animation-delay: -8s; }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, -40px) scale(1.08); }
        }

        /* ── Content ── */
        .stage {
            position: relative; z-index: 1;
            width: 100%; height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2rem;
            gap: 0;
        }

        /* ── Logo ── */
        .logo { width: 160px; object-fit: contain; margin-bottom: 2rem; }

        /* ── Gear Icon ── */
        .gear-wrap {
            position: relative; width: 88px; height: 88px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.75rem;
        }
        .gear-wrap::before {
            content: '';
            position: absolute; inset: -8px;
            border-radius: 50%;
            border: 2px solid rgba(234,179,8,.25);
            animation: ping-ring 2.4s ease-in-out infinite;
        }
        @keyframes ping-ring {
            0%,100% { transform: scale(1); opacity: .5; }
            50%      { transform: scale(1.18); opacity: 0; }
        }

        .gear-bg {
            width: 88px; height: 88px; border-radius: 50%;
            background: rgba(234,179,8,.15);
            border: 1.5px solid rgba(234,179,8,.3);
            display: flex; align-items: center; justify-content: center;
        }

        /* CSS-only gear SVG via clip-path alternative — inline SVG animated */
        .gear-svg {
            width: 44px; height: 44px;
            animation: spin-gear 6s linear infinite;
            fill: var(--accent);
            filter: drop-shadow(0 0 8px rgba(234,179,8,.5));
        }
        @keyframes spin-gear {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ── Card ── */
        .card {
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 28px;
            padding: 2.5rem 3rem;
            max-width: 560px; width: 100%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,.25);
        }
        @media (max-width: 480px) {
            .card { padding: 2rem 1.5rem; }
        }

        .headline {
            font-size: clamp(1.4rem, 4vw, 2rem);
            font-weight: 900; color: #fff;
            letter-spacing: -.01em;
            margin-bottom: .75rem;
            line-height: 1.3;
        }

        .sub {
            font-size: clamp(.9rem, 2.5vw, 1.05rem);
            font-weight: 600; color: rgba(255,255,255,.7);
            line-height: 1.7; margin-bottom: 1.5rem;
        }

        /* ── ETA / Countdown ── */
        .eta-box {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(234,179,8,.15);
            border: 1px solid rgba(234,179,8,.3);
            border-radius: 999px;
            padding: .5rem 1.25rem;
            margin-bottom: 1.75rem;
            font-size: .85rem; font-weight: 700;
            color: var(--accent);
        }
        .eta-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent);
            animation: blink 1.4s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; } 50% { opacity: .2; }
        }

        .countdown-grid {
            display: flex; gap: .75rem; justify-content: center;
            margin-bottom: 1.75rem;
        }
        .cd-unit {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 14px;
            padding: .6rem .85rem;
            min-width: 64px;
            text-align: center;
        }
        .cd-num {
            display: block;
            font-size: 1.6rem; font-weight: 900; color: #fff;
            line-height: 1;
        }
        .cd-label {
            display: block;
            font-size: .65rem; font-weight: 700;
            color: rgba(255,255,255,.45);
            margin-top: .2rem;
        }

        /* ── WhatsApp button ── */
        .wa-btn {
            display: inline-flex; align-items: center; gap: .6rem;
            background: #25D366;
            color: #fff; text-decoration: none;
            font-size: .9rem; font-weight: 800;
            padding: .75rem 1.75rem;
            border-radius: 999px;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 6px 24px rgba(37,211,102,.35);
        }
        .wa-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(37,211,102,.45); }

        .wa-icon { width: 20px; height: 20px; flex-shrink: 0; }

        /* ── Footer note ── */
        .footer-note {
            margin-top: 2.5rem;
            font-size: .72rem; font-weight: 600;
            color: rgba(255,255,255,.3);
            letter-spacing: .03em;
        }
    </style>
</head>
<body>

{{-- Background --}}
<div class="bg-wrap">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

{{-- Content --}}
<div class="stage">

    {{-- Logo --}}
    <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="logo">

    {{-- Animated Gear --}}
    <div class="gear-wrap">
        <div class="gear-bg">
            <svg class="gear-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12 3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5 3.5 3.5 0 0 1-3.5 3.5m7.43-2.92c.04-.34.07-.69.07-1.08s-.03-.73-.07-1.08l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.69-.07 1.08s.03.73.07 1.08l-2.11 1.63c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.58 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.63z"/>
            </svg>
        </div>
    </div>

    {{-- Card --}}
    <div class="card">
        <h1 class="headline">الموقع تحت الصيانة حالياً</h1>
        <p class="sub">{{ $message }}</p>

        @if($eta)
            @php
                $etaTs  = \Carbon\Carbon::parse($eta);
                $future = $etaTs->isFuture();
            @endphp
            @if($future)
            {{-- Live countdown --}}
            <div class="countdown-grid" id="countdown">
                <div class="cd-unit"><span class="cd-num" id="cd-d">00</span><span class="cd-label">يوم</span></div>
                <div class="cd-unit"><span class="cd-num" id="cd-h">00</span><span class="cd-label">ساعة</span></div>
                <div class="cd-unit"><span class="cd-num" id="cd-m">00</span><span class="cd-label">دقيقة</span></div>
                <div class="cd-unit"><span class="cd-num" id="cd-s">00</span><span class="cd-label">ثانية</span></div>
            </div>
            @else
            <div class="eta-box">
                <span class="eta-dot"></span>
                متوقع العودة في {{ $etaTs->format('d/m/Y — h:i A') }}
            </div>
            @endif
        @endif

        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="wa-btn">
            <svg class="wa-icon" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            تواصل معنا على واتساب
        </a>
    </div>

    <p class="footer-note">MindFitBro &copy; {{ date('Y') }}</p>
</div>

@if($eta && \Carbon\Carbon::parse($eta)->isFuture())
<script>
(function() {
    var target = {{ \Carbon\Carbon::parse($eta)->timestamp * 1000 }};
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var diff = Math.max(0, Math.floor((target - Date.now()) / 1000));
        var d = Math.floor(diff / 86400);
        var h = Math.floor((diff % 86400) / 3600);
        var m = Math.floor((diff % 3600) / 60);
        var s = diff % 60;
        document.getElementById('cd-d').textContent = pad(d);
        document.getElementById('cd-h').textContent = pad(h);
        document.getElementById('cd-m').textContent = pad(m);
        document.getElementById('cd-s').textContent = pad(s);
        if (diff > 0) setTimeout(tick, 1000);
    }
    tick();
})();
</script>
@endif

</body>
</html>
