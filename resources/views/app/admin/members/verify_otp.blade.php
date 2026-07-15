@extends('layouts.admin.app')
@section('title', 'التحقق من البريد الإلكتروني')
@section('page-title', 'التحقق من البريد الإلكتروني')
@section('page-subtitle', 'أدخل رمز التحقق المرسَل لبريد الكوتش')

@section('style')
<style>
.otp-wrapper {
    max-width: 520px;
    margin: 0 auto;
}
.otp-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 24px;
    overflow: hidden;
}
.otp-header {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 36px 32px;
    text-align: center;
}
.otp-icon-wrap {
    width: 72px; height: 72px;
    background: rgba(255,255,255,0.12);
    border-radius: 24px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    border: 2px solid rgba(255,255,255,0.2);
}
.otp-title { color: #fff; font-size: 22px; font-weight: 900; margin-bottom: 6px; }
.otp-sub { color: rgba(255,255,255,0.7); font-size: 13px; }
.otp-email-tag {
    display: inline-block;
    background: rgba(212,237,87,0.2);
    border: 1px solid rgba(212,237,87,0.4);
    color: #D4ED57;
    font-size: 13px;
    font-weight: 700;
    padding: 6px 16px;
    border-radius: 20px;
    margin-top: 12px;
    direction: ltr;
}
.otp-body { padding: 36px 32px; }

.otp-inputs {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 28px 0;
    direction: ltr;
}
.otp-digit {
    width: 52px; height: 62px;
    background: #F4F7FF;
    border: 2px solid #E0E8FF;
    border-radius: 14px;
    font-size: 28px;
    font-weight: 900;
    color: #174DAD;
    text-align: center;
    font-family: 'Courier New', monospace;
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
    caret-color: transparent;
}
.otp-digit:focus {
    border-color: #174DAD;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(23,77,173,0.12);
}
.otp-digit.filled {
    border-color: #174DAD;
    background: #EBF0FF;
}
.otp-digit.error {
    border-color: #EF4444;
    background: #FFF5F5;
    animation: shake .3s ease;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    25%      { transform: translateX(-4px); }
    75%      { transform: translateX(4px); }
}

.timer-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #FFFBE6;
    border: 1px solid #FDE68A;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #92400E;
    font-weight: 600;
    margin-bottom: 24px;
}
.timer-badge .countdown {
    font-weight: 900;
    color: #D97706;
    font-size: 14px;
    min-width: 32px;
}
.timer-badge.expired {
    background: #FFF5F5;
    border-color: #FECACA;
    color: #991B1B;
}

.verify-btn {
    width: 100%;
    padding: 15px;
    background: #174DAD;
    color: #fff;
    font-size: 15px;
    font-weight: 900;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: opacity .2s, transform .2s;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
}
.verify-btn:hover:not(:disabled) { opacity: .9; transform: translateY(-1px); }
.verify-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.resend-btn {
    width: 100%;
    padding: 12px;
    background: #F4F7FF;
    color: #174DAD;
    font-size: 13px;
    font-weight: 700;
    border: 1.5px solid #E0E8FF;
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.resend-btn:hover:not(:disabled) { background: #EBF0FF; border-color: #174DAD; }
.resend-btn:disabled { opacity: .5; cursor: not-allowed; }

.attempts-left {
    display: flex;
    gap: 6px;
    justify-content: center;
    margin-top: 16px;
}
.attempt-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #174DAD;
    transition: background .2s;
}
.attempt-dot.used { background: #EF4444; }

.error-box {
    background: #FFF5F5;
    border: 1.5px solid #FECACA;
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #991B1B;
    font-weight: 600;
}

.back-link {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #94A3B8;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    justify-content: center;
    margin-top: 20px;
    transition: color .2s;
}
.back-link:hover { color: #64748B; }
</style>
@endsection

@section('content')

<div class="otp-wrapper">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-400">
        <a href="{{ route('admin.members.index') }}" class="flex items-center gap-1.5 hover:text-blue-500 transition">
            <span class="material-symbols-rounded" style="font-size:17px">arrow_forward_ios</span>
            الأعضاء
        </a>
        <span class="material-symbols-rounded" style="font-size:15px">chevron_left</span>
        <span class="text-slate-600">التحقق من البريد</span>
    </div>

    {{-- Error --}}
    @if ($errors->has('otp'))
    <div class="error-box mb-5">
        <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
        {{ $errors->first('otp') }}
    </div>
    @endif

    @if(session('otp_sent'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-5 py-4 mb-5 font-bold text-sm text-green-700">
        <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">mark_email_read</span>
        {{ session('otp_sent') }}
    </div>
    @endif

    <div class="otp-card">

        {{-- Header --}}
        <div class="otp-header">
            <div class="otp-icon-wrap">
                <span class="material-symbols-rounded text-white" style="font-size:36px;font-variation-settings:'FILL' 1">verified</span>
            </div>
            <div class="otp-title">التحقق من البريد الإلكتروني</div>
            <div class="otp-sub">تم إرسال رمز مكوّن من 6 أرقام إلى</div>
            <div class="otp-email-tag">{{ $email }}</div>
        </div>

        {{-- Body --}}
        <div class="otp-body">

            <p class="text-sm font-bold text-slate-500 text-center mb-1">
                اطلب من الكوتش <strong class="text-slate-700">{{ $name }}</strong> مشاركة الرمز معك
            </p>

            {{-- Timer --}}
            <div class="timer-badge" id="timerBadge">
                <span class="material-symbols-rounded text-amber-500 flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">timer</span>
                <span>الرمز صالح لمدة </span>
                <span class="countdown" id="countdown">15:00</span>
            </div>

            {{-- OTP Form --}}
            <form method="POST" action="{{ route('admin.members.verify-otp.post', $token) }}" id="otpForm">
                @csrf
                <input type="hidden" name="otp" id="otpHidden">

                <div class="otp-inputs" id="otpInputs">
                    @for($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        class="otp-digit {{ $errors->has('otp') ? 'error' : '' }}"
                        data-index="{{ $i }}"
                        autocomplete="off"
                        pattern="[0-9]"
                    >
                    @endfor
                </div>

                {{-- Attempts remaining --}}
                @if(isset($attemptsLeft))
                <div class="attempts-left mb-5">
                    @for($i = 0; $i < 3; $i++)
                    <div class="attempt-dot {{ $i >= $attemptsLeft ? 'used' : '' }}"></div>
                    @endfor
                    <span class="text-xs font-bold text-slate-400 mr-2">{{ $attemptsLeft }} محاولات متبقية</span>
                </div>
                @endif

                <button type="submit" class="verify-btn" id="verifyBtn" disabled>
                    <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">verified</span>
                    تأكيد الرمز وإنشاء الحساب
                </button>
            </form>

            {{-- Resend --}}
            <form method="POST" action="{{ route('admin.members.resend-otp', $token) }}">
                @csrf
                <button type="submit" class="resend-btn" id="resendBtn" disabled>
                    <span class="material-symbols-rounded" style="font-size:16px">refresh</span>
                    <span id="resendText">إعادة إرسال الرمز (متاح بعد <span id="resendCountdown">02:00</span>)</span>
                </button>
            </form>

            <a href="{{ route('admin.members.create') }}" class="back-link">
                <span class="material-symbols-rounded" style="font-size:16px">arrow_forward_ios</span>
                إلغاء والعودة لإنشاء حساب جديد
            </a>

        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// ── OTP digit inputs ─────────────────────────────────────────────
const digits    = document.querySelectorAll('.otp-digit');
const hidden    = document.getElementById('otpHidden');
const verifyBtn = document.getElementById('verifyBtn');

digits.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
        const val = e.target.value.replace(/\D/g, '');
        e.target.value = val;
        e.target.classList.toggle('filled', val !== '');
        e.target.classList.remove('error');

        if (val && idx < digits.length - 1) {
            digits[idx + 1].focus();
        }
        syncHidden();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            digits[idx - 1].focus();
            digits[idx - 1].value = '';
            digits[idx - 1].classList.remove('filled');
            syncHidden();
        }
        // Allow only digits, Backspace, Tab, Arrow keys
        if (!/^\d$/.test(e.key) && !['Backspace','Tab','ArrowLeft','ArrowRight','Delete'].includes(e.key)) {
            e.preventDefault();
        }
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        pasted.split('').slice(0, 6).forEach((ch, i) => {
            if (digits[i]) {
                digits[i].value = ch;
                digits[i].classList.add('filled');
            }
        });
        const next = Math.min(pasted.length, 5);
        digits[next].focus();
        syncHidden();
    });
});

// Focus first digit on load
digits[0].focus();

function syncHidden() {
    const otp = Array.from(digits).map(d => d.value).join('');
    hidden.value = otp;
    verifyBtn.disabled = otp.length < 6;
}

// ── Countdown timer (15 min) ─────────────────────────────────────
let totalSeconds    = 15 * 60;
let resendSeconds   = 2 * 60;
const countdownEl   = document.getElementById('countdown');
const resendBtn     = document.getElementById('resendBtn');
const resendCdEl    = document.getElementById('resendCountdown');
const timerBadge    = document.getElementById('timerBadge');

function pad(n) { return String(n).padStart(2, '0'); }

const timer = setInterval(() => {
    totalSeconds--;
    resendSeconds--;

    // Main countdown
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    countdownEl.textContent = pad(m) + ':' + pad(s);

    if (totalSeconds <= 0) {
        clearInterval(timer);
        countdownEl.textContent = '00:00';
        timerBadge.classList.add('expired');
        timerBadge.querySelector('span:last-child').textContent = 'انتهت صلاحية الرمز — أعد الإرسال';
        verifyBtn.disabled = true;
    }

    // Resend countdown
    if (resendSeconds <= 0) {
        resendBtn.disabled = false;
        document.getElementById('resendText').innerHTML =
            '<span class="material-symbols-rounded" style="font-size:16px">refresh</span> إعادة إرسال رمز جديد';
    } else {
        const rm = Math.floor(resendSeconds / 60);
        const rs = resendSeconds % 60;
        resendCdEl.textContent = pad(rm) + ':' + pad(rs);
    }
}, 1000);
</script>
@endsection
