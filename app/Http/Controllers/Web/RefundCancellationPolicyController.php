<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RefundCancellationPolicyController extends Controller
{
    function index()
    {
        return view('app.web.refund_cancellation_policy');
    }
}
