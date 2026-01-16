<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/patient/index", 'name' => "Patients"], ['name' => "Scheduled list"]
        ];
        return view('/schedule/index', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
