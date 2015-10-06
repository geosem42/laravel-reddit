<?php

namespace App\Http\Controllers;

use App\Moderator;
use App\Http\Requests;
use App\Subreddit;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\Gate;

class ModeratorsController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'destroy']]);
    }

    public function create(Subreddit $subreddit, User $user)
    {
        if(Gate::denies('update-sub', $subreddit)) {
            Session::flash('message_danger', 'You are not allowed to do that.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('mysubreddits');
        } else {
            $subreddit = Subreddit::with('user')->findOrFail($subreddit->id);

            $moderators = Moderator::where('subreddit_id', '=', $subreddit->id)->get();

            return view('subreddit/moderators/create')->with(compact('subreddit', 'moderators'));
        }
    }

    public function store(Requests\ModeratorRequest $request, Subreddit $subreddit, User $user)
    {
        if(Gate::denies('update-sub', $subreddit)) {

        } elseif(Moderator::where('user_id', '=', Input::get('user_id'))->where('subreddit_id', '=', $subreddit->id)->count() > 0) {
            Session::flash('message_info', 'User is already a moderator of this subreddit.');
            Session::flash('alert-class', 'alert-warning');
            return redirect('subreddit/' . $subreddit->id . '/moderators/create');
        } else {
            $moderator = new Moderator;
            $moderator->user_id = Input::get('user_id');
            $moderator->subreddit_id = $subreddit->id;
            $moderator->save();
        }

        if($moderator) {
            Session::flash('message', 'Moderator has been added.');
            Session::flash('success-class', 'alert-success');
        }


        return redirect('subreddit/' . $subreddit->id . '/moderators/create');
    }

    public function destroy(Subreddit $subreddit, Moderator $moderator)
    {
        $mod = Moderator::where('subreddit_id', $subreddit->id)
            ->where('user_id', $moderator->user_id)->first();
        $mod->delete();

        Session::flash('message', 'Moderator has been deleted.');
        Session::flash('alert-class', 'alert-success');

        return redirect('subreddit/' . $subreddit->id . '/moderators/create');
    }

    public function getUsers($query = '') {
        $q = User::select('id', 'name');
        if ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        }
        return Response::json($q->get());
    }
}
