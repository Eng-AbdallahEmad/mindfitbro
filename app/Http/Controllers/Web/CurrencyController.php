<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(private CurrencyService $currencyService) {}

    public function switch(Request $request)
    {
        $currency = strtoupper($request->input('currency', 'SAR'));

        $this->currencyService->set($currency);

        return back();
    }
}
