<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subLolhow;
use App\Subscription;
use Illuminate\Support\Facades\Auth;

class subscriptionsApiController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe($name, Request $request, subLolhow $subLolhow, Subscription $subscription)
    {
        $subLolhow = $subLolhow->select('name', 'id')->where('name', $name)->first();
        if (!$subLolhow) {
            return Response()->json([
               'error' => "sublolhow doesn't exist"
            ], 404);
        }

        $user = Auth::guard('api')->user();

        $sub = $subscription->where('user_id', $user->id)->where('sub_lolhow_id', $subLolhow->id)->first();
        if (!$sub) {
            $sub = new Subscription();
            $sub->user_id = $user->id;
            $sub->sub_lolhow_id = $subLolhow->id;
            $sub->save();
        }

        return Response()->json([
            'status' => 'success',
            'sub_lolhow' => $subLolhow->name
        ], 200);
    }

    public function unsubscribe($name, Request $request, subLolhow $subLolhow, Subscription $subscription)
    {
        $subLolhow = $subLolhow->select('name', 'id')->where('name', $name)->first();
        if (!$subLolhow) {
            return Response()->json([
                'error' => "sublolhow doesn't exist"
            ], 404);
        }

        $user = Auth::guard('api')->user();

        $sub = $subscription->where('user_id', $user->id)->where('sub_lolhow_id', $subLolhow->id)->first();
        if ($sub) {
            $sub->delete();
        }

        return Response()->json([
            'status' => 'success',
            'sub_lolhow' => $subLolhow->name
        ], 200);
    }
}
