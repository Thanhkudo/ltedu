<?php

namespace App\Http\Controllers;

class GuideController extends Controller
{
    public function student()
    {
        return view('guide.student');
    }

    public function admin()
    {
        return view('guide.admin');
    }
}
