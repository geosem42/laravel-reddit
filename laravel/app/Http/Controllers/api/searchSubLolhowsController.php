<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subLolhow;

class searchSubLolhowsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query, subLolhow $subLolhow)
    {
        $results = $subLolhow->searchByName($query)->toArray();

        return response()->json(
           $results
        );
    }
}
