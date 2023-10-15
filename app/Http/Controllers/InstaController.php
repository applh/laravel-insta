<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

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

    public function dashboard(Request $request)
    {
        // get logged in user
        $user = $request->user();

        // keep breeze dashboard
        return view('dashboard', [
            'user' => $user,
            'insta_access_token' => $insta_access_token ?? '',
        ]);
    }

    public function api(Request $request)
    {
        // get logged in user
        $user = $request->user();

        // validate insta access token
        $request->validate([
            'insta_access_token' => 'required|string|min:1|max:255',
        ]);

        // insta: user access token
        $insta_access_token = $request->input('insta_access_token');

        // keep breeze dashboard
        return redirect('/dashboard');
    }
}
