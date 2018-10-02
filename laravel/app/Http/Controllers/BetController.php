<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Bet;
use Redirect;

class BetController extends Controller
{
    public function __construct(ValidationFactory $validationFactory)
    {
        $this->middleware('auth', ['except' => ['loadcss']]);

        $validationFactory->extend(
            'moderator',
            function ($attribute, $value, $parameters) {
                if (empty($value)) {
                    return true;
                }
                $mods = explode(',', $value);
                if (count($mods) > 10) {
                    return false;
                } else {
                    return true;
                }
            },
            'Not more than 10 moderators allowed.'
        );

        $validationFactory->extend(
            'moderator_valid',
            function ($attribute, $value, $parameters) {
                if (empty($value)) {
                    return true;
                }
                $mods = new Moderator();
                return $mods->validateMods($value);
            },
            'Make sure all mod usernames are valid'
        );

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bet.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title'           => 'required|unique:bets',
                'description'     => 'required',
                'betting_closes'  => 'required',
                'resolution_paid' => 'required',
                'initial_bet'     => 'required|min:2',
                'fee'             => 'required'
            ],
            $messsages = array(
                            'initial_bet.min' => 'The initial bet must be at least 10.'
                        )
        );
         
        $bet                  = new Bet();
        $bet->user_id         = Auth::user()->id;
        $bet->title           = $request->title;
        $bet->description     = $request->description;
        $bet->betting_closes  = $request->betting_closes;
        $bet->resolution_paid = $request->resolution_paid;
        $bet->initial_bet     = $request->initial_bet;
        $bet->fee             = $request->fee;
        if($bet->save())
        {
            return \Redirect::back()->with('success', 'Bets has been successfully added!');
        }
        else
        {
            return \Redirect::back()->with('warning', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
