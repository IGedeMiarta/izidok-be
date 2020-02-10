<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    #for testing purpose only
    public function upload(Request $request){
        return $request->all();
    }
}
