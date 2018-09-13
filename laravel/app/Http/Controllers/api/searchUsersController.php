<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\subPlebbit;
use App\Subscription;
use Illuminate\Support\Facades\Auth;

class searchUsersController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query, Request $request, User $user)
    {
        $results = $user->searchByName($query)->toArray();

        return Response()->json(
            $results
        , 200);
    }

}
