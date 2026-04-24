@props([
    'coach'          => null,
])

<aside class="dash-sidebar" :class="{ open: sideOpen }">

    {{-- Logo --}}
    <div class="flex items-center justify-between mb-8 px-1">
        <img src="{{ asset('assets/logo/mindfitbro.png') }}" class="w-32" alt="MindFitBro Logo">
        <button class="lg:hidden text-white/50 hover:text-white z-50" @click="sideOpen = false">
            <span class="material-symbols-rounded">close</span>
        </button>
    </div>

    {{-- Avatar --}}
    <div class="flex items-center gap-3 px-1 mb-8 pb-6 border-b border-white/10">
        <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-black text-textColor font-arabic text-sm flex-shrink-0">
            {{ mb_substr($coach->name, 0, 1) }}
        </div>
        <div>
            <p class="text-white font-black text-lg font-display leading-none">{{ $coach->name }}</p>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-accent/20 text-accent mt-1 inline-block font-arabic">
                كوتش
            </span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex flex-col gap-1 flex-1">
        <a href="{{ route('home') }}" class="nav-item">
            <span class="material-symbols-rounded nav-icon"
                    style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">home</span>
            الصفحة الرئيسية
        </a>
        <a href="{{ route('dashboard') }}" class="nav-item">
            <span class="material-symbols-rounded nav-icon"
                    style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">dashboard</span>
            نظرة عامة
        </a>
        <a href="{{ route('coach.bookings') }}" class="nav-item active">
            <span class="material-symbols-rounded nav-icon"
                    style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">calendar_month</span>
            الحجوزات
        </a>
        <a href="#" class="nav-item">
            <span class="material-symbols-rounded nav-icon"
                    style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">group</span>
            المشتركين
        </a>
        <a href="#" class="nav-item">
            <span class="material-symbols-rounded nav-icon"
                    style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">settings</span>
            الإعدادات
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-right">
                <span class="material-symbols-rounded nav-icon"
                        style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">logout</span>
                تسجيل الخروج
            </button>
        </form>
    </nav>

    {{-- Bottom badge --}}
    <div class="mt-auto pt-4 border-t border-white/10">
        <div class="rounded-2xl bg-accent/10 border border-accent/20 p-3 font-arabic text-right">
            <p class="text-accent text-xs font-black mb-0.5">لوحة تحكم الكوتش</p>
            <p class="text-white/50 text-[10px]">{{ now()->isoFormat('D MMMM Y') }}</p>
        </div>
    </div>

</aside>