<?php

namespace App\Http\Controllers;

use App\User;
use App\Vote;
use App\Subreddit;
use App\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Http\Requests\VoteRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Model;


class VotesController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\VoteRequest|Request $request
     * @param $id
     * @return Response
     */
    public function store(Requests\VoteRequest $request)
    {
        // AJAX JSON RESPONSE
        $response = array(
            'status' => 'success',
            'msg' => 'Vote has been added.',
        );
        if(Auth::check()){
            \Log::info(Auth::user());
            Auth::user()->votes()->create($request->all());
        } else {
                return \Response::json('Nope');
        }

        return \Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param VoteRequest $request
     * @return Response
     * @internal param int $id
     */
    public function destroy(Requests\VoteRequest $request)
    {
        // AJAX JSON RESPONSE
        $response = array(
            'status' => 'success',
            'msg' => 'Vote has been removed.',
        );

        if(Auth::check()){
            \Log::info(Auth::user());
            Auth::user()->votes()->delete($request->all());
        } else {
            return \Response::json('Nope');
        }

        return \Response::json($response);
    }
}
