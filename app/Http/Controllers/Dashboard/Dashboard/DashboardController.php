<?php

namespace App\Http\Controllers\Dashboard\Dashboard;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('pages.dashboard.dashboard.index', ['title' => 'Dashboard']);
    }
}
