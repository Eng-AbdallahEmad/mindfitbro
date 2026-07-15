@extends('layouts.admin.app')

@section('title', 'سجل دعوات الأبطال')
@section('page-title', 'سجل دعوات الأبطال')
@section('page-subtitle', 'جميع دعوات الخصم المُرسلة عبر برنامج جائزة الأبطال')

@section('style')
<style>
    .stat-card {
        background:#fff;border-radius:14px;padding:1.1rem 1.3rem;
        border:1px solid #e5e7eb;display:flex;align-items:center;gap:.9rem;
    }
    .stat-val { font-size:1.5rem;font-weight:900;color:#1C1C1C;line-height:1; }
    .stat-lbl { font-size:.7rem;font-weight:700;color:#9ca3af;margin-top:.2rem; }
    .badge { display:inline-flex;align-items:center;gap:4px;font-size:.65rem;font-weight:800;
             padding:2px 8px;border-radius:20px;border-width:1px;border-style:solid; }
    .badge-pending  { background:#fffbeb;color:#d97706;border-color:#fcd34d; }
    .badge-redeemed { background:#f0fdf4;color:#16a34a;border-color:#86efac; }
    .badge-expired  { background:#f9fafb;color:#9ca3af;border-color:#e5e7eb; }
    .table-wrap { overflow-x:auto; }
    table { width:100%;border-collapse:collapse;font-size:.8rem; }
    th { text-align:right;padding:.6rem 1rem;background:#f8fafc;font-weight:800;color:#64748b;
         font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #e2e8f0; }
    td { padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;color:#374151;font-weight:600; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#fafafa; }
    .filter-input { border:1px solid #e2e8f0;border-radius:8px;padding:.4rem .75rem;
                    font-size:.8rem;font-weight:600;color:#374151;outline:none;background:#fff; }
    .filter-input:focus { border-color:#3b82f6;box-shadow:0 0 0 2px rgba(59,130,246,.15); }
</style>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['val' => $stats['total'],    'lbl' => 'إجمالي الدعوات',    'icon' => 'send',         'color' => '#6366f1'],
        ['val' => $stats['pending'],  'lbl' => 'في الانتظار',       'icon' => 'schedule',     'color' => '#f59e0b'],
        ['val' => $stats['redeemed'], 'lbl' => 'مُستخدمة',          'icon' => 'check_circle', 'color' => '#22c55e'],
        ['val' => $stats['expired'],  'lbl' => 'منتهية الصلاحية',   'icon' => 'cancel',       'color' => '#9ca3af'],
    ] as $card)
    <div class="stat-card">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $card['color'] }}18">
            <span class="material-symbols-rounded" style="font-size:20px;color:{{ $card['color'] }};font-variation-settings:'FILL' 1">{{ $card['icon'] }}</span>
        </div>
        <div>
            <p class="stat-val">{{ number_format($card['val']) }}</p>
            <p class="stat-lbl">{{ $card['lbl'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white border border-gray-100 rounded-2xl p-4 mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" action="{{ route('admin.family-invitations.index') }}" class="flex flex-wrap items-center gap-3 w-full">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="ابحث بالبريد أو الاسم أو المُرسِل..." class="filter-input flex-1" style="min-width:180px">
        <select name="status" class="filter-input">
            <option value="">كل الحالات</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>في الانتظار</option>
            <option value="redeemed" {{ request('status') === 'redeemed' ? 'selected' : '' }}>مُستخدمة</option>
            <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>منتهية</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition">
            بحث
        </button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.family-invitations.index') }}" class="text-slate-400 hover:text-slate-600 font-bold text-sm">مسح</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>المُرسِل</th>
                    <th>المُدعو</th>
                    <th>الباقة</th>
                    <th>كود الخصم</th>
                    <th>نسبة الخصم</th>
                    <th>الحالة</th>
                    <th>تاريخ الإرسال</th>
                    <th>تاريخ الاستخدام</th>
                    <th>انتهاء الكود</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invitations as $inv)
                <tr>
                    <td class="text-slate-400 text-xs">{{ $inv->id }}</td>
                    <td>
                        <p class="font-black text-slate-800 text-xs">{{ $inv->inviter?->name ?? '—' }}</p>
                        <p class="text-slate-400 text-[10px]">{{ $inv->inviter?->email ?? '' }}</p>
                    </td>
                    <td>
                        <p class="font-black text-slate-800 text-xs">{{ $inv->invitee_name ?: '—' }}</p>
                        <p class="text-slate-400 text-[10px]">{{ $inv->invitee_email }}</p>
                    </td>
                    <td class="text-xs text-slate-600">{{ $inv->subscription?->plan?->name ?? '—' }}</td>
                    <td>
                        <span class="font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-[11px]"
                              style="font-family:monospace;letter-spacing:1px">
                            {{ $inv->coupon?->code ?? '—' }}
                        </span>
                    </td>
                    <td class="font-black text-slate-700 text-xs">
                        {{ $inv->coupon ? number_format($inv->coupon->value, 0) . '%' : '—' }}
                    </td>
                    <td>
                        @php
                            $badgeClass = ['pending' => 'badge-pending', 'redeemed' => 'badge-redeemed', 'expired' => 'badge-expired'][$inv->status] ?? 'badge-expired';
                            $badgeIcon  = ['pending' => 'schedule', 'redeemed' => 'check_circle', 'expired' => 'cancel'][$inv->status] ?? 'cancel';
                            $badgeLabel = ['pending' => 'انتظار', 'redeemed' => 'مُستخدمة', 'expired' => 'منتهية'][$inv->status] ?? $inv->status;
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            <span class="material-symbols-rounded" style="font-size:10px;font-variation-settings:'FILL' 1">{{ $badgeIcon }}</span>
                            {{ $badgeLabel }}
                        </span>
                    </td>
                    <td class="text-xs text-slate-400">{{ $inv->sent_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="text-xs text-slate-400">{{ $inv->redeemed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="text-xs {{ $inv->coupon?->expires_at?->isPast() ? 'text-red-400' : 'text-slate-400' }}">
                        {{ $inv->coupon?->expires_at?->format('Y-m-d') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-12 text-slate-400">
                        <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px">mail_off</span>
                        لا توجد دعوات بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invitations->hasPages())
    <div class="p-4 border-t border-gray-100 flex justify-center">
        {{ $invitations->links() }}
    </div>
    @endif
</div>

@endsection
