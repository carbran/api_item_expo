<?php

namespace App\Http\Controllers;

class UsefulController extends Controller
{
    public function version()
    {
        return response()->json(['data' => date('d-m-Y H:i:s'),
            'status'                       => 'OK',
            'message'                      => 'API is running',
            'min_version'                  => '1.0.0']);
    }
}
