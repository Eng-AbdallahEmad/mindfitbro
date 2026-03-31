<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }

    .mfb-bar {
        width: 5px;
        height: var(--h);
        border-radius: 3px;
        background: #D4ED57;
        animation: mfbBounce 1s ease-in-out infinite;
        animation-delay: var(--d);
    }
    @keyframes mfbBounce {
        0%, 100% { transform: scaleY(1);   }
        50%       { transform: scaleY(1.5); }
    }
    @keyframes mfbFadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0);    }
    }
    @keyframes mfbProgress {
        from { width: 0%;    }
        to   { width: 100%;  }
    }


</style>
@yield('style')

<!-- Styles / Scripts -->
@vite(['resources/css/app.css', 'resources/js/app.js'])