@props(['current' => null])
@php
    $cur = $current ?? (session('currency') ?? 'SAR');
    $currencies = [
        'SAR' => ['flag' => '', 'label' => 'SAR'],
        'EGP' => ['flag' => '', 'label' => 'EGP'],
        'TND' => ['flag' => '', 'label' => 'TND'],
        'USD' => ['flag' => '', 'label' => 'USD'],
    ];
@endphp
<div class="flex items-center gap-1 font-arabic">
    @foreach($currencies as $code => $info)
    <form method="POST" action="{{ route('currency.switch') }}" class="inline">
        @csrf
        <input type="hidden" name="currency" value="{{ $code }}">
        <button type="submit"
            class="text-xs font-bold px-2 py-1 rounded-lg transition-all {{ $cur === $code ? 'bg-primary text-white' : 'bg-white/10 text-white/60 hover:bg-white/20 hover:text-white' }}"
            title="{{ $code }}">
            {{ $info['flag'] }} {{ $info['label'] }}
        </button>
    </form>
    @endforeach
</div>
