<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PollOption;
use App\BetResult;
use App\Thread;
use App\Poll;
use App\Bet;
use DB;

class ReadBets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:bets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This jon will read all bets and create poll based on bets';

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
//        $today = '2018-10-16 05:59';
        
        $pollArray = array();

        DB::beginTransaction();
        
        // Get all open bets and check resolution paid + 48 hrs with today date
        $bets = Bet
//                ::where(['resolution_paid' => $today, 'status' => 'open'])
                ::where(DB::raw("DATE_FORMAT(resolution_paid,'%Y-%m-%d %H:%i')"), $today)
                ->where('status','open')
                ->get();
        
        if(count($bets) > 0)
        {
            foreach ($bets as $key => $value) {
                if(strtotime($value->resolution_paid) == strtotime($today))
                {
                    $pollArray[$key] = $value->id;
                }
            }

            // Check bet creator has announced result or not
            $createPoll = array();
            foreach ($pollArray as $key => $value) {
                //$result     = BetResult::where('bet_id',$value)->count();
                $betDetails = Bet::with('options')->find($value);
                $sublol_id  = Thread::select('sub_lolhow_id')->where(['type' => 'bet', 'id' => $betDetails->thread_id])->first();
                
                if(count($betDetails) > 0 && count($sublol_id) > 0)
                {
                    // Create thread to display in listing                     
                    $thread              = new Thread();
                    $thread->code        = $thread->getCode();
                    $thread->title       = $betDetails->title;
                    $thread->post        = $betDetails->title;
                    $thread->poster_id   = $betDetails->user_id;
                    $thread->sub_lolhow_id = $sublol_id->sub_lolhow_id;
                    $thread->reply_count = 0;
                    $thread->upvotes     = 0;
                    $thread->downvotes   = 0;
                    $thread->score       = 0;
                    $thread->type        = 'poll';
                    $thread->sticky      = 0;
                    $thread->save();

                    // Create poll
                    $pollEndTime    = "+".(24 * intValue(config('settings.firstBetToPollDays'))). " hours";
                    
                    $poll                = new Poll();       
                    $poll->user_id       = $betDetails->user_id;
                    $poll->thread_id     = $thread->id;
                    $poll->bet_id        = $value;
                    $poll->sublolhow_id  = $sublol_id->sub_lolhow_id;
                    $poll->title         = $betDetails->title;
                    $poll->description   = $betDetails->description;
                    $poll->poll_end      = date("Y-m-d H:i:s", strtotime($pollEndTime)); // 2 Days poll
                    $poll->minimum_karma = config('settings.minKarmaValueForPoll');
                    $poll->suggestion    = 'yes';
                    $poll->status        = 'open';
                    $poll->type          = 'review';
                    $poll->save();

                    //Update bet status to closed
                    $bet = Bet::find($value);
                    $bet->status = 'closed';
                    $bet->save();                    
                    
                    if(count($betDetails['options']) > 0)
                    {
                        foreach ($betDetails['options'] as $key => $option) {
                            $polloption = new PollOption();
                            $polloption->poll_id = $poll->id;
                            $polloption->bet_option_id = $option->id;
                            $polloption->choise =  $option->choice;
                            $polloption->save();
                        }
                    }
                    else
                    {
                        echo "This bet ( $value ) has not option";
                        echo "\n";
                    }             
                }
                else
                {
                    echo "Bet ( $value ) information not found.";
                    echo "\n";
                }
            }

            DB::commit(); 
            echo "Bet read and Poll created.";
            echo "\n";
        }
        else
        {
            echo "Bet not found which ( resolution paid time == current time )";
            echo "\n";
        } 
    }
}
