<?php


namespace App\Http\Controllers;


class ViewController
{
    public function login()
    {
        try {
            $userId = auth()->payload()->get('sub');
            if ($userId) {
                return redirect('/ranking');
            }
        } catch (\Exception $exception) {}

        return view('login');
    }

    public function register()
    {
        try {
            $userId = auth()->payload()->get('sub');
            if ($userId) {
                return redirect('/ranking');
            }
        } catch (\Exception $exception) {}

        return view('register');
    }

    public function logout()
    {
        auth()->logout();

        return redirect('login');
    }
}
