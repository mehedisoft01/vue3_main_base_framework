<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;


class DashboardController extends Controller
{

    public function singleApp()
    {
        return view('backend');
    }

    public function employeeApp()
    {
        return view('backend');
    }

    public function dashboardData()
    {
        $authUser = auth()->user();

        $color = ($authUser->theme && $authUser->theme == 'bg-default bg-theme2') ? '#000000' : '#FFFFFF';

        $data = [];

        return $data;
    }
}
