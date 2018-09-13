<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subPlebbit;
use App\Subscription;
use Illuminate\Support\Facades\Auth;

class subscriptionsApiController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe($name, Request $request, subPlebbit $subPlebbit, Subscription $subscription)
    {
        $subPlebbit = $subPlebbit->select('name', 'id')->where('name', $name)->first();
        if (!$subPlebbit) {
            return Response()->json([
               'error' => "subplebbit doesn't exist"
            ], 404);
        }

        $user = Auth::guard('api')->user();

        $sub = $subscription->where('user_id', $user->id)->where('sub_plebbit_id', $subPlebbit->id)->first();
        if (!$sub) {
            $sub = new Subscription();
            $sub->user_id = $user->id;
            $sub->sub_plebbit_id = $subPlebbit->id;
            $sub->save();
        }

        return Response()->json([
            'status' => 'success',
            'sub_plebbit' => $subPlebbit->name
        ], 200);
    }

    public function unsubscribe($name, Request $request, subPlebbit $subPlebbit, Subscription $subscription)
    {
        $subPlebbit = $subPlebbit->select('name', 'id')->where('name', $name)->first();
        if (!$subPlebbit) {
            return Response()->json([
                'error' => "subplebbit doesn't exist"
            ], 404);
        }

        $user = Auth::guard('api')->user();

        $sub = $subscription->where('user_id', $user->id)->where('sub_plebbit_id', $subPlebbit->id)->first();
        if ($sub) {
            $sub->delete();
        }

        return Response()->json([
            'status' => 'success',
            'sub_plebbit' => $subPlebbit->name
        ], 200);
    }
}
