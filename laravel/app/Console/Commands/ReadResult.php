<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PollResult;
use App\BetOption;
use App\UserBet;
use App\Arrow;
use App\Poll;
use App\User;
use App\Bet;
use DB;

class ReadResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This function distribute arrow to winner user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $today = date('Y-m-d H:i');
//        $today = '2018-10-11 10:31';
        
        $polls = PollResult::with('option_name', 'poll_details')
//                ->where('updated_at', $today)
                ->where(DB::raw("DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i')"), $today)
                ->get();
        
        if(count($polls) > 0)
        {
            foreach ($polls as $key => $poll) 
            {
                $poll_bet_id      = $poll['poll_details']['bet_id'];
                $poll_option_name = $poll['option_name']['choise']; 
                $bet_option       = BetOption::where(['bet_id' => $poll_bet_id, 'choice' => $poll_option_name])->first();
                
                DB::beginTransaction();

                // get all arrow spent by all users
                $totalBetArrow = DB::table('user_bets')
                                ->select(DB::raw('SUM(amount) as total_arrow'))
                                ->where('bet_id', $poll_bet_id)
                                ->get();

                // get all arrow of winner users
                $winnerArrow   = DB::table('user_bets')
                                ->select(DB::raw('SUM(amount) as total_arrow'))
                                ->where(['bet_id' => $poll_bet_id, 'choise_id' => $bet_option->id])
                                ->get();

                // distributes arrow                        
                $distributeArr = (int)$totalBetArrow[0]->total_arrow - (int)$winnerArrow[0]->total_arrow;

                // get all user who won this bet
                $wonusersArr   = UserBet::where(['bet_id' => $poll_bet_id, 'choise_id' => $bet_option->id])->get();
            
                if(!empty($wonusersArr))
                {
                    foreach ($wonusersArr as $key => $user) {
                        $arrowCal = $user->amount/(int)$winnerArrow[0]->total_arrow;                  
                        $arrow = new Arrow();
                        $arrow->user_id     = $user->user_id;
                        $arrow->bet_id      = $poll_bet_id;
                        $arrow->arrow       = $arrowCal * $distributeArr;
                        $arrow->description = 'Won arrow of bet id ' . $poll_bet_id .'( you spent : '.$user->amount.' )';                
                        if($arrow->save())
                        {
                            $query = DB::select(DB::raw("SELECT SUM(`arrow`) as `arrow_count` FROM `arrows` WHERE `user_id` = " . $user->user_id));
                            $user = User::find($user->user_id);
                            $user->arrow = $query[0]->arrow_count;
                            $user->save(); 
                        }
                    }
                    $bet = Bet::find($poll_bet_id);
                    $bet->status = 'closed';
                    if($bet->save())
                    {
                        DB::commit();
                        echo "Poll Id - " . $poll['poll_details']['id'] . " : Result announced and arrow distributed to winner users.";
                        echo "\n";
                    }
                    else
                    {
                        echo "Poll Id - " . $poll['poll_details']['id'] . " : Error in result read cron.";
                        echo "\n";
                    }  
                }
                else
                {
                    echo "Poll Id - " . $poll['poll_details']['id'] . " :For this bet no one winner.";
                    echo "\n";
                }
            }
        }
        else
        {
            echo "No result found with end date == current time and date";
        }   
    }
}
