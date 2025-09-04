<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request, $lang)
    {
        Session::put('lang', $lang);

        // back to the previous page
        return redirect()->back();
    }
}
