<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermsOfServiceController extends Controller
{
    function index()
    {
        return view('app.web.terms_of_service');
        
    }
}
