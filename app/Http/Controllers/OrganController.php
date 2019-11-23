<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organ;

class OrganController extends Controller
{
    public function index()
  	{
  		$organ = Organ::all();
	    $data['organ'] = $organ;
	  	return response()->json([
	  	    			'success' => true,
	  	    			'message' => 'success',
	  	    			'data' => $data
	  	    		],201);
  	}
}
