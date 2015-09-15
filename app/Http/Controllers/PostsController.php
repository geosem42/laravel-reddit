<?php

namespace App\Http\Controllers;

use App\User;
use App\Vote;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use App\Subreddit;
use Embed\Embed;
use Image;

class PostsController extends Controller
{
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

        return view('post/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest|Request $request
     * @return Response
     */
    public function store(PostRequest $request)
    {
        if (Input::has('link')) {
            $input['link'] = Input::get('link');
            $info = Embed::create($input['link']);

            $image = \Image::make($info->image)->resize(120, 120)->save('C:\xampp\htdocs\laravel-5\public\images' . '/' . str_random(8) . '.jpg');

            $embed_data = ['text' => $info->description, 'image' => $image->filename . '.jpg'];

            //Auth::user()->posts()->create(array_add($request->all(), 'image', $info->image));

            Auth::user()->posts()->create(array_merge($request->all(), $embed_data));

            return redirect('/articles');
        }
        Auth::user()->posts()->create($request->all());

        return redirect('/');
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
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function autocomplete(){

    }
}
