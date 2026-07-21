<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryPolicyController extends Controller
{
    function index()
    {
        return view('app.web.delivery_policy');
    }
}
