{{-- 
    ╔══════════════════════════════════════════════════╗
    ║  صفحة إدارة الحجوزات — Coach Bookings Page     ║
    ║  MindFitBro Dashboard                           ║
    ╚══════════════════════════════════════════════════╝
--}}

@extends('layouts.web.app')

@section('title', 'إدارة الحجوزات')

@php
    $coach = auth()->user();
@endphp

@section('style')
    <style>
        .dash-wrap {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            background: #F0F4FB;
        }

        @media (max-width: 1024px) {
            .dash-wrap { grid-template-columns: 1fr; }
            .dash-sidebar { display: none; }
            .dash-sidebar.open { display: flex; position: fixed; z-index: 50; inset: 0; }
        }

        .dash-sidebar {
            background: #0f2d6b;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.2rem;
            gap: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .7rem 1rem;
            border-radius: 14px;
            font-family: 'Cairo', sans-serif;
            font-size: .85rem;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all .22s;
            text-decoration: none;
            direction: rtl;
        }

        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active {
            background: rgba(212,237,87,0.12);
            color: #D4ED57;
            border: 1px solid rgba(212,237,87,0.2);
        }

        .nav-item.active .nav-icon { color: #D4ED57; }
        .nav-icon { color: rgba(255,255,255,0.35); transition: color .22s; font-size: 20px; }

        .card {
            background: #fff;
            border-radius: 22px;
            padding: 1.5rem;
            border: 1px solid rgba(23,77,173,.06);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim { animation: fadeUp .5s ease both; }
        .anim-1 { animation-delay: .05s; }
        .anim-2 { animation-delay: .12s; }
        .anim-3 { animation-delay: .19s; }
        .anim-4 { animation-delay: .26s; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .spinner {
            width: 28px;
            height: 28px;
            border: 3px solid #e5e7eb;
            border-top-color: #174DAD;
            border-radius: 999px;
            animation: spin .7s linear infinite;
        }

        /* Loading overlay */
        .bookings-loading {
            position: relative;
        }
        .bookings-loading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(240, 244, 251, 0.6);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* Smooth transition for cards */
        .booking-card {
            transition: all 0.3s ease;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            font-family: 'Cairo', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 9999;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
        }
        .toast-success {
            background: #065F46;
            color: #fff;
        }
        .toast-error {
            background: #991B1B;
            color: #fff;
        }
    </style>
@endsection

@section('content')
<div class="dash-wrap">

    <x-web.sidebar :coach="$coach"/>

    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- Topbar --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-primary transition">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">arrow_forward</span>
                    </a>
                    <p class="text-gray-400 text-sm font-bold">
                        {{ now()->locale('ar')->isoFormat('dddd، D MMMM Y') }}
                    </p>
                </div>

                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black mt-5 flex justify-center items-center gap-2">
                    إدارة الحجوزات
                    <span class="material-symbols-rounded text-4xl">calendar_month</span>
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                        onclick="document.querySelector('.dash-sidebar').classList.toggle('open')">
                    <span class="material-symbols-rounded text-textColor"
                          style="font-size:20px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">menu</span>
                </button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 anim anim-1" id="stats-grid">

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary"
                          style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">event_note</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">إجمالي الحجوزات</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none" id="stat-total">{{ $bookings->total() }}</p>
                </div>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-amber-500"
                          style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">pending_actions</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">في الانتظار</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none" id="stat-pending">{{ $counts['pending'] ?? 0 }}</p>
                </div>
                @if(($counts['pending'] ?? 0) > 0)
                    <span id="pending-badge" class="text-[10px] font-black font-arabic text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full self-start animate-pulse">
                        يحتاج مراجعة
                    </span>
                @endif
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-green-600"
                          style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">مؤكدة</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none" id="stat-confirmed">{{ $counts['confirmed'] ?? 0 }}</p>
                </div>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500"
                          style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">ملغية / مرفوضة</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none" id="stat-cancelled">{{ ($counts['cancelled'] ?? 0) + ($counts['rejected'] ?? 0) }}</p>
                </div>
            </div>

        </div>

        {{-- Filters --}}
        <div class="card anim anim-2" style="direction:rtl">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">

                @php
                    $tabConfig = [
                        ''          => ['label' => 'الكل',        'color' => 'primary'],
                        'pending'   => ['label' => 'في الانتظار', 'color' => 'amber-600'],
                        'confirmed' => ['label' => 'مؤكدة',       'color' => 'green-600'],
                        'completed' => ['label' => 'مكتملة',      'color' => 'blue-600'],
                        'cancelled' => ['label' => 'ملغية',       'color' => 'red-500'],
                    ];
                    $currentStatus = request('status', '');
                @endphp

                <div class="flex items-center gap-1 bg-gray-50 rounded-xl p-1 font-arabic flex-shrink-0">
                    @foreach($tabConfig as $value => $config)
                        @php
                            $isActive = $currentStatus === $value;
                        @endphp
                        <button
                            type="button"
                            class="filter-tab px-3.5 py-2 rounded-lg text-xs font-black transition font-arabic
                                {{ $isActive ? "bg-white text-{$config['color']} shadow-sm" : 'text-gray-400 hover:text-textColor' }}"
                            data-status="{{ $value }}"
                            data-color="{{ $config['color'] }}"
                        >
                            {{ $config['label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="flex-1 w-full sm:w-auto relative">
                    <span class="material-symbols-rounded absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                        style="font-size:18px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">search</span>
                    <input
                        id="searchInput"
                        type="text"
                        value="{{ request('search') }}"
                        placeholder="ابحث باسم العميل..."
                        class="w-full bg-gray-50 border border-gray-200 focus:border-primary rounded-xl pr-10 pl-4 py-2.5 text-sm font-arabic outline-none transition-colors"
                    >
                </div>

            </div>
        </div>

        {{-- Bookings List --}}
        <div class="flex flex-col gap-3 anim anim-3" id="bookings-list">

            @forelse($bookings as $booking)
                @php
                    $client      = $booking->subscription->user ?? null;
                    $plan        = $booking->subscription->plan ?? null;
                    $meetingDate = \Carbon\Carbon::parse($booking->meeting_date);
                    $meetingTime = \Carbon\Carbon::parse($booking->meeting_time);
                    $isToday     = $meetingDate->isToday();
                    $isPast      = $meetingDate->isPast() && !$isToday;

                    $statusMap = [
                        'pending'   => ['label' => 'في الانتظار', 'icon' => 'schedule',     'bg' => 'bg-amber-50',  'text' => 'text-amber-600',  'border' => 'border-amber-200',  'dot' => 'bg-amber-400'],
                        'confirmed' => ['label' => 'مؤكد',        'icon' => 'check_circle',  'bg' => 'bg-green-50',  'text' => 'text-green-600',  'border' => 'border-green-200',  'dot' => 'bg-green-400'],
                        'completed' => ['label' => 'مكتمل',       'icon' => 'verified',      'bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'border' => 'border-blue-200',   'dot' => 'bg-blue-400'],
                        'cancelled' => ['label' => 'ملغي',        'icon' => 'cancel',        'bg' => 'bg-red-50',    'text' => 'text-red-500',    'border' => 'border-red-200',    'dot' => 'bg-red-400'],
                        'rejected'  => ['label' => 'مرفوض',       'icon' => 'block',         'bg' => 'bg-red-50',    'text' => 'text-red-500',    'border' => 'border-red-200',    'dot' => 'bg-red-400'],
                    ];
                    $sc = $statusMap[$booking->status] ?? ['label' => $booking->status, 'icon' => 'help', 'bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'];
                @endphp

                <div class="card booking-card group hover:shadow-md transition-shadow" style="direction:rtl" data-booking-id="{{ $booking->id }}">
                    <div class="flex flex-col gap-4">

                        {{-- Row 1 --}}
                        <div class="flex items-start sm:items-center gap-4">

                            <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black font-arabic text-sm flex-shrink-0 relative {{ $sc['bg'] }} {{ $sc['text'] }}">
                                {{ $client ? mb_substr($client->name, 0, 1) : '?' }}
                                <div class="absolute -bottom-0.5 -left-0.5 w-3 h-3 rounded-full border-2 border-white {{ $sc['dot'] }}"></div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <p class="font-black text-textColor text-base leading-none font-arabic">
                                        {{ $client->name ?? '—' }}
                                    </p>

                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full border flex items-center gap-1 font-arabic {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                        <span class="material-symbols-rounded"
                                              style="font-size:11px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">{{ $sc['icon'] }}</span>
                                        {{ $sc['label'] }}
                                    </span>

                                    @if($isToday && !in_array($booking->status, ['cancelled', 'rejected']))
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20 animate-pulse">
                                            اليوم!
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-gray-400 text-xs font-bold">
                                    @if($plan)
                                        <span class="flex items-center gap-1 font-arabic">
                                            <span class="material-symbols-rounded"
                                                  style="font-size:13px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">{{ $plan->icon ?? 'star' }}</span>
                                            {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                                        </span>
                                    @endif

                                    @if($client?->email)
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-rounded"
                                                  style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">mail</span>
                                            {{ $client->email }}
                                        </span>
                                    @endif

                                    @if($client?->phone)
                                        <span class="flex items-center gap-1 direction-ltr">
                                            <span class="material-symbols-rounded"
                                                  style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">phone</span>
                                            {{ $client->phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">

                                @if($booking->status === 'pending')
                                    <button type="button"
                                            class="btn-confirm flex items-center gap-1.5 text-[11px] font-black font-arabic text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded-xl hover:bg-green-100 transition"
                                            data-action="{{ route('coach.bookings.confirm', $booking) }}"
                                            data-booking-id="{{ $booking->id }}">
                                        <span class="material-symbols-rounded"
                                              style="font-size:15px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                                        <span class="hidden sm:inline">تأكيد</span>
                                    </button>

                                    <button type="button"
                                            class="btn-reject flex items-center gap-1.5 text-[11px] font-black font-arabic text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-xl hover:bg-red-100 transition"
                                            data-action="{{ route('coach.bookings.reject', $booking) }}"
                                            data-booking-id="{{ $booking->id }}">
                                        <span class="material-symbols-rounded"
                                              style="font-size:15px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                                        <span class="hidden sm:inline">رفض</span>
                                    </button>
                                @endif

                                @if($booking->status === 'confirmed' && $booking->meet_link)
                                    <a href="{{ $booking->meet_link }}" target="_blank" rel="noopener noreferrer"
                                       class="flex items-center gap-1.5 text-[11px] font-black font-arabic text-white bg-primary border border-primary px-3 py-2 rounded-xl hover:bg-primary/90 transition">
                                        <span class="material-symbols-rounded"
                                              style="font-size:15px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">videocam</span>
                                        <span class="hidden sm:inline">انضم للجلسة</span>
                                    </a>
                                @endif

                            </div>
                        </div>

                        {{-- Row 2 --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 pt-3 border-t border-gray-100">

                            <div class="flex items-center gap-2 border rounded-xl px-3 py-2 {{ $isToday ? 'bg-primary/5 border-primary/20' : 'bg-gray-50 border-gray-100' }}">
                                <span class="material-symbols-rounded {{ $isToday ? 'text-primary' : 'text-gray-400' }}"
                                      style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">calendar_today</span>
                                <span class="text-xs font-black font-arabic {{ $isToday ? 'text-primary' : 'text-textColor' }}">
                                    {{ $meetingDate->locale('ar')->isoFormat('dddd، D MMMM Y') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2">
                                <span class="material-symbols-rounded text-gray-400"
                                      style="font-size:16px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">schedule</span>
                                <span class="text-xs font-black font-arabic text-textColor">
                                    {{ $meetingTime->format('h:i A') }}
                                </span>
                            </div>

                            @if($isPast)
                                <span class="text-[10px] font-bold font-arabic text-gray-400">
                                    {{ $meetingDate->locale('ar')->diffForHumans() }}
                                </span>
                            @endif

                            {{-- Meet Link Form --}}
                            @if($booking->status === 'pending')
                                <div class="flex-1 sm:mr-auto flex items-center gap-2">
                                    <span class="material-symbols-rounded text-blue-500 flex-shrink-0"
                                          style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">videocam</span>

                                    <input type="url"
                                           id="meet-link-{{ $booking->id }}"
                                           value="{{ $booking->meet_link ?? '' }}"
                                           placeholder="https://meet.google.com/xxx-xxxx-xxx"
                                           class="flex-1 text-xs font-arabic bg-gray-50 border border-gray-200 focus:border-primary rounded-lg px-3 py-1.5 outline-none transition-colors min-w-0">

                                    <button type="button"
                                            class="btn-save-link flex-shrink-0 text-[11px] font-black font-arabic text-primary bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition"
                                            data-action="{{ route('coach.bookings.meet-link', $booking) }}"
                                            data-booking-id="{{ $booking->id }}">
                                        حفظ
                                    </button>
                                </div>
                            @endif

                            @if($booking->meet_link && !in_array($booking->status, ['pending', 'cancelled', 'rejected']))
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-blue-400"
                                          style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">link</span>
                                    <a href="{{ $booking->meet_link }}" target="_blank" rel="noopener noreferrer"
                                       class="text-[11px] font-bold text-blue-500 hover:underline font-arabic truncate max-w-[200px]">
                                        {{ $booking->meet_link }}
                                    </a>
                                </div>
                            @endif

                        </div>

                        @if($booking->notes)
                            <div class="flex items-start gap-2 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                <span class="material-symbols-rounded text-gray-400 flex-shrink-0 mt-0.5"
                                      style="font-size:14px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">sticky_note_2</span>
                                <p class="text-xs font-bold font-arabic text-gray-500 leading-relaxed">
                                    {{ $booking->notes }}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

            @empty
                <div class="card">
                    <div class="flex flex-col items-center justify-center py-16 gap-4 font-arabic text-center">
                        <div class="w-20 h-20 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center">
                            <span class="material-symbols-rounded text-gray-300"
                                  style="font-size:40px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48">event_busy</span>
                        </div>
                        <div>
                            <h3 class="font-black text-textColor text-lg mb-1">مفيش حجوزات</h3>
                            <p class="text-gray-400 text-sm font-bold">
                                @if(request('status'))
                                    مفيش حجوزات بالفلتر ده دلوقتي
                                @elseif(request('search'))
                                    مفيش نتائج لـ "{{ request('search') }}"
                                @else
                                    مفيش حجوزات لسه
                                @endif
                            </p>
                        </div>

                        @if(request('status') || request('search'))
                            <button type="button" onclick="clearFilters()"
                               class="flex items-center gap-2 text-sm font-black text-primary hover:underline font-arabic">
                                <span class="material-symbols-rounded"
                                      style="font-size:16px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">filter_alt_off</span>
                                مسح الفلتر
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div id="pagination-wrap" class="flex items-center justify-center gap-1 font-arabic anim anim-4" style="direction:rtl">
            @if($bookings->hasPages())
                {{ $bookings->appends(request()->except('page'))->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>

</div>

{{-- Toast Container --}}
<div id="toast" class="toast" aria-live="polite"></div>

{{-- Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/30 backdrop-blur-sm" style="direction:rtl">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl font-arabic">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-rounded text-red-500" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">warning</span>
            </div>
            <div>
                <h3 class="font-black text-textColor text-base" id="modalTitle">تأكيد</h3>
                <p class="text-gray-400 text-xs font-bold" id="modalMessage">هل أنت متأكد؟</p>
            </div>
        </div>
        <div class="flex items-center gap-2 justify-end">
            <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-black text-gray-500 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
                إلغاء
            </button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-xl text-xs font-black text-white bg-red-500 border border-red-600 hover:bg-red-600 transition">
                تأكيد
            </button>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        // ─── Config ─────────────────────────────────────────────────
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const bookingsList = document.getElementById('bookings-list');
        const paginationWrap = document.getElementById('pagination-wrap');

        // ─── Toast Notifications ────────────────────────────────────
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast toast-${type} show`;
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // ─── Confirm Modal ──────────────────────────────────────────
        let modalCallback = null;

        function openModal(title, message, callback) {
            const modal = document.getElementById('confirmModal');
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            modalCallback = callback;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalCallback = null;
        }

        document.getElementById('modalConfirmBtn').addEventListener('click', () => {
            if (modalCallback) modalCallback();
            closeModal();
        });

        // Close modal on backdrop click
        document.getElementById('confirmModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) closeModal();
        });

        // ─── AJAX Fetch Bookings ────────────────────────────────────
        let currentController = null;

        function fetchBookings(params) {
            // Cancel any in-flight request
            if (currentController) currentController.abort();
            currentController = new AbortController();

            const url = new URL(window.location);

            Object.entries(params).forEach(([key, val]) => {
                if (val) {
                    url.searchParams.set(key, val);
                } else {
                    url.searchParams.delete(key);
                }
            });
            url.searchParams.delete('page');

            window.history.replaceState({}, '', url.toString());

            // Loading state
            bookingsList.classList.add('bookings-loading');
            bookingsList.style.opacity = '0.5';
            bookingsList.style.pointerEvents = 'none';

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: currentController.signal,
            })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newList = doc.getElementById('bookings-list');
                const newPagination = doc.getElementById('pagination-wrap');
                const newStatsGrid = doc.getElementById('stats-grid');

                if (newList) bookingsList.innerHTML = newList.innerHTML;
                if (newPagination) paginationWrap.innerHTML = newPagination.innerHTML;
                if (newStatsGrid) {
                    document.getElementById('stats-grid').innerHTML = newStatsGrid.innerHTML;
                }

                bookingsList.classList.remove('bookings-loading');
                bookingsList.style.opacity = '1';
                bookingsList.style.pointerEvents = '';

                // Re-bind action buttons on new content
                bindActionButtons();
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.error('Fetch error:', err);
                bookingsList.classList.remove('bookings-loading');
                bookingsList.style.opacity = '1';
                bookingsList.style.pointerEvents = '';
                showToast('حصل خطأ في تحميل البيانات', 'error');
            });
        }

        // ─── Clear Filters ──────────────────────────────────────────
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            // Reset tabs
            document.querySelectorAll('.filter-tab').forEach(t => {
                t.className = 'filter-tab px-3.5 py-2 rounded-lg text-xs font-black transition font-arabic text-gray-400 hover:text-textColor';
            });
            const allTab = document.querySelector('.filter-tab[data-status=""]');
            if (allTab) {
                allTab.classList.remove('text-gray-400', 'hover:text-textColor');
                allTab.classList.add('bg-white', 'text-primary', 'shadow-sm');
            }
            fetchBookings({ status: '', search: '' });
        }

        // ─── Tabs ────────────────────────────────────────────────────
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // Reset all tabs
                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.className = 'filter-tab px-3.5 py-2 rounded-lg text-xs font-black transition font-arabic text-gray-400 hover:text-textColor';
                });

                // Activate clicked tab
                const color = this.dataset.color;
                this.classList.remove('text-gray-400', 'hover:text-textColor');
                this.classList.add('bg-white', `text-${color}`, 'shadow-sm');

                fetchBookings({ status: this.dataset.status });
            });
        });

        // ─── Search ──────────────────────────────────────────────────
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchBookings({ search: this.value });
            }, 500);
        });

        // ─── AJAX Action Helper ─────────────────────────────────────
        async function submitAction(url, method = 'PATCH', body = {}) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: method,
                        ...body,
                    }),
                });

                const data = await res.json();

                if (res.ok) {
                    showToast(data.message || 'تم بنجاح');
                    // Refresh the list
                    fetchBookings({});
                } else {
                    showToast(data.message || 'حصل خطأ', 'error');
                }
            } catch (err) {
                console.error('Action error:', err);
                showToast('حصل خطأ في الاتصال', 'error');
            }
        }

        // ─── Bind Action Buttons ────────────────────────────────────
        function bindActionButtons() {
            // Confirm buttons
            document.querySelectorAll('.btn-confirm').forEach(btn => {
                btn.addEventListener('click', function () {
                    const url = this.dataset.action;
                    submitAction(url, 'PATCH');
                });
            });

            // Reject buttons
            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', function () {
                    const url = this.dataset.action;
                    openModal(
                        'رفض الحجز',
                        'هل أنت متأكد من رفض هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
                        () => submitAction(url, 'PATCH')
                    );
                });
            });

            // Save meet link buttons
            document.querySelectorAll('.btn-save-link').forEach(btn => {
                btn.addEventListener('click', function () {
                    const bookingId = this.dataset.bookingId;
                    const input = document.getElementById(`meet-link-${bookingId}`);
                    const meetLink = input?.value?.trim();

                    if (!meetLink) {
                        showToast('أدخل لينك الميتنج الأول', 'error');
                        input?.focus();
                        return;
                    }

                    // Basic URL validation
                    try {
                        new URL(meetLink);
                    } catch {
                        showToast('اللينك مش صحيح', 'error');
                        input?.focus();
                        return;
                    }

                    submitAction(this.dataset.action, 'PATCH', { meet_link: meetLink });
                });
            });
        }

        // Initial bind
        bindActionButtons();

        // ─── Keyboard shortcut: Escape to close modal ────────────────
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
@endsection