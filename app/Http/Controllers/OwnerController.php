<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        if (session()->get('userid') == NULL)
        {
            return view('login');
        }else{
            $cafeinfo = $this->cafeInfo();
            $userinfo = $this->userInfo();
            return view('cafepanel', ['cafeinfo' => $cafeinfo, 'userinfo' => $userinfo]);
        }
    }

    public function getReviews ()
    {
        if (session()->get('userid') == NULL)
        {
            return view('login');
        }else{
            $reviewsinfo = $this->reviewsInfo();
            $userinfo = $this->userInfo();
            return view('reviews', ['reviewsinfo' => $reviewsinfo, 'userinfo' => $userinfo]);
        }
    }

    public function login (Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (!isset($email))
        {
            return 'No email enter';
        }

        if (!isset($password))
        {
            return 'No password enter';
        }

        $checkEmail = DB::table('users')->where('email', $email)->first();

        if (!isset($checkEmail))
        {
            return 'Email not valid';
        }

        $hashedPassword = $checkEmail->password;

        if (Hash::check($password, $hashedPassword))
        {
            session(['userid' => $checkEmail->id]);
            return redirect('/');
        }
    }

    private function cafeInfo()
    {
        $owner_id = session()->get('userid');
        return DB::table('cafelist')->where('owner_id', $owner_id)->get();
    }

    private function cafeInfoAll()
    {
        $owner_id = session()->get('userid');
        return DB::table('cafelist')->where('owner_id', $owner_id)->first();
    }

    private function reviewsInfo()
    {
        $cafe_id = $this->cafeInfoAll();
        return DB::table('reviews')->where('cafe_id', $cafe_id->id)->get();
    }

    private function userInfo()
    {
        $owner_id = session()->get('userid');
        return DB::table('users')->where('id', $owner_id)->get();
    }

    public function logout()
    {
        session(['userid' => NULL]);
        return redirect('/');
    }
}