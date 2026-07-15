@extends('layouts.admin.app')

@section('title', 'ملف المدرب — ' . $coach->name)
@section('page-title', 'ملف المدرب')
@section('page-subtitle', 'عرض بيانات وإحصائيات المدرب')

@section('style')
<style>
    .info-chip {
        display: inline-flex; align-items: center; gap: .5rem;
        background: #f1f5f9; border-radius: 10px;
        padding: .45rem .9rem; font-size: .8rem; font-weight: 700; color: #475569;
    }
    .stat-pill {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: .2rem; padding: 1rem .5rem;
    }
    .card { background: #fff; border: 1px solid #e8edf5; border-radius: 20px; overflow: hidden; }
    .card-header { display: flex; align-items: center; gap: .85rem; padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9; background: #fafbfd; }
    .card-icon { width: 36px; height: 36px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .card-body { padding: 1.4rem; }

    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { padding: .6rem 1rem; text-align: right; font-size: .69rem; font-weight: 900; color: #94a3b8; letter-spacing: .07em; background: #f8fafc; border-bottom: 1px solid #f0f4f8; white-space: nowrap; }
    .admin-table td { padding: .8rem 1rem; font-size: .83rem; font-weight: 600; color: #374151; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover td { background: #f8fafc; }

    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .75rem; border-radius: 999px; font-size: .71rem; font-weight: 800; white-space: nowrap; }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; flex-shrink:0; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-red    { background:#fee2e2; color:#dc2626; }
    .badge-blue   { background:#eff6ff; color:#2563eb; }
    .badge-cyan   { background:#ecfeff; color:#0891b2; }
    .badge-orange { background:#fff7ed; color:#ea580c; }

    .page-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 .4rem; border-radius: 8px; font-size: .78rem; font-weight: 800; text-decoration: none; transition: all .18s; border: 1.5px solid transparent; }
    .page-btn-active  { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .page-btn-normal  { background: #fff; color: #64748b; border-color: #e2e8f0; }
    .page-btn-normal:hover { border-color: #3b82f6; color: #3b82f6; }
    .page-btn-disabled { background: #f8fafc; color: #cbd5e1; border-color: #f1f5f9; pointer-events: none; }
</style>
@endsection

@section('content')

{{-- Flash --}}
@if(session('success'))
<div id="flashMsg" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px">check_circle</span>
    {{ session('success') }}
    <button onclick="document.getElementById('flashMsg').remove()" class="mr-auto text-green-400 hover:text-green-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

{{-- Back --}}
<div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-400">
    <a href="{{ route('admin.coaches.index') }}" class="flex items-center gap-1.5 hover:text-blue-500 transition">
        <span class="material-symbols-rounded" style="font-size:17px">arrow_forward_ios</span>
        المدربون
    </a>
    <span class="material-symbols-rounded" style="font-size:15px">chevron_left</span>
    <span class="text-slate-600">{{ $coach->name }}</span>
</div>

{{-- ══ HERO CARD ══ --}}
<div class="card mb-5">
    <div class="p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center text-2xl font-black flex-shrink-0">
                {{ mb_substr($coach->name, 0, 1) }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5 mb-2">
                    <h1 class="font-black text-slate-800 text-lg leading-tight">{{ $coach->name }}</h1>
                    @php
                        $stMap = ['active'=>['نشط','badge-green'],'inactive'=>['غير نشط','badge-gray'],'banned'=>['محظور','badge-red']];
                        [$stLabel, $stClass] = $stMap[$coach->status] ?? [$coach->status,'badge-gray'];
                    @endphp
                    <span class="badge {{ $stClass }}"><span class="dot"></span>{{ $stLabel }}</span>
                    <span class="badge badge-cyan">
                        <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">sports</span>
                        مدرب
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="info-chip">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:15px">alternate_email</span>
                        {{ $coach->username }}
                    </span>
                    <span class="info-chip" dir="ltr">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:15px">mail</span>
                        {{ $coach->email }}
                    </span>
                    @if($coach->phone)
                    <span class="info-chip" dir="ltr">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:15px">phone</span>
                        {{ $coach->phone }}
                    </span>
                    @endif
                    <span class="info-chip">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:15px">{{ $coach->gender === 'male' ? 'man' : 'woman' }}</span>
                        {{ $coach->gender === 'male' ? 'ذكر' : 'أنثى' }}
                    </span>
                    <span class="info-chip">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:15px">calendar_today</span>
                        انضم {{ $coach->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                <button onclick="openEditModal()"
                    class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-xs px-4 py-2.5 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:15px">edit</span>
                    تعديل
                </button>
                <button onclick="openBanModal({{ $coach->status === 'banned' ? 'true' : 'false' }})"
                    class="flex items-center gap-1.5 font-black text-xs px-4 py-2.5 rounded-xl transition
                        {{ $coach->status === 'banned' ? 'bg-green-50 hover:bg-green-100 text-green-600' : 'bg-orange-50 hover:bg-orange-100 text-orange-600' }}">
                    <span class="material-symbols-rounded" style="font-size:15px">{{ $coach->status === 'banned' ? 'lock_open' : 'block' }}</span>
                    {{ $coach->status === 'banned' ? 'رفع الحظر' : 'حظر' }}
                </button>
            </div>

        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-3 divide-x divide-x-reverse divide-slate-100 border-t border-slate-100 mt-5 pt-1">
            <div class="stat-pill">
                <p class="text-xl font-black text-slate-800">{{ number_format($evalStats['total']) }}</p>
                <p class="text-[11px] font-bold text-slate-400">إجمالي التقييمات</p>
            </div>
            <div class="stat-pill">
                <p class="text-xl font-black text-slate-800">{{ number_format($evalStats['this_month']) }}</p>
                <p class="text-[11px] font-bold text-slate-400">هذا الشهر</p>
            </div>
            <div class="stat-pill">
                <p class="text-xl font-black text-slate-800">{{ number_format($evalStats['members']) }}</p>
                <p class="text-[11px] font-bold text-slate-400">عدد الأعضاء</p>
            </div>
        </div>
    </div>
</div>

{{-- ══ EVALUATIONS TABLE ══ --}}
<div class="card">
    <div class="card-header">
        <div class="card-icon bg-cyan-50">
            <span class="material-symbols-rounded text-cyan-500" style="font-size:17px;font-variation-settings:'FILL' 1">assignment</span>
        </div>
        <div class="flex-1">
            <p class="font-black text-slate-800 text-sm">سجل التقييمات</p>
            <p class="text-[11px] text-slate-400 font-semibold">التقييمات التي أجراها هذا المدرب</p>
        </div>
        @if($evalStats['last_eval'])
        <span class="text-[11px] text-slate-400 font-semibold hidden sm:block">
            آخر تقييم: {{ \Carbon\Carbon::parse($evalStats['last_eval'])->format('d/m/Y') }}
        </span>
        @endif
    </div>

    @if($evaluations->isEmpty())
    <div class="flex flex-col items-center justify-center py-14 text-slate-300">
        <span class="material-symbols-rounded mb-3" style="font-size:44px;font-variation-settings:'FILL' 1">assignment</span>
        <p class="font-black text-slate-400 text-sm">لم يُجرِ هذا المدرب أي تقييمات بعد</p>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>العضو</th>
                    <th class="hidden sm:table-cell">الوزن (كجم)</th>
                    <th class="hidden sm:table-cell">الطول (سم)</th>
                    <th class="hidden md:table-cell">نسبة الدهون %</th>
                    <th class="hidden md:table-cell">الكتلة العضلية</th>
                    <th class="hidden lg:table-cell">مستوى اللياقة</th>
                    <th>تاريخ التقييم</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluations as $eval)
                @php
                    $fitnessMap = [
                        'beginner'     => ['مبتدئ',  'badge-gray'],
                        'intermediate' => ['متوسط',  'badge-blue'],
                        'advanced'     => ['متقدم',  'badge-cyan'],
                        'athlete'      => ['رياضي',  'badge-green'],
                    ];
                    [$fLabel, $fClass] = $fitnessMap[$eval->fitness_level ?? ''] ?? [$eval->fitness_level ?? '—', 'badge-gray'];
                @endphp
                <tr>
                    <td>
                        @if($eval->member)
                        <a href="{{ route('admin.members.show', $eval->member) }}" class="hover:text-blue-500 transition">
                            <p class="font-black text-slate-800 text-sm leading-tight">{{ $eval->member->name }}</p>
                            <p class="text-[11px] text-slate-400 font-semibold">@ {{ $eval->member->username }}</p>
                        </a>
                        @else
                        <span class="text-slate-300 text-xs">محذوف</span>
                        @endif
                    </td>
                    <td class="hidden sm:table-cell">{{ $eval->weight ? number_format($eval->weight, 1) : '—' }}</td>
                    <td class="hidden sm:table-cell">{{ $eval->height ? number_format($eval->height, 1) : '—' }}</td>
                    <td class="hidden md:table-cell">{{ $eval->body_fat_percentage ? number_format($eval->body_fat_percentage, 1).'%' : '—' }}</td>
                    <td class="hidden md:table-cell">{{ $eval->muscle_mass ? number_format($eval->muscle_mass, 1) : '—' }}</td>
                    <td class="hidden lg:table-cell">
                        @if($eval->fitness_level)
                        <span class="badge {{ $fClass }}">{{ $fLabel }}</span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="text-slate-400 text-xs font-bold">
                        {{ $eval->evaluated_at ? \Carbon\Carbon::parse($eval->evaluated_at)->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($evaluations->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400 font-semibold">
            عرض {{ $evaluations->firstItem() }}–{{ $evaluations->lastItem() }} من {{ $evaluations->total() }} تقييم
        </p>
        <div class="flex items-center gap-1.5">
            @if($evaluations->onFirstPage())
                <span class="page-btn page-btn-disabled"><span class="material-symbols-rounded" style="font-size:16px">chevron_right</span></span>
            @else
                <a href="{{ $evaluations->previousPageUrl() }}" class="page-btn page-btn-normal"><span class="material-symbols-rounded" style="font-size:16px">chevron_right</span></a>
            @endif
            @foreach($evaluations->getUrlRange(max(1,$evaluations->currentPage()-2), min($evaluations->lastPage(),$evaluations->currentPage()+2)) as $page => $url)
                @if($page == $evaluations->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn page-btn-normal">{{ $page }}</a>
                @endif
            @endforeach
            @if($evaluations->hasMorePages())
                <a href="{{ $evaluations->nextPageUrl() }}" class="page-btn page-btn-normal"><span class="material-symbols-rounded" style="font-size:16px">chevron_left</span></a>
            @else
                <span class="page-btn page-btn-disabled"><span class="material-symbols-rounded" style="font-size:16px">chevron_left</span></span>
            @endif
        </div>
    </div>
    @endif

    @endif
</div>

{{-- ══ EDIT MODAL ══ --}}
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[200] opacity-0 pointer-events-none transition-opacity duration-250 flex items-center justify-center p-4"
     onclick="closeModal()">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-250 overflow-hidden modal-inner" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <span class="material-symbols-rounded text-slate-500" style="font-size:16px;font-variation-settings:'FILL' 1">edit</span>
                </div>
                <p class="font-black text-slate-800 text-sm">تعديل بيانات المدرب</p>
            </div>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.coaches.update', $coach) }}" class="p-6 flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">الاسم الكامل</label>
                <input type="text" name="name" value="{{ old('name', $coach->name) }}" required
                       class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 font-cairo outline-none focus:border-blue-400 focus:bg-white transition">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone', $coach->phone) }}"
                       class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 font-cairo outline-none focus:border-blue-400 focus:bg-white transition" dir="ltr">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">كلمة مرور جديدة <span class="text-slate-400 font-semibold">(اتركها فارغة للإبقاء)</span></label>
                <input type="password" name="password" autocomplete="new-password" placeholder="••••••••"
                       class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 font-cairo outline-none focus:border-blue-400 focus:bg-white transition" dir="ltr">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••"
                       class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 font-cairo outline-none focus:border-blue-400 focus:bg-white transition" dir="ltr">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">الحالة</label>
                <select name="status"
                        class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 font-cairo outline-none focus:border-blue-400 focus:bg-white transition">
                    <option value="active"   {{ $coach->status === 'active'   ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ $coach->status === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="banned"   {{ $coach->status === 'banned'   ? 'selected' : '' }}>محظور</option>
                </select>
            </div>
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                @foreach($errors->all() as $err)
                    <p class="text-red-600 text-xs font-bold">• {{ $err }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">حفظ التغييرات</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ BAN MODAL ══ --}}
<div id="banModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[200] opacity-0 pointer-events-none transition-opacity duration-250 flex items-center justify-center p-4"
     onclick="closeBanModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl transform scale-95 transition-transform duration-250 overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div id="banIconWrap2" class="w-8 h-8 rounded-lg flex items-center justify-center">
                    <span id="banIcon2" class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1"></span>
                </div>
                <p id="banTitle2" class="font-black text-slate-800 text-sm"></p>
            </div>
            <button onclick="closeBanModal()" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p id="banDesc2" class="text-slate-500 text-sm font-semibold leading-relaxed"></p>
        </div>
        <form method="POST" action="{{ route('admin.coaches.status', $coach) }}" class="px-6 pb-6 flex gap-3">
            @csrf @method('PATCH')
            <button type="submit" id="banBtn2" class="flex-1 font-black text-sm py-2.5 rounded-xl transition text-white">تأكيد</button>
            <button type="button" onclick="closeBanModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    // ── Edit modal ──
    function openEditModal() {
        const m = document.getElementById('editModal');
        m.classList.remove('opacity-0','pointer-events-none');
        m.querySelector('.modal-inner').classList.remove('scale-95');
        m.querySelector('.modal-inner').classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        const m = document.getElementById('editModal');
        m.classList.add('opacity-0','pointer-events-none');
        m.querySelector('.modal-inner').classList.add('scale-95');
        m.querySelector('.modal-inner').classList.remove('scale-100');
        document.body.style.overflow = '';
    }

    // ── Ban modal ──
    function openBanModal(isBanned) {
        const m    = document.getElementById('banModal');
        const wrap = document.getElementById('banIconWrap2');
        const icon = document.getElementById('banIcon2');
        const title= document.getElementById('banTitle2');
        const desc = document.getElementById('banDesc2');
        const btn  = document.getElementById('banBtn2');
        const name = '{{ addslashes($coach->name) }}';

        if (isBanned) {
            wrap.className = 'w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center';
            icon.className = 'material-symbols-rounded text-green-500'; icon.textContent = 'lock_open';
            title.textContent = 'رفع الحظر عن المدرب';
            desc.innerHTML = `هل تريد رفع الحظر عن المدرب <strong>${name}</strong>؟ سيتمكن من تسجيل الدخول مجدداً.`;
            btn.className = 'flex-1 bg-green-500 hover:bg-green-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        } else {
            wrap.className = 'w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center';
            icon.className = 'material-symbols-rounded text-orange-500'; icon.textContent = 'block';
            title.textContent = 'حظر المدرب';
            desc.innerHTML = `هل تريد حظر المدرب <strong>${name}</strong>؟ لن يتمكن من الدخول حتى يتم رفع الحظر.`;
            btn.className = 'flex-1 bg-orange-500 hover:bg-orange-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        }

        m.classList.remove('opacity-0','pointer-events-none');
        document.body.style.overflow = 'hidden';
    }
    function closeBanModal() {
        document.getElementById('banModal').classList.add('opacity-0','pointer-events-none');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); closeBanModal(); }
    });

    // ── Auto-open edit modal if validation failed ──
    @if($errors->any())
    openEditModal();
    @endif
</script>
@endsection
