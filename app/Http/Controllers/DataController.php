<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function open() 
    {
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data'),200);

    }
    // php artisan jwt:secret xvOk7WY08UpOrSaSSabmkwXKRW3uzp6
    public function closed() 
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'),200);
    }
}
