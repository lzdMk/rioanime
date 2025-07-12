<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        return view('admin/dashboard');
    }

    public function metrics()
    {
        return view('admin/metrics');
    }

    public function accounts()
    {
        return view('admin/accounts');
    }

    public function animeManage()
    {
        return view('admin/anime_manage');
    }
}