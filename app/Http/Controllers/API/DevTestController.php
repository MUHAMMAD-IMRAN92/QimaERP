<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DevTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $secret = 'hJYNe0mii7wU6wiNdYUh0zI8wLABJJtzSBu6fGw7';
        abort_unless($request->secret === $secret, 401, 'Not Authorized for this request');

        return response()->json([
            'message' => 'Welcome Dev'
        ]);
    }

    public function store(Request $request)
    {
        return $request->all();
    }
}
