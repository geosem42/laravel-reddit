<?php

namespace App\Http\Controllers;

use App\Subreddit;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SubredditController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $subreddit = Subreddit::latest('created_at')->paginate(3);

        return view('subreddit/index')->with('subreddit', $subreddit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('subreddit/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\SubredditRequest|Request $request
     * @return Response
     */
    public function store(Requests\SubredditRequest $request)
    {
        Auth::user()->subreddit()->create($request->all());

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @internal param Subreddit $subreddit
     * @param Subreddit $subreddit
     * @return $this
     */
    public function show(Subreddit $subreddit)
    {
        $subreddit = Subreddit::with('posts.votes')->findOrFail($subreddit->id);

        return view('subreddit/show')->with('subreddit', $subreddit);
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
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
