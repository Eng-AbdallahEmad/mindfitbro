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

        {{-- ── Gradient header ── --}}
        <div class="bg-gradient-to-br from-primary to-blue-700 px-6 pb-8 text-center relative overflow-hidden" style="padding-top:2.5rem">

            {{-- Dot grid texture --}}
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>

            {{-- Decorative glow blobs --}}
            <div style="position:absolute;top:-40px;left:-40px;width:180px;height:180px;border-radius:50%;background:rgba(212,237,87,.08);pointer-events:none;"></div>
            <div style="position:absolute;bottom:-30px;right:-20px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

            <div class="relative z-10">

                {{-- Rocket icon — block-level (flex justify-center) so badge stays BELOW it --}}
                <div id="ceremony-rocket" class="flex justify-center mb-5">
                    <div class="w-20 h-20 rounded-2xl bg-white/15 flex items-center justify-center ring-2 ring-white/20 ring-offset-2 ring-offset-transparent">
                        <span class="material-symbols-rounded text-accent"
                              style="font-size:44px;font-variation-settings:'FILL' 1">rocket_launch</span>
                    </div>
                </div>

                {{-- Badge --}}
                <div class="inline-flex items-center gap-1.5 bg-white/15 border border-white/25 rounded-full px-3.5 py-1.5 text-white text-[11px] font-black mb-4">
                    <span class="material-symbols-rounded"
                          style="font-size:13px;font-variation-settings:'FILL' 1">celebration</span>
                    {{ __('messages.user_dashboard.upcoming_today_badge') }}
                </div>

                <h2 class="text-white text-2xl font-black leading-tight mb-1.5">
                    {{ __('messages.user_dashboard.ceremony_title') }}
                </h2>
                <p class="text-white/60 text-sm mb-1">{{ $todayStr }}</p>
                <p class="text-accent text-sm font-bold">{{ __('messages.user_dashboard.ceremony_subtitle') }}</p>

            </div>
        </div>

        {{-- ── Plan chip + CTA ── --}}
        <div class="px-5 py-5 flex flex-col gap-4">

            @if($plan)
            <div class="flex items-center gap-3 bg-[#F4F7FF] rounded-2xl p-3.5 border border-primary/10 w-full"
                 style="direction:{{ $dir }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ $plan->icon_bg ?? '#EFF5FF' }}">
                    <span class="material-symbols-rounded"
                          style="font-size:20px;font-variation-settings:'FILL' 1;color:{{ $plan->icon_color ?? '#174DAD' }}">
                        {{ $plan->icon ?? 'fitness_center' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-black text-textColor leading-none mb-0.5 truncate">{{ $planName }}</p>
                    <p class="text-xs text-gray-400 font-bold">
                        {{ $subscription->duration_months }} {{ __('messages.user_dashboard.upcoming_months') }}
                    </p>
                </div>
                <span class="flex-shrink-0 text-[10px] font-black text-primary bg-primary/8 px-2.5 py-1 rounded-full font-arabic whitespace-nowrap">
                    {{ __('messages.user_dashboard.upcoming_today_badge') }}
                </span>
            </div>
            @endif

            {{-- CTA --}}
            <button id="startJourneyBtn" type="button"
                    data-label="{{ __('messages.user_dashboard.ceremony_btn') }}"
                    data-loading="{{ __('messages.user_dashboard.ceremony_loading') }}"
                    class="w-full flex items-center justify-center gap-2.5 bg-primary text-white font-black font-arabic
                           text-base px-8 py-4 rounded-2xl hover:bg-primary/90 active:scale-95 transition-all
                           shadow-lg shadow-primary/25">
                <span class="material-symbols-rounded flex-shrink-0"
                      style="font-size:22px;color:#D4ED57">rocket_launch</span>
                {{ __('messages.user_dashboard.ceremony_btn') }}
            </button>

            <p class="text-[11px] text-gray-400 font-bold text-center -mt-1">
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
            rocketEl.classList.add('launching');
            setTimeout(function () {
                var h = ceremonyCard.offsetHeight;
                ceremonyCard.style.height   = h + 'px';
                ceremonyCard.style.overflow  = 'hidden';
                void ceremonyCard.offsetHeight;
                ceremonyCard.style.transition = 'height 0.45s ease, opacity 0.35s ease';
                requestAnimationFrame(function () {
                    ceremonyCard.style.height  = '0';
                    ceremonyCard.style.opacity = '0';
                });
            }, 450);
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
            bar._cTarget         = bar.style.width;
            bar.style.transition = 'none';
            bar.style.width      = '0';
        });

        activeWrap.style.height        = 'auto';
        activeWrap.style.overflow      = 'visible';
        activeWrap.style.opacity       = '1';
        activeWrap.style.pointerEvents = 'auto';
        activeWrap.removeAttribute('aria-hidden');

        if (instant) {
            items.forEach(function (el) { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; });
            if (ring) ring.style.strokeDashoffset = ring._cTarget;
            bars.forEach(function (bar) { bar.style.width = bar._cTarget; });
            return;
        }

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                items.forEach(function (el, i) {
                    setTimeout(function () {
                        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        el.style.opacity    = '1';
                        el.style.transform  = 'translateY(0)';
                    }, i * 70);
                });
                if (ring) {
                    setTimeout(function () {
                        ring.style.transition       = '';
                        ring.style.strokeDashoffset = ring._cTarget;
                    }, 150);
                }
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
