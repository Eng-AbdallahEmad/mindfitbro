@php
    $planName = $plan
        ? (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name)
        : '—';
    $todayStr = now()->locale($locale)
        ->isoFormat($isRtl ? 'dddd، D MMMM YYYY' : 'dddd, MMMM D, YYYY');
@endphp

{{-- ═══════════════════════════════════════════════════════
     CEREMONY CARD (visible on load)
════════════════════════════════════════════════════════ --}}
<div id="ceremony-wrap" class="flex flex-col gap-5 anim anim-1">
    <div id="ceremony-card" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden font-arabic">

        {{-- Gradient header --}}
        <div class="bg-gradient-to-br from-primary to-blue-700 px-8 py-12 text-center relative overflow-hidden">
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
            <div class="relative z-10">

                {{-- Rocket icon (becomes the launch target) --}}
                <div id="ceremony-rocket" class="mb-5" style="display:inline-block">
                    <div class="w-24 h-24 rounded-3xl bg-white/15 flex items-center justify-center" style="display:inline-flex">
                        <span class="material-symbols-rounded text-accent"
                              style="font-size:52px;font-variation-settings:'FILL' 1">rocket_launch</span>
                    </div>
                </div>

                {{-- Date badge --}}
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-white text-[11px] font-black mb-4">
                    <span class="material-symbols-rounded"
                          style="font-size:13px;font-variation-settings:'FILL' 1">celebration</span>
                    {{ __('messages.user_dashboard.upcoming_today_badge') }}
                </div>
                <h2 class="text-white text-2xl font-black mb-2">{{ __('messages.user_dashboard.ceremony_title') }}</h2>
                <p class="text-white/70 text-sm mb-1">{{ $todayStr }}</p>
                <p class="text-accent text-sm font-bold">{{ __('messages.user_dashboard.ceremony_subtitle') }}</p>

            </div>
        </div>

        {{-- Plan chip + CTA --}}
        <div class="px-6 py-6 flex flex-col items-center gap-4">

            @if($plan)
            <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3.5 border border-gray-100 w-full max-w-xs"
                 style="direction:{{ $dir }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ $plan->icon_bg ?? '#EFF5FF' }}">
                    <span class="material-symbols-rounded"
                          style="font-size:20px;font-variation-settings:'FILL' 1;color:{{ $plan->icon_color ?? '#174DAD' }}">
                        {{ $plan->icon ?? 'fitness_center' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-black text-textColor leading-none mb-0.5">{{ $planName }}</p>
                    <p class="text-xs text-gray-400 font-bold">
                        {{ $subscription->duration_months }} {{ __('messages.user_dashboard.upcoming_months') }}
                    </p>
                </div>
            </div>
            @endif

            {{-- CTA — data-* keeps translation strings out of inline JS --}}
            <button id="startJourneyBtn" type="button"
                    data-label="{{ __('messages.user_dashboard.ceremony_btn') }}"
                    data-loading="{{ __('messages.user_dashboard.ceremony_loading') }}"
                    class="flex items-center justify-center gap-2 bg-primary text-white font-black font-arabic text-base
                           px-10 py-4 rounded-2xl hover:bg-primary/90 active:scale-95 transition-all
                           shadow-lg shadow-primary/30">
                <span class="material-symbols-rounded flex-shrink-0"
                      style="font-size:20px;color:#D4ED57">rocket_launch</span>
                {{ __('messages.user_dashboard.ceremony_btn') }}
            </button>

            <p class="text-xs text-gray-400 font-bold text-center">
                {{ __('messages.user_dashboard.ceremony_desc') }}
            </p>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     ACTIVE DASHBOARD — pre-rendered, revealed after ceremony
════════════════════════════════════════════════════════ --}}
<div id="active-wrap"
     style="height:0;overflow:hidden;opacity:0;pointer-events:none;"
     aria-hidden="true">
    @include('components.web.dashboard.active')
</div>

{{-- Rocket launch keyframe --}}
<style>
@keyframes _rocketLaunch {
    0%   { transform: translateY(0) scale(1); opacity: 1; }
    20%  { transform: translateY(-12px) scale(1.18); }
    70%  { transform: translateY(-130px) scale(0.8); opacity: 1; }
    100% { transform: translateY(-260px) scale(0.55); opacity: 0; }
}
#ceremony-rocket.launching {
    animation: _rocketLaunch 0.85s cubic-bezier(.22,1,.36,1) forwards;
    will-change: transform, opacity;
}
@media (prefers-reduced-motion: reduce) {
    #ceremony-rocket.launching { animation: none !important; }
}
</style>

<script>
(function () {
    var btn          = document.getElementById('startJourneyBtn');
    var ceremonyWrap = document.getElementById('ceremony-wrap');
    var rocketEl     = document.getElementById('ceremony-rocket');
    var ceremonyCard = document.getElementById('ceremony-card');
    var activeWrap   = document.getElementById('active-wrap');
    var reduced      = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var fired        = false;

    btn.addEventListener('click', function () {
        if (fired) return;
        fired       = true;
        btn.disabled = true;
        btn.textContent = btn.dataset.loading;

        fetch('{{ route("dashboard.start-journey") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                'Accept': 'application/json',
            },
        })
        .then(function (res) { if (!res.ok) throw new Error(); return res; })
        .then(function () {
            if (reduced) {
                ceremonyWrap.hidden = true;
                revealDashboard(true);
                return;
            }
            // Step 1: rocket launch animation
            rocketEl.classList.add('launching');
            // Step 2: collapse ceremony card
            setTimeout(function () {
                var h = ceremonyCard.offsetHeight;
                ceremonyCard.style.height  = h + 'px';
                ceremonyCard.style.overflow = 'hidden';
                void ceremonyCard.offsetHeight; // force reflow before transition
                ceremonyCard.style.transition = 'height 0.45s ease, opacity 0.35s ease';
                requestAnimationFrame(function () {
                    ceremonyCard.style.height  = '0';
                    ceremonyCard.style.opacity = '0';
                });
            }, 450);
            // Step 3: swap to dashboard
            setTimeout(function () {
                ceremonyWrap.hidden = true;
                revealDashboard(false);
            }, 1050);
        })
        .catch(function () {
            fired = false;
            btn.disabled    = false;
            btn.textContent = btn.dataset.label;
        });
    });

    function revealDashboard(instant) {
        var items = activeWrap.querySelectorAll('.card, .card-dark');
        var ring  = document.getElementById('journeyRing');
        var bars  = activeWrap.querySelectorAll('.macro-bar-fill');

        // Reset start states BEFORE making the container visible
        items.forEach(function (el) {
            el.style.transition = 'none';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(20px)';
        });
        if (ring) {
            ring._cTarget               = ring.dataset.dashoffset || '408';
            ring.style.transition       = 'none';
            ring.style.strokeDashoffset = '408';
        }
        bars.forEach(function (bar) {
            bar._cTarget      = bar.style.width;
            bar.style.transition = 'none';
            bar.style.width   = '0';
        });

        // Reveal the container
        activeWrap.style.height       = 'auto';
        activeWrap.style.overflow     = 'visible';
        activeWrap.style.opacity      = '1';
        activeWrap.style.pointerEvents = 'auto';
        activeWrap.removeAttribute('aria-hidden');

        if (instant) {
            items.forEach(function (el) {
                el.style.opacity   = '1';
                el.style.transform = 'translateY(0)';
            });
            if (ring) ring.style.strokeDashoffset = ring._cTarget;
            bars.forEach(function (bar) { bar.style.width = bar._cTarget; });
            return;
        }

        // Two rAF frames so the browser commits the initial reset before animating
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                // Stagger card entrances
                items.forEach(function (el, i) {
                    setTimeout(function () {
                        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        el.style.opacity    = '1';
                        el.style.transform  = 'translateY(0)';
                    }, i * 70);
                });
                // Journey ring
                if (ring) {
                    setTimeout(function () {
                        ring.style.transition       = ''; // restore CSS .ring-fill transition
                        ring.style.strokeDashoffset = ring._cTarget;
                    }, 150);
                }
                // Macro bars
                setTimeout(function () {
                    bars.forEach(function (bar) {
                        bar.style.transition = '';
                        bar.style.width      = bar._cTarget;
                    });
                }, 280);
            });
        });
    }
})();
</script>
