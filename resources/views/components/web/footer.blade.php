@props([
    'hidden' => false,
])

{{-- ═══════════════════════════════════════════
    Footer Section — MindFitBro
═══════════════════════════════════════════ --}}
<footer class="w-full bg-primary overflow-hidden">

    {{-- ─── Top Wave Divider ─── --}}
    <div class="w-full overflow-hidden leading-none -mb-1">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-[60px] block">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,0 L0,0 Z" fill="#EEEEEE"/>
        </svg>
    </div>

    {{-- ─── Main Footer Content ─── --}}
    <div class="px-4 lg:px-20 pt-14 lg:pt-20 pb-12">

        {{-- Grid: 4 columns --}}
        <div class="grid grid-cols-1 md:grid-cols-2 {{ $hidden ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }} gap-12">

            {{-- ── Column 1: Brand ── --}}
            <div class="flex flex-col gap-6 lg:col-span-1">

                {{-- Logo / Brand Name --}}
                <div class="flex flex-col gap-3">
                    <a href="/" class="inline-block">
                        <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro Logo" class="w-52 mb-2">
                    </a>
                    <p class="font-arabic text-sm leading-relaxed text-white/60 max-w-[220px]">
                        مش برنامج… ده أسلوب حياة. بنبني عقلية منضبطة وجسم أقوى مع مجتمع بيدعمك.
                    </p>
                </div>

                {{-- Social Icons --}}
                <div class="flex gap-2.5">
                    {{-- Instagram --}}
                    <a href="#"
                        class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#D4ED57]/20 hover:border-[#D4ED57]/40 transition-all duration-300 group">
                        <svg viewBox="0 0 20 20" width="18" height="18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor" class="text-white/60 group-hover:text-[#D4ED57] transition-colors duration-300">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Dribbble-Light-Preview" transform="translate(-340.000000, -7439.000000)" fill="currentColor">
                                        <g id="icons" transform="translate(56.000000, 160.000000)">
                                            <path d="M289.869652,7279.12273 C288.241769,7279.19618 286.830805,7279.5942 285.691486,7280.72871 C284.548187,7281.86918 284.155147,7283.28558 284.081514,7284.89653 C284.035742,7285.90201 283.768077,7293.49818 284.544207,7295.49028 C285.067597,7296.83422 286.098457,7297.86749 287.454694,7298.39256 C288.087538,7298.63872 288.809936,7298.80547 289.869652,7298.85411 C298.730467,7299.25511 302.015089,7299.03674 303.400182,7295.49028 C303.645956,7294.859 303.815113,7294.1374 303.86188,7293.08031 C304.26686,7284.19677 303.796207,7282.27117 302.251908,7280.72871 C301.027016,7279.50685 299.5862,7278.67508 289.869652,7279.12273 M289.951245,7297.06748 C288.981083,7297.0238 288.454707,7296.86201 288.103459,7296.72603 C287.219865,7296.3826 286.556174,7295.72155 286.214876,7294.84312 C285.623823,7293.32944 285.819846,7286.14023 285.872583,7284.97693 C285.924325,7283.83745 286.155174,7282.79624 286.959165,7281.99226 C287.954203,7280.99968 289.239792,7280.51332 297.993144,7280.90837 C299.135448,7280.95998 300.179243,7281.19026 300.985224,7281.99226 C301.980262,7282.98483 302.473801,7284.28014 302.071806,7292.99991 C302.028024,7293.96767 301.865833,7294.49274 301.729513,7294.84312 C300.829003,7297.15085 298.757333,7297.47145 289.951245,7297.06748 M298.089663,7283.68956 C298.089663,7284.34665 298.623998,7284.88065 299.283709,7284.88065 C299.943419,7284.88065 300.47875,7284.34665 300.47875,7283.68956 C300.47875,7283.03248 299.943419,7282.49847 299.283709,7282.49847 C298.623998,7282.49847 298.089663,7283.03248 298.089663,7283.68956 M288.862673,7288.98792 C288.862673,7291.80286 291.150266,7294.08479 293.972194,7294.08479 C296.794123,7294.08479 299.081716,7291.80286 299.081716,7288.98792 C299.081716,7286.17298 296.794123,7283.89205 293.972194,7283.89205 C291.150266,7283.89205 288.862673,7286.17298 288.862673,7288.98792 M290.655732,7288.98792 C290.655732,7287.16159 292.140329,7285.67967 293.972194,7285.67967 C295.80406,7285.67967 297.288657,7287.16159 297.288657,7288.98792 C297.288657,7290.81525 295.80406,7292.29716 293.972194,7292.29716 C292.140329,7292.29716 290.655732,7290.81525 290.655732,7288.98792" id="instagram-[#167]">
                                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                        </svg>
                    </a>
                    {{-- TikTok --}}
                    <a href="#"
                        class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#D4ED57]/20 hover:border-[#D4ED57]/40 transition-all duration-300 group">
                        <svg fill="currentColor" viewBox="0 0 32 32" width="18" height="18" class="text-white/60 group-hover:text-[#D4ED57] transition-colors duration-300" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"/>
                        </svg>
                    </a>
                    {{-- YouTube --}}
                    <a href="#"
                        class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#D4ED57]/20 hover:border-[#D4ED57]/40 transition-all duration-300 group">
                        <svg fill="currentColor" viewBox="0 0 32 32" width="18" height="18" class="text-white/60 group-hover:text-[#D4ED57] transition-colors duration-300" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.932 20.459v-8.917l7.839 4.459zM30.368 8.735c-0.354-1.301-1.354-2.307-2.625-2.663l-0.027-0.006c-3.193-0.406-6.886-0.638-10.634-0.638-0.381 0-0.761 0.002-1.14 0.007l0.058-0.001c-0.322-0.004-0.701-0.007-1.082-0.007-3.748 0-7.443 0.232-11.070 0.681l0.434-0.044c-1.297 0.363-2.297 1.368-2.644 2.643l-0.006 0.026c-0.4 2.109-0.628 4.536-0.628 7.016 0 0.088 0 0.176 0.001 0.263l-0-0.014c-0 0.074-0.001 0.162-0.001 0.25 0 2.48 0.229 4.906 0.666 7.259l-0.038-0.244c0.354 1.301 1.354 2.307 2.625 2.663l0.027 0.006c3.193 0.406 6.886 0.638 10.634 0.638 0.38 0 0.76-0.002 1.14-0.007l-0.058 0.001c0.322 0.004 0.702 0.007 1.082 0.007 3.749 0 7.443-0.232 11.070-0.681l-0.434 0.044c1.298-0.362 2.298-1.368 2.646-2.643l0.006-0.026c0.399-2.109 0.627-4.536 0.627-7.015 0-0.088-0-0.176-0.001-0.263l0 0.013c0-0.074 0.001-0.162 0.001-0.25 0-2.48-0.229-4.906-0.666-7.259l0.038 0.244z"/>
                        </svg>
                    </a>
                    {{-- WhatsApp --}}
                    <a href="#"
                        class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/10 flex items-center justify-center hover:bg-[#D4ED57]/20 hover:border-[#D4ED57]/40 transition-all duration-300 group">
                        <svg fill="currentColor" viewBox="0 0 24 24" width="18" height="18" class="text-white/60 group-hover:text-[#D4ED57] transition-colors duration-300" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
            </div>

            @if (!$hidden)
                {{-- ── Column 2: Quick Links ── --}}
                <div class="flex flex-col gap-5 font-arabic">
                    <h4 class="text-white font-black text-base relative pb-3 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-8 after:h-[2px] after:bg-[#D4ED57] after:rounded-full">
                        روابط سريعة
                    </h4>
                    <ul class="flex flex-col gap-3.5">
                        @foreach([
                            ['label' => 'الرئيسية',         'href' => route('home')],
                            ['label' => 'برامجنا التدريبية', 'href' => route('home') . '#programs'],
                            ['label' => 'نتائج عملاؤنا',     'href' => route('home') . '#before-after'],
                            ['label' => 'آراء المشتركين',    'href' => route('home') . '#testimonials'],
                            ['label' => 'تواصل معنا',        'href' => route('home') . '#contact'],
                            ['label' => 'حاسبة السعرات الحرارية', 'href' => route('calorie-calculator')],
                        ] as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                                class="group flex items-center gap-2 text-sm text-white/60 hover:text-white transition-colors duration-300 w-fit">
                                <span class="material-symbols-rounded !text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 group-hover:text-accent flex-shrink-0">arrow_back_ios</span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Column 3: Plans ── --}}
            <div class="flex flex-col gap-5 font-arabic">
                <h4 class="text-white font-black text-base relative pb-3 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-8 after:h-[2px] after:bg-[#D4ED57] after:rounded-full">
                    الباقات
                </h4>
                <ul class="flex flex-col gap-4">
                    @foreach([
                        ['name' => 'ستارتر',       'price' => '299', 'icon' => 'bolt'],
                        ['name' => 'برو',           'price' => '599', 'icon' => 'star'],
                        ['name' => 'إيليت',         'price' => '999', 'icon' => 'emoji_events'],
                    ] as $plan)
                    <li>
                        <a href="{{ route('home') }}#programs"
                            class="group flex items-center justify-between gap-3 bg-white/5 hover:bg-white/10 border border-white/5 hover:border-[#D4ED57]/30 rounded-[12px] px-4 py-3 transition-all duration-300">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-rounded text-[#D4ED57]/70 group-hover:text-[#D4ED57] transition-colors" style="font-size:16px">{{ $plan['icon'] }}</span>
                                <span class="text-sm font-bold text-white/70 group-hover:text-white transition-colors">{{ $plan['name'] }}</span>
                            </div>
                            <span class="text-xs font-black text-[#D4ED57]/60 group-hover:text-[#D4ED57] transition-colors font-display flex items-center gap-1">
                                {{ $plan['price'] }}
                                <svg width="10" height="12" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="inline-block flex-shrink-0 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <path d="M9.36633 2.59339C10.0415 1.83554 10.4564 1.4953 11.2713 1.06514V13.6848L9.36633 14.0784V2.59339Z" fill="currentColor"/>
                                    <path d="M15.4529 8.93793C15.8478 8.10434 15.8943 7.73386 16 6.87871L1.39805 10.0494C1.05179 10.8207 0.940326 11.2518 0.886964 12.0176L15.4529 8.93793Z" fill="currentColor"/>
                                    <path d="M15.4529 12.8033C15.8478 11.9697 15.8943 11.5992 16 10.744L9.43602 12.1334C9.38956 12.8975 9.44292 13.2895 9.38956 14.0552L15.4529 12.8033Z" fill="currentColor"/>
                                    <path d="M15.4529 16.668C15.8478 15.8345 15.8943 15.464 16 14.6088L10.0168 15.9077C9.7148 16.3245 9.52895 17.0191 9.38956 17.92L15.4529 16.668Z" fill="currentColor"/>
                                    <path d="M5.95136 15.3519C6.53213 14.6341 7.13614 13.7311 7.5543 12.9901L0.51109 14.5167C0.164822 15.2881 0.0533618 15.7192 0 16.4849L5.95136 15.3519Z" fill="currentColor"/>
                                    <path d="M5.64935 1.52825C6.32448 0.770398 6.73938 0.430158 7.5543 0V13.0364L5.64935 13.4301V1.52825Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('home') }}#programs"
                    class="group mt-1 font-arabic text-textColor bg-accent px-4 py-2.5 rounded-full text-sm font-black flex justify-center items-center gap-2 transition-all duration-300 hover:bg-yellow-300 w-fit">
                    أشترك الآن
                    <svg class="transition-transform duration-300 group-hover:-translate-x-1" width="18" height="10" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </a>
            </div>

            {{-- ── Column 4: .... + Contact ── --}}
            <div class="flex flex-col gap-5 font-arabic">
                <h4 class="text-white font-black text-base relative pb-3 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-8 after:h-[2px] after:bg-[#D4ED57] after:rounded-full">
                    ابقى على تواصل
                </h4>

                {{-- Mini Contact Info --}}
                <div class="flex flex-col gap-3.5">
                    <a href="tel:+966593035979" class="group flex items-center gap-3 text-sm text-white/60 hover:text-white transition-colors duration-300">
                        <div class="w-8 h-8 rounded-[8px] bg-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-[#D4ED57]/20 transition-colors">
                            <span class="material-symbols-rounded text-[#D4ED57]/60 group-hover:text-[#D4ED57]" style="font-size:16px">call</span>
                        </div>
                        <span dir="ltr">+966 593 035 979</span>
                    </a>
                    <a href="mailto:info@mindfitbro.com" class="group flex items-center gap-3 text-sm text-white/60 hover:text-white transition-colors duration-300">
                        <div class="w-8 h-8 rounded-[8px] bg-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-[#D4ED57]/20 transition-colors">
                            <span class="material-symbols-rounded text-[#D4ED57]/60 group-hover:text-[#D4ED57]" style="font-size:16px">mail</span>
                        </div>
                        info@mindfitbro.com
                    </a>
                    <div class="flex items-center gap-3 text-sm text-white/60">
                        <div class="w-8 h-8 rounded-[8px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-[#D4ED57]/60" style="font-size:16px">location_on</span>
                        </div>
                        الرياض، المملكة العربية السعودية
                    </div>
                </div>

                <hr class="border-white/10">

                {{-- الرقم الضريبي والسجل التجاري --}}
                {{-- <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3 text-sm text-white/60">
                        <div class="w-8 h-8 rounded-[8px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-[#D4ED57]/60" style="font-size:16px">receipt_long</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[10px] text-white/30 font-bold">الرقم الضريبي</span>
                            <span>3xxxxxxxxxx</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-white/60">
                        <div class="w-8 h-8 rounded-[8px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-[#D4ED57]/60" style="font-size:16px">corporate_fare</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[10px] text-white/30 font-bold">السجل التجاري</span>
                            <span>10xxxxxxxx</span>
                        </div>
                    </div>
                </div> --}}

            </div>

        </div>

        {{-- ─── Divider ─── --}}
        <div class="mt-16 mb-8 border-t border-white/10"></div>

        {{-- ─── Bottom Bar ─── --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 font-arabic">

            {{-- Copyright --}}
            <p class="text-xs text-white/30 font-medium">
                © {{ date('Y') }} MindFitBro — جميع الحقوق محفوظة
            </p>

            {{-- Middle: Guarantee Badge --}}
            <div>
                <img src="{{ asset('assets/logo/pro1.png') }}" alt="ProTeamsCo Logo" class="w-32 opacity-15">
            </div>

            {{-- Legal Links --}}
            <div class="flex items-center gap-5 text-xs text-white/30">
                <a href="{{ route('privacy-policy') }}" class="hover:text-white/60 transition-colors duration-300">سياسة الخصوصية</a>
                <span class="text-white/10">|</span>
                <a href="{{ route('terms-of-service') }}" class="hover:text-white/60 transition-colors duration-300">الشروط والأحكام</a>
            </div>

        </div>

    </div>

</footer>
