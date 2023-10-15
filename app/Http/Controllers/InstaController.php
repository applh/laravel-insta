<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstaController extends Controller
{
    //
    public function home()
    {
        return view('insta_home');
    }

    public function user($name)
    {
        return view('insta_user', ['name' => $name]);
    }

    public function dashboard()
    {
        // keep breeze dashboard
        return view('dashboard');
    }

}
