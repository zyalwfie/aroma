<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman beranda (home).
     */
    public function index()
    {
        return view('frontend.home');
    }
}
