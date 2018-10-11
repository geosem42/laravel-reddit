<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PollOption;
use App\PollResult;
use App\BetOption;
use App\UserPoll;
use App\Poll;
use App\Bet;
use DB;

class ReadPoll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:poll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will read all poll where status open and poll end date today then convert it to result or extend to 10 days poll';

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
        // Concept of this function 
        // Setp 1 : Read all poll which poll_end == today dat 
        // Step 2 : Check its poll type it is 10 Days poll or 2 Days
        // Step 3 : If 10 Days poll then 
        // Step 3.1 : Check majority ans and insert into poll result table 
        // Step 4 : If 2 Days poll
        // Step 4.1 Check this poll's parent bet has answer or not 
        // Step 4.1.1 : 
        // Step 4.1.2 : Else then extend this poll to 1o Days
        
        //$today = date('Y-m-d H:i:s');
        $today = "2018-10-23 10:13:02";
        $polls = Poll::with('bet_result', 'poll_result')->where(['poll_end' => $today, 'status' => 'open'])->get();        
        if(count($polls) > 0)
        {
            foreach ($polls as $key => $poll) {                
                $diff   = abs(strtotime($poll->poll_end) - strtotime($poll->created_at));
                $years  = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                
                if($days >= 10) // 10 Days period poll
                {
                    $results = UserPoll::select(DB::raw('count(*) as total, option_id'))->where('poll_id', $poll->id)->groupBy('option_id')->get();
                    if(count($results) > 0)
                    {
                        $result_count  = 0;
                        $result_option = 0;
                        foreach ($results as $key => $result) {
                            if($result->total > $result_count)
                            {
                                $result_count  = $result->total;
                                $result_option = $result->option_id;
                            }
                        }
                        $pollresult = new PollResult();
                        $pollresult->poll_id   = $poll->id;
                        $pollresult->choise_id = $result_option;
                        if($pollresult->save())
                        {
                            $pollupdate = Poll::find($poll->id);
                            $pollupdate->status = 'closed';
                            if($pollupdate->save())
                            {
                                echo "Poll id - " . $poll->id . " Result announced.";
                                echo "\n";
                            }
                        }
                    }
                    else
                    {
                        echo "Poll id - " . $poll->id . " has no result";
                        echo "\n";
                    }
                }
                else // 2 Days period poll
                {
                    if($days == 2)
                    {
                        if(count($poll['bet_result']) > 0)
                        {
                            $results = UserPoll::select(DB::raw('count(*) as total, option_id'))->where('poll_id', $poll->id)->groupBy('option_id')->get();
                            if(count($results) > 0)
                            {
                                $result_count  = 0;
                                $result_option = 0;
                                foreach ($results as $key => $result) {
                                    if($result->total > $result_count)
                                    {
                                        $result_count  = $result->total;
                                        $result_option = $result->option_id;
                                    }
                                }                                
                            }
                            $poll_option = PollOption::select('choise')->where('id', $result_option)->first();
                            $bet_option  = BetOption::select('choice')->where('id', $poll['bet_result']->choise_id)->first();
                            // Check poll result and bet creator result both same then declare result
                            if(trim($poll_option->choise) == trim($bet_option->choice))
                            {
                                $pollresult = new PollResult();
                                $pollresult->poll_id   = $poll->id;
                                $pollresult->choise_id = $result_option;
                                if($pollresult->save())
                                {
                                    $pollupdate = Poll::find($poll->id);
                                    $pollupdate->status = 'closed';
                                    if($pollupdate->save())
                                    {
                                        echo "Poll id - " . $poll->id . " Result announced.";
                                        echo "\n";
                                    }
                                }
                            }
                            else // Result not then dispute and poll will be extend for 10 days
                            {
                                $pollOptionDetails  = PollOption::select('id')->where('choise', $bet_option->choice)->first();
                                $bet_poll_option_id = $pollOptionDetails->id;
                                
                                $poll_result = UserPoll::select(DB::raw('count(*) as total, option_id'))->where('poll_id', $poll->id)->groupBy('option_id')->get();
                                $poll_result_count = UserPoll::where('poll_id', $poll->id)->count();
                                if(count($poll_result) > 0)
                                {
                                    $percentagefordispute = 0;
                                    foreach ($poll_result as $key => $poll_result) {
                                        if($bet_poll_option_id != $poll_result->option_id)
                                        {
                                            $percentage = $poll_result->total * 100 / $poll_result_count;
                                            $percentagefordispute = $percentagefordispute + $percentage;
                                        }
                                    }
                                    if($percentagefordispute > 20)
                                    {
                                        $pollupdate = Poll::find($poll->id);
                                        $pollupdate->poll_end = date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                                        if($pollupdate->save())
                                        {
                                            echo "Poll id - " . $poll->id . " +10 Days from " . $poll->poll_end.  " to " . date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                                            echo "\n";
                                        }
                                    }
                                }
                                else
                                {
                                    $pollupdate = Poll::find($poll->id);
                                    $pollupdate->poll_end = date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                                    if($pollupdate->save())
                                    {
                                        echo "Poll id - " . $poll->id . " +10 Days from " . $poll->poll_end.  " to " . date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                                        echo "\n";
                                    }
                                }
                            }
                        }
                        else
                        {
                            $pollupdate = Poll::find($poll->id);
                            $pollupdate->poll_end = date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                            if($pollupdate->save())
                            {
                                echo "Poll id - " . $poll->id . " +10 Days from " . $poll->poll_end.  " to " . date( "Y-m-d H:i:s", strtotime( $poll->poll_end ) + 240 * 3600 );
                                echo "\n";
                            }
                        }
                    }
                    else
                    {
                        echo "Poll id - " . $poll->id . " has " . $days. " poll days";
                        echo "\n";
                    }
                }
            }
        }
        else
        {
            echo "No poll found which ( end date and time == current date and time )";
            echo "\n";
        }
    }
}
