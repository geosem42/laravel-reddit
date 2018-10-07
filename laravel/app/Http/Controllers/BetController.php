<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Thread;
use Validator;
use App\subLolhow;
use App\BetOption;
use App\UserBet;
use App\User;
use App\Arrow;
use App\Bet;
use Redirect;
use DB;

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
        $validator = Validator::make($request->all(),
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
         
        if ($validator->fails())
        {
            return redirect('/submit?type=bet')->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        $subLolhow = subLolhow::where('name', $request->sublolhow)->first();
        if(empty($subLolhow)) {
            return redirect('/submit?type=bet')->with('warning', 'Sublolhow not exits');
        }

        $thread                = new Thread();
        $thread->title         = $request->title;
        $thread->code          = $thread->getCode();
        $thread->type          = 'bet';
        $thread->post          = $request->description;
        $thread->poster_id     = Auth::user()->id;
        $thread->sub_lolhow_id = $subLolhow->id;
        $thread->save();
        
        $bet                  = new Bet();
        $bet->user_id         = Auth::user()->id;
        $bet->thread_id       = $thread->id;
        $bet->title           = $request->title;
        $bet->description     = $request->description;
        $bet->betting_closes  = date('Y-m-d H:i:s', strtotime("$request->betting_closes $request->timzone_bc"));
        $bet->resolution_paid = date('Y-m-d H:i:s', strtotime("$request->resolution_paid $request->timzone_rp"));
        $bet->initial_bet     = $request->initial_bet;
        $bet->fee             = $request->fee;
        if($bet->save())
        {
            $optionsArr  = explode(',', $request->options);
            $optionBatch = array();
            if(count($optionsArr) > 0) {
                foreach ($optionsArr as $key => $value) {
                    if(isset($value) && $value != '') {
                        $optionBatch[$key]['choice']     = trim($value);
                        $optionBatch[$key]['bet_id']     = $bet->id;
                        $optionBatch[$key]['created_at'] = date('Y-m-d H:i:s');
                        $optionBatch[$key]['updated_at'] = date('Y-m-d H:i:s');
                    }
                }
            }
            
            BetOption::insert($optionBatch);
            DB::commit();
            return redirect('/submit?type=bet')->with('success', 'Bets has been successfully added!');
            //return \Redirect::back()->with('success', 'Bets has been successfully added!');
        }
        else
        {
            return redirect('/submit?type=bet')->with('warning', 'Something went wrong. Please try again.');
            //return \Redirect::back()->with('warning', 'Something went wrong. Please try again.');
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

    public function submitbet(Request $request)
    {
        $request->validate(
            [
                'bet_id'    => 'required',
                'option_id' => 'required',
                'betamount' => 'required'
            ]
        );

        $checkbet = UserBet::where(['bet_id' => $request->bet_id, 'user_id' => Auth::user()->id])->get();
        if(count($checkbet) > 0) {
            return \Redirect::back()->with('warning', 'Your have already applied on this bet.');
        }

        DB::beginTransaction();

        $userarrow = User::select('arrow')->where('id', Auth::user()->id)->first();        
        if($userarrow->arrow > 0)
        {
            $betarrow = Bet::select('initial_bet')->where('id', $request->bet_id)->first();
            if($userarrow->arrow >= $betarrow->initial_bet && $userarrow->arrow >= $request->betamount)
            {
                $arrow = new Arrow();
                $arrow->user_id     = Auth::user()->id;
                $arrow->bet_id      = $request->bet_id;
                $arrow->arrow       = '-'.$request->betamount;
                $arrow->description = 'Applied on Bet';
                if($arrow->save())
                {
                    $query = DB::select(DB::raw("SELECT SUM(`arrow`) as `arrow_count` FROM `arrows` WHERE `user_id` = " . Auth::user()->id));
                    $user = User::find(Auth::user()->id);
                    $user->arrow = $query[0]->arrow_count;
                    $user->save(); 
                }
                
                $userbet = new UserBet();
                $userbet->user_id   = Auth::user()->id;
                $userbet->bet_id    = $request->bet_id;
                $userbet->choise_id = $request->option_id;
                $userbet->amount    = $request->betamount;        

                if($userbet->save())
                {
                    DB::commit();
                    return \Redirect::back()->with('success', 'Your Bets has been successfully applied!');
                }
                else
                {
                    return \Redirect::back()->with('warning', 'Something went wrong. Please try again.');
                }
            }
            else
            {
                return \Redirect::back()->with('warning', 'You need at least '. $request->betamount .' arrow to apply this bet.');
            }
        }
        else
        {
            return \Redirect::back()->with('warning', 'You have not enough arrow to apply bet.');
        }
    }

    public function betresult(Request $request)
    {
        DB::beginTransaction();

        // get all arrow spent by all users
        $totalBetArrow = DB::table('user_bets')
                        ->select(DB::raw('SUM(amount) as total_arrow'))
                        ->where('bet_id', $request->bet_id)
                        ->get();

        // get all arrow of winner users
        $winnerArrow   = DB::table('user_bets')
                        ->select(DB::raw('SUM(amount) as total_arrow'))
                        ->where(['bet_id' => $request->bet_id, 'choise_id' => $request->option_id])
                        ->get();

        // distributes arrow                        
        $distributeArr = (int)$totalBetArrow[0]->total_arrow - (int)$winnerArrow[0]->total_arrow;

        // get all user who won this bet
        $wonusersArr   = UserBet::where(['bet_id' => $request->bet_id, 'choise_id' => $request->option_id])->get();
    
        if(!empty($wonusersArr))
        {
            foreach ($wonusersArr as $key => $user) {
                $arrowCal = $user->amount/(int)$winnerArrow[0]->total_arrow;                  
                $arrow = new Arrow();
                $arrow->user_id     = $user->user_id;
                $arrow->bet_id      = $request->bet_id;
                $arrow->arrow       = $arrowCal * $distributeArr;
                $arrow->description = 'Won arrow of bet id ' . $request->bet_id .'( you spent : '.$user->amount.' )';                
                if($arrow->save())
                {
                    $query = DB::select(DB::raw("SELECT SUM(`arrow`) as `arrow_count` FROM `arrows` WHERE `user_id` = " . $user->user_id));
                    $user = User::find($user->user_id);
                    $user->arrow = $query[0]->arrow_count;
                    $user->save(); 
                }
            }
            $bet = Bet::find($request->bet_id);
            $bet->status = 'closed';
            if($bet->save())
            {
                DB::commit();
                return \Redirect::back()->with('success', 'Result announced and arrow distributed to winner users.');   
            }
            else
            {
                return \Redirect::back()->with('warning', 'Something went wrong. Please try again.');       
            }  
        }
        else
        {
            return \Redirect::back()->with('warning', 'Something went wrong. Please try again.');
        }
    }
}