<?php

namespace App\Http\Controllers;

use App\Moderator;
use App\Http\Requests;
use App\Subirt;
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

    public function create(Subirt $subirt, User $user)
    {
        if(Gate::denies('update-sub', $subirt)) {
            Session::flash('message_danger', 'You are not allowed to do that.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('mysubirts');
        } else {
            $subirt = Subirt::with('user')->findOrFail($subirt->id);

            $moderators = Moderator::where('subirt_id', '=', $subirt->id)->get();

            return view('subirt/moderators/create')->with(compact('subirt', 'moderators'));
        }
    }

    public function store(Requests\ModeratorRequest $request, Subirt $subirt, User $user)
    {
        if(Gate::denies('update-sub', $subirt)) {

        } elseif(Moderator::where('user_id', '=', Input::get('user_id'))->where('subirt_id', '=', $subirt->id)->count() > 0) {
            Session::flash('message_info', 'User is already a moderator of this subirt.');
            Session::flash('alert-class', 'alert-warning');
            return redirect('subirt/' . $subirt->id . '/moderators/create');
        } else {
            $moderator = new Moderator;
            $moderator->user_id = Input::get('user_id');
            $moderator->subirt_id = $subirt->id;
            $moderator->save();
        }

        if($moderator) {
            Session::flash('message', 'Moderator has been added.');
            Session::flash('success-class', 'alert-success');
        }


        return redirect('subirt/' . $subirt->id . '/moderators/create');
    }

    public function destroy(Subirt $subirt, Moderator $moderator)
    {
        $mod = Moderator::where('subirt_id', $subirt->id)
            ->where('user_id', $moderator->user_id)->first();
        $mod->delete();

        Session::flash('message', 'Moderator has been deleted.');
        Session::flash('alert-class', 'alert-success');

        return redirect('subirt/' . $subirt->id . '/moderators/create');
    }

    public function getUsers($query = '') {
        $q = User::select('id', 'name');
        if ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        }
        return Response::json($q->get());
    }
}
