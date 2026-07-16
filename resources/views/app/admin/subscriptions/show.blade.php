@extends('layouts.admin.app')

@section('title', 'تفاصيل الاشتراك #' . $subscription->id)
@section('page-title', 'تفاصيل الاشتراك')
@section('page-subtitle', 'عرض كامل بيانات الاشتراك والجلسات المرتبطة')

@section('style')
<style>
    .card { background:#fff; border:1px solid #e8edf5; border-radius:20px; overflow:hidden; }
    .card-header { display:flex; align-items:center; gap:.85rem; padding:1rem 1.4rem; border-bottom:1px solid #f1f5f9; background:#fafbfd; }
    .card-icon { width:36px; height:36px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .card-body { padding:1.4rem; }

    .info-row { display:flex; justify-content:space-between; align-items:center; padding:.7rem 0; border-bottom:1px solid #f8fafc; font-size:.84rem; }
    .info-row:last-child { border-bottom:none; padding-bottom:0; }
    .info-label { color:#94a3b8; font-weight:700; font-size:.78rem; }
    .info-value { font-weight:700; color:#374151; text-align:left; }

    .stat-box { background:#f8fafc; border-radius:14px; padding:1rem; text-align:center; }

    .badge { display:inline-flex; align-items:center; gap:.3rem; padding:.28rem .8rem; border-radius:999px; font-size:.72rem; font-weight:800; white-space:nowrap; }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-yellow { background:#fefce8; color:#ca8a04; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-red    { background:#fee2e2; color:#dc2626; }
    .badge-blue   { background:#eff6ff; color:#2563eb; }
    .badge-violet { background:#f5f3ff; color:#7c3aed; }
    .badge-cyan   { background:#ecfeff; color:#0891b2; }
    .badge-orange { background:#fff7ed; color:#ea580c; }

    .admin-table { width:100%; border-collapse:collapse; }
    .admin-table th { padding:.6rem 1.1rem; text-align:right; font-size:.69rem; font-weight:900; color:#94a3b8; background:#f8fafc; border-bottom:1px solid #f0f4f8; white-space:nowrap; }
    .admin-table td { padding:.8rem 1.1rem; font-size:.83rem; font-weight:600; color:#374151; border-bottom:1px solid #f8fafc; vertical-align:middle; }
    .admin-table tbody tr:last-child td { border-bottom:none; }
    .admin-table tbody tr:hover td { background:#f8fafc; }

    .modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(3px); z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; display:flex; align-items:center; justify-content:center; padding:1rem; }
    .modal-backdrop.is-open { opacity:1; pointer-events:auto; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:460px; box-shadow:0 24px 60px rgba(0,0,0,.18); transform:translateY(16px) scale(.97); transition:transform .25s; overflow:hidden; }
    .modal-box.wide { max-width:560px; }
    .modal-backdrop.is-open .modal-box { transform:translateY(0) scale(1); }
    .modal-input { width:100%; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:.65rem .9rem; font-size:.85rem; font-weight:600; color:#1e293b; font-family:'Cairo',sans-serif; outline:none; transition:border-color .2s,box-shadow .2s; }
    .modal-input:focus { background:#fff; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    .modal-textarea { width:100%; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:.75rem .9rem; font-size:.85rem; font-weight:600; color:#1e293b; font-family:'Cairo',sans-serif; outline:none; transition:border-color .2s,box-shadow .2s; resize:vertical; min-height:110px; }
    .modal-textarea:focus { background:#fff; border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
    /* review panel */
    .review-panel { background:linear-gradient(135deg,#fffbe6 0%,#fff7ed 100%); border:2px solid #fde68a; border-radius:20px; overflow:hidden; }
    .review-panel-header { background:linear-gradient(90deg,#D97706,#B45309); padding:1rem 1.4rem; display:flex; align-items:center; gap:.75rem; }
    .receipt-frame { width:100%; height:340px; border:none; background:#f8fafc; }
    .receipt-img { width:100%; max-height:340px; object-fit:contain; background:#f8fafc; display:block; }
    .review-action-btn { flex:1; display:flex; align-items:center; justify-content:center; gap:.5rem; padding:.85rem 1rem; border:none; border-radius:14px; font-size:.9rem; font-weight:900; cursor:pointer; font-family:'Cairo',sans-serif; transition:opacity .2s,transform .15s; }
    .review-action-btn:hover { opacity:.9; transform:translateY(-1px); }
    .btn-approve { background:#16a34a; color:#fff; }
    .btn-reject  { background:#ef4444; color:#fff; }
</style>
@endsection

@section('content')

@php
    $statusMap = [
        'pending_review' => ['بانتظار المراجعة', 'badge-orange', 'pending'],
        'approved'       => ['موافق عليه',        'badge-blue',   'thumb_up'],
        'active'         => ['نشط',               'badge-green',  'check_circle'],
        'expired'        => ['منتهي',              'badge-gray',   'event_busy'],
        'rejected'       => ['مرفوض',             'badge-red',    'cancel'],
        'cancelled'      => ['ملغي',               'badge-red',    'cancel'],
        'waiting'        => ['في الانتظار',        'badge-yellow', 'schedule'],
    ];
    $bookingStatusMap = [
        'pending'   => ['معلّق',   'badge-yellow'],
        'confirmed' => ['مؤكد',   'badge-green'],
        'completed' => ['مكتمل',  'badge-blue'],
        'cancelled' => ['ملغي',   'badge-red'],
    ];
    $isGuest = is_null($subscription->user_id);
    $memberName  = $isGuest ? ($subscription->guest_name  ?: 'ضيف') : $subscription->user?->name;
    $memberEmail = $isGuest ? ($subscription->guest_email ?: '—')   : $subscription->user?->email;
    $st = $statusMap[$subscription->status] ?? ['غير معروف','badge-gray','help'];
@endphp

{{-- Flash --}}
@if(session('success'))
<div id="flashMsg" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">check_circle</span>
    {{ session('success') }}
    <button onclick="document.getElementById('flashMsg').remove()" class="mr-auto text-green-400 hover:text-green-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif
@if(session('error'))
<div id="flashErrMsg" class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">error</span>
    {{ session('error') }}
    <button onclick="document.getElementById('flashErrMsg').remove()" class="mr-auto text-red-400 hover:text-red-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-400">
    <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center gap-1.5 hover:text-blue-500 transition">
        <span class="material-symbols-rounded" style="font-size:17px">arrow_forward_ios</span>
        الاشتراكات
    </a>
    <span class="material-symbols-rounded" style="font-size:15px">chevron_left</span>
    <span class="text-slate-600">اشتراك #{{ $subscription->id }}</span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ════ Main Column ════ --}}
    <div class="xl:col-span-2 flex flex-col gap-5">

        {{-- ── Review Panel (pending_review only) ── --}}
        @if($subscription->status === 'pending_review')
        <div class="review-panel">
            <div class="review-panel-header">
                <span class="material-symbols-rounded text-white" style="font-size:20px;font-variation-settings:'FILL' 1">pending_actions</span>
                <div>
                    <p class="text-white font-black text-sm">بانتظار مراجعتك</p>
                    <p class="text-amber-100 text-[11px] font-semibold">تحقق من الإيصال واتخذ قراراً بالموافقة أو الرفض</p>
                </div>
                <a href="{{ route('admin.subscriptions.receipt', $subscription) }}"
                    target="_blank"
                    class="mr-auto flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white font-black text-xs px-3 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:14px">open_in_new</span>
                    فتح في تبويب
                </a>
            </div>

            {{-- Receipt inline preview --}}
            @php
                $receiptExt = $subscription->receipt_path
                    ? strtolower(pathinfo($subscription->receipt_path, PATHINFO_EXTENSION))
                    : null;
                $isPdf = $receiptExt === 'pdf';
            @endphp
            <div style="background:#f1f5f9;">
                @if($receiptExt && !$isPdf)
                <img src="{{ route('admin.subscriptions.receipt', $subscription) }}"
                     class="receipt-img" alt="إيصال الدفع">
                @elseif($isPdf)
                <iframe src="{{ route('admin.subscriptions.receipt', $subscription) }}"
                        class="receipt-frame" title="إيصال الدفع PDF"></iframe>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <span class="material-symbols-rounded mb-2" style="font-size:36px">image_not_supported</span>
                    <p class="text-sm font-bold">لا يوجد إيصال مرفق</p>
                </div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="p-5 flex gap-3">
                <button onclick="openModal('approveModal')" class="review-action-btn btn-approve">
                    <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                    موافقة على الاشتراك
                </button>
                <button onclick="openModal('rejectModal')" class="review-action-btn btn-reject">
                    <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">cancel</span>
                    رفض الطلب
                </button>
            </div>
        </div>
        @endif

        {{-- ── Reviewer info (approved/rejected) ── --}}
        @if(in_array($subscription->status, ['approved','rejected']) && $subscription->reviewed_at)
        @php $reviewer = $subscription->reviewer; @endphp
        <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-sm">
            <span class="material-symbols-rounded text-slate-400 flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">
                {{ $subscription->status === 'approved' ? 'verified' : 'block' }}
            </span>
            <span class="text-slate-500 font-semibold">
                تمت المراجعة بواسطة
                <strong class="text-slate-700">{{ $reviewer?->name ?? 'مدير' }}</strong>
                في {{ $subscription->reviewed_at->format('d/m/Y — H:i') }}
            </span>
        </div>
        @endif

        {{-- ── Hero: Summary ── --}}
        <div class="card">
            <div class="p-6">

                {{-- Top row --}}
                <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <span class="text-slate-400 font-black text-sm">#{{ $subscription->id }}</span>
                            <span class="badge {{ $st[1] }}">
                                <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">{{ $st[2] }}</span>
                                {{ $st[0] }}
                            </span>
                            @if($subscription->duration_months)
                            <span class="badge badge-violet">
                                <span class="material-symbols-rounded" style="font-size:11px">event_repeat</span>
                                {{ $subscription->duration_months === 3 ? '٣ شهور' : '٦ شهور' }}
                            </span>
                            @elseif($subscription->is_yearly)
                            <span class="badge badge-violet">
                                <span class="material-symbols-rounded" style="font-size:11px">event_repeat</span>
                                سنوي (قديم)
                            </span>
                            @else
                            <span class="badge badge-cyan">
                                <span class="material-symbols-rounded" style="font-size:11px">calendar_month</span>
                                شهري (قديم)
                            </span>
                            @endif
                        </div>
                        <p class="text-slate-400 text-xs font-bold">
                            تاريخ الطلب: {{ $subscription->created_at->format('d/m/Y — H:i') }}
                        </p>
                    </div>

                    <div class="flex gap-2 flex-wrap">
                        @if($subscription->receipt_path)
                        <a href="{{ route('admin.subscriptions.receipt', $subscription) }}"
                            target="_blank"
                            class="flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 font-black text-xs px-4 py-2.5 rounded-xl transition">
                            <span class="material-symbols-rounded" style="font-size:15px">receipt_long</span>
                            الإيصال
                        </a>
                        @endif
                        <button onclick="openEditModal()"
                            class="flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-black text-xs px-4 py-2.5 rounded-xl transition">
                            <span class="material-symbols-rounded" style="font-size:15px">edit_calendar</span>
                            تعديل
                        </button>
                        <button onclick="openDeleteModal()"
                            class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-500 font-black text-xs px-4 py-2.5 rounded-xl transition">
                            <span class="material-symbols-rounded" style="font-size:15px">delete</span>
                            حذف
                        </button>
                    </div>
                </div>

                @php
                    $showSubCur  = $subscription->currency ?? 'SAR';
                    $showSubMeta = ['SAR'=>['sym'=>'ر.س','dec'=>0],'EGP'=>['sym'=>'ج.م','dec'=>0],'TND'=>['sym'=>'د.ت','dec'=>3],'USD'=>['sym'=>'$','dec'=>2]][$showSubCur] ?? ['sym'=>$showSubCur,'dec'=>0];
                @endphp
                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="stat-box">
                        <p class="text-xl font-black text-slate-800" style="white-space:nowrap">
                            {{ number_format((float)$subscription->total, $showSubMeta['dec']) }}
                            <span class="text-xs font-bold text-slate-400">{{ $showSubMeta['sym'] }}</span>
                        </p>
                        <p class="text-[11px] text-slate-400 font-bold mt-1">المبلغ الكلي</p>
                    </div>
                    <div class="stat-box">
                        <p class="text-xl font-black text-slate-800">
                            {{ $subscription->start_date ? $subscription->start_date->format('d/m/Y') : '—' }}
                        </p>
                        <p class="text-[11px] text-slate-400 font-bold mt-1">تاريخ البدء</p>
                    </div>
                    <div class="stat-box">
                        <p class="text-xl font-black {{ $subscription->end_date?->isPast() && $subscription->status === 'active' ? 'text-red-500' : 'text-slate-800' }}">
                            {{ $subscription->end_date ? $subscription->end_date->format('d/m/Y') : '—' }}
                        </p>
                        <p class="text-[11px] text-slate-400 font-bold mt-1">تاريخ الانتهاء</p>
                    </div>
                </div>

                {{-- Financial breakdown --}}
                <div class="bg-slate-50 rounded-2xl p-4">
                    <p class="text-xs font-black text-slate-400 mb-3">تفاصيل الفاتورة</p>
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-semibold">السعر الأصلي</span>
                            <span class="font-black text-slate-700" style="direction:ltr">{{ number_format((float)$subscription->subtotal, $showSubMeta['dec']) }} {{ $showSubMeta['sym'] }}</span>
                        </div>
                        @if($subscription->yearly_discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-emerald-600 font-semibold">خصم الاشتراك السنوي</span>
                            <span class="font-black text-emerald-600" style="direction:ltr">- {{ number_format((float)$subscription->yearly_discount, $showSubMeta['dec']) }} {{ $showSubMeta['sym'] }}</span>
                        </div>
                        @endif
                        @if($subscription->coupon_discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-emerald-600 font-semibold">
                                خصم الكوبون
                                @if($subscription->coupon_code)
                                <span class="text-xs bg-emerald-100 text-emerald-700 font-black px-1.5 py-0.5 rounded-md">{{ $subscription->coupon_code }}</span>
                                @endif
                            </span>
                            <span class="font-black text-emerald-600" style="direction:ltr">- {{ number_format((float)$subscription->coupon_discount, $showSubMeta['dec']) }} {{ $showSubMeta['sym'] }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm pt-2 border-t border-slate-200 mt-1">
                            <span class="font-black text-slate-700">الإجمالي المدفوع</span>
                            <span class="font-black text-slate-800 text-base" style="direction:ltr">{{ number_format((float)$subscription->total, $showSubMeta['dec']) }} {{ $showSubMeta['sym'] }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Meeting Bookings ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon bg-blue-50">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:17px;font-variation-settings:'FILL' 1">video_call</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">جلسات الاجتماع</p>
                    <p class="text-[11px] text-slate-400 font-semibold">{{ $subscription->meetingBookings->count() }} جلسة مرتبطة</p>
                </div>
            </div>

            @if($subscription->meetingBookings->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-slate-300">
                <span class="material-symbols-rounded mb-2" style="font-size:36px;font-variation-settings:'FILL' 1">video_call</span>
                <p class="font-bold text-slate-400 text-sm">لا توجد جلسات بعد</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th class="hidden md:table-cell">رابط الاجتماع</th>
                            <th class="hidden sm:table-cell">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscription->meetingBookings->sortByDesc('meeting_date') as $booking)
                        @php [$bLabel,$bClass] = $bookingStatusMap[$booking->status] ?? [$booking->status,'badge-gray']; @endphp
                        <tr>
                            <td class="font-bold text-slate-700">
                                {{ $booking->meeting_date ? \Carbon\Carbon::parse($booking->meeting_date)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="font-bold text-slate-500" dir="ltr">
                                {{ $booking->meeting_time ? \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') : '—' }}
                            </td>
                            <td><span class="badge {{ $bClass }}">{{ $bLabel }}</span></td>
                            <td class="hidden md:table-cell">
                                @if($booking->meet_link)
                                <a href="{{ $booking->meet_link }}" target="_blank"
                                   class="text-blue-500 hover:text-blue-700 font-bold text-xs flex items-center gap-1 transition" dir="ltr">
                                    <span class="material-symbols-rounded" style="font-size:14px">open_in_new</span>
                                    meet.google.com
                                </a>
                                @else
                                <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="hidden sm:table-cell text-slate-400 text-xs max-w-[150px] truncate">
                                {{ $booking->notes ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Plan Snapshot ── --}}
        @if($subscription->plans_snapshot)
        <div class="card">
            <div class="card-header">
                <div class="card-icon bg-amber-50">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:17px;font-variation-settings:'FILL' 1">workspace_premium</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">لقطة الباقة وقت الشراء</p>
                    <p class="text-[11px] text-slate-400 font-semibold">البيانات المسجّلة عند إتمام الاشتراك</p>
                </div>
            </div>
            <div class="card-body">
                @php
                    $snapshot = $subscription->plans_snapshot;
                    // normalize: array of items → flatten to first item's fields
                    $snapFlat = isset($snapshot[0]) && is_array($snapshot[0]) ? $snapshot[0] : $snapshot;
                    $skipKeys = ['id','created_at','updated_at','style_variant','icon_bg','icon_color'];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($snapFlat as $key => $val)
                    @if(!in_array($key, $skipKeys) && !is_array($val))
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-[10px] font-black text-slate-400 mb-0.5 uppercase tracking-wide">{{ $key }}</p>
                        <p class="text-sm font-black text-slate-700 truncate">
                            {{ is_bool($val) ? ($val ? 'نعم' : 'لا') : ($val ?? '—') }}
                        </p>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ════ Sidebar ════ --}}
    <div class="flex flex-col gap-5">

        {{-- ── Member Info ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon {{ $isGuest ? 'bg-slate-100' : 'bg-blue-50' }}">
                    <span class="material-symbols-rounded {{ $isGuest ? 'text-slate-400' : 'text-blue-500' }}"
                          style="font-size:17px;font-variation-settings:'FILL' 1">
                        {{ $isGuest ? 'person_off' : 'person' }}
                    </span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">{{ $isGuest ? 'ضيف' : 'العضو' }}</p>
                    <p class="text-[11px] text-slate-400 font-semibold">
                        {{ $isGuest ? 'اشتراك بدون حساب' : 'عضو مسجّل' }}
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">الاسم</span>
                    <span class="info-value">{{ $memberName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد</span>
                    <span class="info-value text-xs" dir="ltr">{{ $memberEmail }}</span>
                </div>
                @if(!$isGuest && $subscription->user)
                <div class="info-row">
                    <span class="info-label">اسم المستخدم</span>
                    <span class="info-value" dir="ltr">@ {{ $subscription->user->username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الهاتف</span>
                    <span class="info-value text-xs" dir="ltr">{{ $subscription->user->phone ?: '—' }}</span>
                </div>
                @endif
                @if($isGuest && $subscription->guest_token)
                <div class="info-row">
                    <span class="info-label">رمز الضيف</span>
                    <span class="info-value text-[11px] font-mono text-slate-400 truncate max-w-[130px]" dir="ltr">{{ $subscription->guest_token }}</span>
                </div>
                @endif

                @if(!$isGuest && $subscription->user)
                <a href="{{ route('admin.members.show', $subscription->user) }}"
                   class="mt-3 flex items-center justify-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-black text-xs py-2.5 rounded-xl transition w-full">
                    <span class="material-symbols-rounded" style="font-size:15px">person</span>
                    عرض ملف العضو
                </a>
                @endif
            </div>
        </div>

        {{-- ── Plan Info ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon bg-amber-50">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:17px;font-variation-settings:'FILL' 1">workspace_premium</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">الباقة</p>
                    <p class="text-[11px] text-slate-400 font-semibold">تفاصيل الباقة المشترك فيها</p>
                </div>
            </div>
            <div class="card-body">
                @if($subscription->plan)
                <div class="info-row">
                    <span class="info-label">الاسم</span>
                    <span class="info-value font-black text-slate-800">{{ $subscription->plan->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">السعر الشهري</span>
                    <span class="info-value" dir="ltr">{{ number_format($subscription->plan->price, 0) }} ر.س</span>
                </div>
                <div class="info-row">
                    <span class="info-label">المدة</span>
                    <span class="info-value">
                        @if($subscription->duration_months)
                            {{ $subscription->duration_months }} شهور
                        @elseif($subscription->plan)
                            {{ $subscription->plan->duration_days ?? '—' }} يوم (قديم)
                        @else —
                        @endif
                    </span>
                </div>
                @else
                <p class="text-slate-400 text-sm font-semibold text-center py-3">الباقة محذوفة</p>
                @endif
            </div>
        </div>

        {{-- ── Rejection Reason (if rejected) ── --}}
        @if($subscription->status === 'rejected' && $subscription->rejection_reason)
        <div class="card">
            <div class="card-header">
                <div class="card-icon bg-red-50">
                    <span class="material-symbols-rounded text-red-500" style="font-size:17px;font-variation-settings:'FILL' 1">report</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">سبب الرفض</p>
                </div>
            </div>
            <div class="card-body">
                <p class="text-sm text-slate-600 font-semibold leading-relaxed">{{ $subscription->rejection_reason }}</p>
            </div>
        </div>
        @endif

        {{-- ── Quick Status Change ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon bg-slate-100">
                    <span class="material-symbols-rounded text-slate-500" style="font-size:17px;font-variation-settings:'FILL' 1">tune</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">تغيير الحالة بسرعة</p>
                </div>
            </div>
            <div class="card-body flex flex-col gap-2">
                @foreach(['pending_review'=>['بانتظار المراجعة','pending','bg-orange-400 hover:bg-orange-500'],'approved'=>['موافقة','thumb_up','bg-blue-500 hover:bg-blue-600'],'active'=>['تفعيل','check_circle','bg-green-500 hover:bg-green-600'],'expired'=>['إنهاء','event_busy','bg-slate-400 hover:bg-slate-500'],'rejected'=>['رفض','cancel','bg-red-500 hover:bg-red-600'],'cancelled'=>['إلغاء','cancel','bg-red-700 hover:bg-red-800'],'waiting'=>['انتظار (قديم)','schedule','bg-yellow-400 hover:bg-yellow-500']] as $s => [$label,$icon,$cls])
                @if($subscription->status !== $s)
                <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="{{ $s }}">
                    <input type="hidden" name="start_date" value="{{ $subscription->start_date?->format('Y-m-d') }}">
                    <input type="hidden" name="end_date"   value="{{ $subscription->end_date?->format('Y-m-d') }}">
                    <button type="submit"
                        class="w-full flex items-center gap-2 {{ $cls }} text-white font-black text-xs py-2.5 px-4 rounded-xl transition">
                        <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1">{{ $icon }}</span>
                        {{ $label }}
                    </button>
                </form>
                @endif
                @endforeach
            </div>
        </div>

    </div>

</div>

{{-- ══ APPROVE MODAL ══ --}}
<div id="approveModal" class="modal-backdrop" onclick="closeModal('approveModal')">
    <div class="modal-box wide" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-green-500" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                </div>
                <p class="font-black text-slate-800 text-sm">تأكيد الموافقة على الاشتراك</p>
            </div>
            <button onclick="closeModal('approveModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.subscriptions.approve', $subscription) }}" class="p-6 flex flex-col gap-4">
            @csrf
            {{-- Member info --}}
            <div class="flex items-center gap-3 bg-slate-50 rounded-2xl px-4 py-3.5 border border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:18px;font-variation-settings:'FILL' 1">person</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm leading-tight">
                        {{ $subscription->user?->name ?? $subscription->guest_name ?? 'ضيف' }}
                    </p>
                    <p class="text-[11px] text-slate-400 font-semibold" dir="ltr">
                        {{ $subscription->user?->email ?? $subscription->guest_email ?? '—' }}
                    </p>
                </div>
                <span class="mr-auto text-[11px] font-black px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                    {{ $subscription->plan?->name ?? '—' }}
                </span>
            </div>

            {{-- Info notice --}}
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3.5">
                <span class="material-symbols-rounded text-green-500 flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">mark_email_read</span>
                <p class="text-sm text-green-700 font-semibold leading-relaxed">
                    سيُرسَل إيميل تأكيد للعميل تلقائياً بعد الموافقة.<br>
                    <span class="text-[11px] text-green-600 font-bold">تواريخ البدء والانتهاء ستُحدَّد لاحقاً عند حجز الجلسة الأولى.</span>
                </p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex-1 flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-black text-sm py-2.5 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                    موافقة وإرسال الإيميل
                </button>
                <button type="button" onclick="closeModal('approveModal')"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ REJECT MODAL ══ --}}
<div id="rejectModal" class="modal-backdrop" onclick="closeModal('rejectModal')">
    <div class="modal-box wide" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:16px;font-variation-settings:'FILL' 1">cancel</span>
                </div>
                <p class="font-black text-slate-800 text-sm">رفض الطلب</p>
            </div>
            <button onclick="closeModal('rejectModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.subscriptions.reject', $subscription) }}" class="p-6 flex flex-col gap-4">
            @csrf
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 font-semibold">
                سيُرسَل إيميل إشعار بالرفض مع السبب للعميل تلقائياً.
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">سبب الرفض <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" class="modal-textarea"
                    placeholder="مثال: الإيصال غير واضح، يرجى إعادة رفع صورة واضحة..."
                    required minlength="5"></textarea>
                <p class="text-[11px] text-slate-400 font-semibold">سيظهر هذا السبب في الإيميل المُرسَل للعميل.</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex-1 flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white font-black text-sm py-2.5 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">cancel</span>
                    رفض وإرسال الإيميل
                </button>
                <button type="button" onclick="closeModal('rejectModal')"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ EDIT MODAL ══ --}}
<div id="editModal" class="modal-backdrop" onclick="closeModal('editModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:16px;font-variation-settings:'FILL' 1">edit_calendar</span>
                </div>
                <p class="font-black text-slate-800 text-sm">تعديل الاشتراك</p>
            </div>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="p-6 flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">حالة الاشتراك</label>
                <select name="status" class="modal-input">
                    @foreach(['pending_review'=>'بانتظار المراجعة','approved'=>'موافق عليه','active'=>'نشط','expired'=>'منتهي','rejected'=>'مرفوض','cancelled'=>'ملغي','waiting'=>'في الانتظار (قديم)'] as $s => $l)
                    <option value="{{ $s }}" {{ $subscription->status === $s ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-slate-500">تاريخ البدء</label>
                    <input type="date" name="start_date" class="modal-input" dir="ltr"
                           value="{{ $subscription->start_date?->format('Y-m-d') }}">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-slate-500">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" class="modal-input" dir="ltr"
                           value="{{ $subscription->end_date?->format('Y-m-d') }}">
                </div>
            </div>
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                @foreach($errors->all() as $e)<p class="text-red-600 text-xs font-bold">• {{ $e }}</p>@endforeach
            </div>
            @endif
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">حفظ</button>
                <button type="button" onclick="closeModal('editModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ DELETE MODAL ══ --}}
<div id="deleteModal" class="modal-backdrop" onclick="closeModal('deleteModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:16px;font-variation-settings:'FILL' 1">delete_forever</span>
                </div>
                <p class="font-black text-slate-800 text-sm">حذف الاشتراك</p>
            </div>
            <button onclick="closeModal('deleteModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                هل أنت متأكد من حذف اشتراك <span class="font-black text-slate-800">{{ $memberName }}</span>؟
                <br><span class="text-red-400 text-xs font-bold"><span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1;vertical-align:middle">warning</span> سيتم حذف جميع الجلسات المرتبطة أيضاً.</span>
            </p>
        </div>
        <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" class="px-6 pb-6 flex gap-3">
            @csrf @method('DELETE')
            <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black text-sm py-2.5 rounded-xl transition">نعم، احذف</button>
            <button type="button" onclick="closeModal('deleteModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    function closeModal(id) { document.getElementById(id).classList.remove('is-open'); document.body.style.overflow = ''; }
    function openModal(id)  { document.getElementById(id).classList.add('is-open');    document.body.style.overflow = 'hidden'; }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') ['approveModal','rejectModal','editModal','deleteModal'].forEach(closeModal);
    });

    function openEditModal()   { openModal('editModal'); }
    function openDeleteModal() { openModal('deleteModal'); }

    @if($errors->any()) openEditModal(); @endif

    // ── Auto-calculate end date when start date changes ──────────
    const durationMonths = {{ (int) ($subscription->duration_months ?? 3) }};
    const startInput = document.getElementById('appStartDate');
    const endInput   = document.getElementById('appEndDate');

    if (startInput && endInput) {
        startInput.addEventListener('change', () => {
            if (!startInput.value) return;
            const start = new Date(startInput.value);
            start.setMonth(start.getMonth() + durationMonths);
            // Format as Y-m-d
            const y = start.getFullYear();
            const m = String(start.getMonth() + 1).padStart(2, '0');
            const d = String(start.getDate()).padStart(2, '0');
            endInput.value = `${y}-${m}-${d}`;
        });
    }
</script>
@endsection
