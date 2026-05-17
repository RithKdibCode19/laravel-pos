<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        // Validate the language
        if (!in_array($lang, ['en', 'km'])) {
            return redirect()->back()->with('error', 'Invalid language selection');
        }

        // Store the selected language in session
        Session::put('lang', $lang);
        
        // Set the application locale
        App::setLocale($lang);

        return redirect()->back()->with('success', 'Language changed successfully');
    }
}
