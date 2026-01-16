<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getUser(Request $request)
    {
        return $request->user();
    }

    // Add more methods as needed for other API actions
}
