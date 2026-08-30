@extends('layouts.admin.app')

@section('title', 'أسعار صرف العملات')
@section('page-title', 'أسعار صرف العملات')
@section('page-subtitle', 'السعر المستخدم لتحويل مبلغ الطلب إلى الجنيه المصري عند الدفع عبر Paymob')

@section('style')
<style>
.card { background:#fff; border:1px solid #e8edf5; border-radius:20px; overflow:hidden; }
.card-header { display:flex; align-items:center; justify-content:space-between; gap:.85rem; padding:1rem 1.4rem; border-bottom:1px solid #f1f5f9; background:#fafbfd; }
.card-header h3 { font-size:.92rem; font-weight:900; color:#1e293b; }
table.fx-table { width:100%; border-collapse:collapse; font-size:.85rem; }
table.fx-table th { text-align:right; padding:.85rem 1.2rem; background:#fafbfd; color:#64748b; font-weight:800; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #f1f5f9; }
table.fx-table td { padding:.9rem 1.2rem; border-bottom:1px solid #f1f5f9; color:#1e293b; font-weight:700; }
table.fx-table tr:last-child td { border-bottom:none; }
.badge { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .75rem; border-radius:999px; font-size:.72rem; font-weight:800; white-space:nowrap; }
.badge-green  { background:#dcfce7; color:#16a34a; }
.badge-amber  { background:#fff7ed; color:#ea580c; }
.badge-red    { background:#fee2e2; color:#dc2626; }
.badge-gray   { background:#f1f5f9; color:#64748b; }
.btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; padding:.6rem 1.3rem; border-radius:12px; font-size:.85rem; font-weight:700; border:none; cursor:pointer; font-family:'Cairo',sans-serif; transition:opacity .2s; }
.btn-primary { background:#3b82f6; color:#fff; }
.btn:hover { opacity:.88; }
.muted { color:#94a3b8; font-weight:600; }
</style>
@endsection

@section('content')

@foreach(['success','error'] as $type)
@if(session($type))
<div class="flex items-center gap-3 {{ $type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }} border rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">{{ $type === 'success' ? 'check_circle' : 'error' }}</span>
    {{ session($type) }}
</div>
@endif
@endforeach

<div class="card mb-6">
    <div class="card-header">
        <h3>الأسعار الحالية</h3>
        <form action="{{ route('admin.fx-rates.refresh') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-rounded" style="font-size:18px">refresh</span>
                تحديث الآن
            </button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="fx-table">
            <thead>
                <tr>
                    <th>العملة</th>
                    <th>السعر المخزّن</th>
                    <th>المصدر</th>
                    <th>آخر تحديث</th>
                    <th>الحالة</th>
                    <th>هامش الأمان</th>
                    <th>السعر الفعلي المُطبَّق</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>
                    <td dir="ltr" style="text-align:right">{{ $row['currency'] }}</td>
                    <td dir="ltr" style="text-align:right">
                        {{ $row['stored_rate'] !== null ? number_format($row['stored_rate'], 6) : '—' }}
                    </td>
                    <td>{{ $row['stored_source'] ?? '—' }}</td>
                    <td>
                        @if($row['fetched_at'])
                            {{ $row['fetched_at']->format('Y-m-d H:i') }}
                            <div class="muted" style="font-size:.72rem;">
                                منذ {{ number_format($row['age_hours'], 1) }} ساعة
                            </div>
                        @else
                            <span class="muted">لم يتم الجلب بعد</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeClass = match($row['tier']) {
                                'fresh' => 'badge-green',
                                'stale' => 'badge-amber',
                                'expired' => 'badge-red',
                                default => 'badge-gray',
                            };
                            $tierLabel = match($row['tier']) {
                                'fresh' => 'محدَّث',
                                'stale' => 'قديم (لا يزال يُستخدم)',
                                'expired' => 'منتهي',
                                default => 'غير متوفر',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $tierLabel }}</span>
                    </td>
                    <td dir="ltr" style="text-align:right">+{{ $markupPercent }}%</td>
                    <td dir="ltr" style="text-align:right">
                        @if($row['effective_rate'] !== null)
                            {{ number_format($row['effective_rate'], 6) }}
                            <div class="muted" style="font-size:.72rem;" dir="rtl">{{ $row['effective_source'] }}</div>
                        @else
                            <span class="badge badge-red">غير متاح — سيفشل الدفع بهذه العملة</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>سياسة الصلاحية</h3></div>
    <div style="padding:1.2rem 1.4rem; font-size:.85rem; color:#475569; line-height:2;">
        <p><strong>محدَّث:</strong> أقل من {{ $staleAfterHours }} ساعة — يُستخدم مباشرة.</p>
        <p><strong>قديم:</strong> بين {{ $staleAfterHours }} و{{ $maxAgeHours }} ساعة — لا يزال يُستخدم، لكن يُسجَّل تحذير في كل عملية دفع.</p>
        <p><strong>منتهي:</strong> {{ $maxAgeHours }} ساعة أو أكثر — يتم اللجوء للسعر الاحتياطي في <code>config/payment.php</code> إن وُجد، وإلا تُرفض عملية الدفع لهذه العملة تماماً (لا يُنشأ طلب ولا يتم الاتصال بـ Paymob).</p>
        <p class="muted" style="font-size:.78rem;">إذا ظل السعر "قديم" أو "منتهي" لفترة طويلة، فهذا يعني على الأرجح أن الجدولة اليومية (<code>fx:refresh</code>) توقفت عن العمل على الخادم — راجع إعداد الـ cron.</p>
    </div>
</div>

@endsection
