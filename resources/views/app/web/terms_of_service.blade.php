@extends('layouts.web.app')

@section('title', 'الشروط والأحكام')

@section('content')

    {{-- Nav Bar --}}
    <x-web.navbar :transparent="true" />

    {{-- Hero Header --}}
    <section class="w-full bg-primary pt-36 pb-16 px-6 text-center relative overflow-hidden">

        {{-- Orbs --}}
        <div class="absolute top-[-60px] right-[-60px] w-72 h-72 rounded-full bg-blue-400/20 blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-[-40px] left-[-40px] w-56 h-56 rounded-full bg-blue-300/10 blur-[60px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center gap-4">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                المستندات القانونية
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                الشروط والأحكام
            </h1>
            <p class="font-arabic text-white/50 text-sm font-semibold">
                آخر تحديث: 22 سبتمبر 2025
            </p>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="rtl">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro Card --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    أهلاً بيك في <span class="text-primary font-black">MindFitBro</span>.
                    باستخدامك لموقعنا أو خدماتنا، بتوافق على الشروط والأحكام دي.
                    برجاء قراءتها بعناية قبل ما تبدأ في استخدام منصتنا.
                </p>
            </div>

            {{-- 1. Acceptance of Terms --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">handshake</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">القبول بالشروط</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    بمجرد دخولك على الموقع أو استخدامك لأي من خدماتنا، بتوافق تلقائياً على الالتزام بشروط الخدمة دي.
                    لو مش موافق على أي جزء من الشروط، برجاء عدم استخدام خدماتنا.
                </p>
            </div>

            {{-- 2. Use of Service --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">manage_accounts</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">استخدام الخدمة</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">بتوافق على الالتزام بالآتي عند استخدام خدماتنا:</p>
                <ul class="flex flex-col gap-3">
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <span><span class="font-black text-textColor">الاستخدام القانوني فقط:</span> عدم استخدام الخدمة لأي أغراض غير قانونية أو مخالفة لهذه الشروط.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <span><span class="font-black text-textColor">صحة المعلومات:</span> تقديم بيانات دقيقة وصحيحة عند التسجيل أو إجراء أي عملية على المنصة.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <span><span class="font-black text-textColor">حماية الحساب:</span> الحفاظ على سرية بيانات تسجيل الدخول الخاصة بيك وعدم مشاركتها مع أي طرف آخر.</span>
                    </li>
                </ul>
            </div>

            {{-- 3. Subscriptions & Payments --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">payments</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">الاشتراكات والمدفوعات</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">بخصوص الاشتراكات والمدفوعات، يرجى العلم بالتالي:</p>
                <ul class="flex flex-col gap-3">
                    @foreach([
                        'بعض الخدمات مدفوعة وتتطلب اشتراكاً نشطاً للوصول إليها.',
                        'الأسعار قابلة للتغيير مع إشعار مسبق للمستخدمين.',
                        'يتم تجديد الاشتراكات تلقائياً ما لم تقم بإلغائها قبل انتهاء الفترة الحالية.',
                        'المبالغ المدفوعة غير قابلة للاسترداد إلا في حالات استثنائية وفق سياسة الاسترداد الخاصة بنا.',
                    ] as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-accent flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 4. Intellectual Property --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">copyright</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">الملكية الفكرية</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    جميع المحتويات المنشورة على المنصة من نصوص وصور وشعارات وتصميمات هي ملك حصري لـ MindFitBro
                    ومحمية بموجب قوانين الملكية الفكرية. لا يُسمح بنسخ أي محتوى أو إعادة نشره أو استخدامه تجارياً
                    بدون إذن كتابي مسبق منا.
                </p>
            </div>

            {{-- 5 & 6: Prohibited Activities + Account Termination — Side by Side --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Prohibited Activities --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">block</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">الأنشطة المحظورة</h2>
                    </div>
                    <ul class="flex flex-col gap-2.5">
                        @foreach([
                            'نشر محتوى مسيء أو مضلل أو غير قانوني.',
                            'محاولة اختراق أو تعطيل المنصة.',
                            'انتحال شخصية أي مستخدم أو موظف.',
                        ] as $item)
                        <li class="flex items-start gap-2.5 text-sm text-gray-600 leading-relaxed">
                            <span class="material-symbols-rounded text-red-400 flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">cancel</span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Account Termination --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">person_off</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">إنهاء الحساب</h2>
                    </div>
                    <ul class="flex flex-col gap-2.5">
                        @foreach([
                            'يحق لنا إيقاف أو إنهاء حسابك عند مخالفة هذه الشروط.',
                            'يمكنك إلغاء حسابك في أي وقت من إعدادات الحساب.',
                            'بعض البيانات قد تُحتفظ بها وفق سياسة الخصوصية.',
                        ] as $item)
                        <li class="flex items-start gap-2.5 text-sm text-gray-600 leading-relaxed">
                            <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            {{-- 7. Disclaimer of Warranties --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">shield_question</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">إخلاء المسؤولية</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    نسعى دائماً لتقديم أفضل خدمة ممكنة، لكننا لا نضمن أن الخدمة ستكون خالية تماماً من الأخطاء أو الانقطاع.
                    إحنا مش مسؤولين عن أي خسائر مباشرة أو غير مباشرة ناتجة عن استخدام أو عدم القدرة على استخدام الخدمة.
                </p>
            </div>

            {{-- 8. Governing Law --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">gavel</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">القانون الحاكم</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    تخضع هذه الشروط وتُفسَّر وفقاً للقوانين المعمول بها في المملكة العربية السعودية.
                    أي نزاعات تنشأ عن استخدام الخدمة تخضع للاختصاص القضائي للمحاكم السعودية المختصة.
                </p>
            </div>

            {{-- 9. Changes to Terms --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">update</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">التعديلات على الشروط</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    محتفظين بحقنا في تعديل شروط الخدمة دي في أي وقت.
                    هنعلمك بأي تغييرات جوهرية عبر البريد الإلكتروني أو بإشعار واضح على الموقع.
                    استمرارك في استخدام الخدمة بعد نشر التعديلات يعني موافقتك عليها.
                </p>
            </div>

            {{-- 10. Contact Us --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex flex-col gap-5">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-textColor" style="font-size:20px">contact_support</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">تواصل معانا</h2>
                </div>

                <p class="text-gray-500 text-sm leading-relaxed">
                    لو عندك أي أسئلة بخصوص شروط الخدمة دي، تقدر تتواصل معانا:
                </p>

                <div class="flex flex-col gap-3">
                    <a href="mailto:info@mindfitbro.com"
                        class="group flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                        <div class="w-8 h-8 rounded-[8px] bg-white border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:border-primary/30 transition-colors">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">mail</span>
                        </div>
                        <span class="font-semibold">info@mindfitbro.com</span>
                    </a>
                    <a href="tel:+96650000000"
                        class="group flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                        <div class="w-8 h-8 rounded-[8px] bg-white border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:border-primary/30 transition-colors">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">call</span>
                        </div>
                        <span class="font-semibold" dir="ltr">+966 5x xxx xxxx</span>
                    </a>
                </div>

            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection