@php
    $remaining      = max(0, $maxInvites - $usedInvites);
    $rowErrors      = session('row_errors', []);
    $oldInvitations = old('invitations', [['name' => '', 'email' => '']]);
    // Ensure at least one row
    if (empty($oldInvitations)) {
        $oldInvitations = [['name' => '', 'email' => '']];
    }
@endphp

{{-- ── FAMILY REWARD SECTION ─────────────────────────────────── --}}
<div class="anim anim-5 mt-5" id="family-reward">

    {{-- Success flash --}}
    @if(session('invitation_sent'))
    <div class="card mb-4" style="direction:{{ $dir }};border-{{ $isRtl ? 'right' : 'left' }}:4px solid #22c55e;">
        <div class="flex items-center gap-3 font-arabic">
            <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:28px;font-variation-settings:'FILL' 1">check_circle</span>
            <div>
                <p class="font-black text-textColor text-sm">{{ __('messages.family_reward.sent_success') }}</p>
                <p class="text-gray-400 text-xs font-bold">{{ __('messages.family_reward.sent_count', ['n' => session('invitation_sent')]) }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Quota error --}}
    @error('quota')
    <div class="card mb-4" style="direction:{{ $dir }};border-{{ $isRtl ? 'right' : 'left' }}:4px solid #ef4444;">
        <p class="font-arabic font-bold text-red-500 text-sm">{{ $message }}</p>
    </div>
    @enderror

    {{-- Main reward card --}}
    <div class="card-dark" style="direction:{{ $dir }}">

        {{-- Header --}}
        <div class="relative z-10 flex items-start sm:items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl flex-shrink-0 flex items-center justify-center"
                     style="background:rgba(212,237,87,0.15);border:1px solid rgba(212,237,87,0.3)">
                    <span class="material-symbols-rounded text-accent" style="font-size:30px;font-variation-settings:'FILL' 1">card_giftcard</span>
                </div>
                <div class="font-arabic">
                    <h3 class="text-white font-black text-base leading-tight mb-1">{{ __('messages.family_reward.title') }}</h3>
                    <p class="text-white/50 text-xs font-bold">{{ __('messages.family_reward.subtitle') }}</p>
                </div>
            </div>
            {{-- Quota pill --}}
            <div class="flex-shrink-0 text-center font-arabic">
                <div class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 rounded-full px-3 py-1.5">
                    <span class="material-symbols-rounded text-accent" style="font-size:14px;font-variation-settings:'FILL' 1">send</span>
                    <span class="text-white font-black text-sm">{{ $usedInvites }}/{{ $maxInvites }}</span>
                    <span class="text-white/50 text-xs font-bold">{{ __('messages.family_reward.used_label') }}</span>
                </div>
            </div>
        </div>

        @if($remaining > 0)
        {{-- Invitation form --}}
        <form method="POST" action="{{ route('family-invitations.store') }}" class="relative z-10">
            @csrf

            <div x-data="{
                rows: @js($oldInvitations),
                rowErrors: @js($rowErrors),
                get remaining() { return {{ $remaining }} - (this.rows.length - 1); },
                addRow() {
                    if (this.rows.length < {{ $remaining }}) {
                        this.rows.push({ name: '', email: '' });
                    }
                },
                removeRow(i) {
                    if (this.rows.length > 1) {
                        this.rows.splice(i, 1);
                    }
                }
            }">
                <div class="flex flex-col gap-3 mb-4">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                            <div class="flex flex-col sm:flex-row items-start gap-2">
                                {{-- Name --}}
                                <div class="flex-1 w-full">
                                    <input type="text"
                                           :name="'invitations[' + index + '][name]'"
                                           x-model="row.name"
                                           placeholder="{{ __('messages.family_reward.name_placeholder') }}"
                                           class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white text-sm font-arabic font-bold placeholder-white/30 focus:outline-none focus:border-accent/60"
                                           style="min-width:0">
                                    <p x-show="rowErrors[index]?.name"
                                       x-text="rowErrors[index]?.name"
                                       class="text-red-300 text-xs font-bold font-arabic mt-1"
                                       style="display:none"></p>
                                </div>
                                {{-- Email --}}
                                <div class="flex-1 w-full">
                                    <input type="email"
                                           :name="'invitations[' + index + '][email]'"
                                           x-model="row.email"
                                           placeholder="{{ __('messages.family_reward.email_placeholder') }}"
                                           class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white text-sm font-arabic font-bold placeholder-white/30 focus:outline-none focus:border-accent/60"
                                           style="min-width:0;direction:ltr;text-align:{{ $isRtl ? 'right' : 'left' }}">
                                    <p x-show="rowErrors[index]?.email"
                                       x-text="rowErrors[index]?.email"
                                       class="text-red-300 text-xs font-bold font-arabic mt-1"
                                       style="display:none"></p>
                                </div>
                                {{-- Remove button --}}
                                <button type="button"
                                        @click="removeRow(index)"
                                        x-show="rows.length > 1"
                                        class="flex-shrink-0 w-9 h-9 rounded-lg bg-white/5 hover:bg-red-500/20 text-white/30 hover:text-red-400 flex items-center justify-center transition"
                                        title="{{ __('messages.family_reward.remove_row') }}"
                                        style="display:none">
                                    <span class="material-symbols-rounded" style="font-size:18px">remove</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Add row + Submit --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <button type="button"
                            @click="addRow()"
                            x-show="rows.length < {{ $remaining }}"
                            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-white/20 text-white/60 hover:text-white hover:border-white/40 font-arabic font-bold text-sm transition"
                            style="display:none">
                        <span class="material-symbols-rounded" style="font-size:16px">add</span>
                        {{ __('messages.family_reward.add_row') }}
                    </button>
                    <button type="submit"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-accent hover:bg-accent/90 text-textColor font-black font-arabic text-sm px-6 py-2.5 rounded-xl transition">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">send</span>
                        {{ __('messages.family_reward.send_btn') }}
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="relative z-10 bg-white/5 border border-white/10 rounded-xl p-4 font-arabic text-center">
            <p class="text-white/50 text-sm font-bold">{{ __('messages.family_reward.quota_reached', ['n' => $maxInvites]) }}</p>
        </div>
        @endif

    </div>{{-- /card-dark --}}

    {{-- Sent invitations list --}}
    @if($invitations->isNotEmpty())
    <div class="card mt-4" style="direction:{{ $dir }}">
        <h4 class="font-arabic font-black text-textColor text-sm mb-3 flex items-center gap-2">
            <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1">mail</span>
            {{ __('messages.family_reward.sent_list_title') }}
        </h4>
        <div class="flex flex-col divide-y divide-gray-100">
            @foreach($invitations as $inv)
            <div class="flex items-center justify-between gap-3 py-3 font-arabic {{ $loop->first ? 'pt-0' : '' }} {{ $loop->last ? 'pb-0' : '' }}">
                <div class="flex-1 min-w-0">
                    <p class="font-black text-textColor text-sm truncate">{{ $inv->invitee_name ?: $inv->invitee_email }}</p>
                    <p class="text-gray-400 text-xs font-bold truncate">{{ $inv->invitee_email }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Coupon code --}}
                    <span class="text-[10px] font-black text-primary bg-primary/5 border border-primary/20 px-2 py-0.5 rounded-full"
                          style="direction:ltr;letter-spacing:1px">
                        {{ $inv->coupon?->code }}
                    </span>
                    {{-- Status badge --}}
                    @php
                        $badgeMap = [
                            'pending'  => ['bg-amber-50 text-amber-600 border-amber-200',  'schedule',       __('messages.family_reward.status_pending')],
                            'used'     => ['bg-blue-50 text-blue-600 border-blue-200',     'pending',        __('messages.family_reward.status_used')],
                            'redeemed' => ['bg-green-50 text-green-600 border-green-200',  'check_circle',   __('messages.family_reward.status_redeemed')],
                            'expired'  => ['bg-gray-100 text-gray-400 border-gray-200',    'cancel',         __('messages.family_reward.status_expired')],
                        ];
                        [$badgeCss, $badgeIcon, $badgeLabel] = $badgeMap[$inv->status] ?? $badgeMap['expired'];
                    @endphp
                    <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full border {{ $badgeCss }}">
                        <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">{{ $badgeIcon }}</span>
                        {{ $badgeLabel }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
