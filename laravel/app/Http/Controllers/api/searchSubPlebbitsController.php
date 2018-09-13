<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subPlebbit;

class searchSubPlebbitsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query, subPlebbit $subPlebbit)
    {
        $results = $subPlebbit->searchByName($query)->toArray();

        return response()->json(
           $results
        );
    }
}
