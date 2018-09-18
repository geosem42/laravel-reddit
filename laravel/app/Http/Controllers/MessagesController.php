<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Messaging;
use Illuminate\Validation\Factory as ValidationFactory;
use Intervention\Image\Exception\NotReadableException;
use Validator;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ValidationFactory $factory)
    {
        $this->middleware('auth');

        $factory->extend(
            'username',
            function ($attribute, $value, $parameters) {
                $users = new User();
                $user = $users->select('id')->where('username', $value)->first();

                if ($user) {
                    if (Auth::user()->id  == $user->id) {
                        return false;
                    }
                    return true;
                } else {
                    return false;
                }
            },
            'This user does not exist or is not allowed'
        );
        $factory->extend(
            'pm_cooldown',
            function ($attribute, $value, $parameters) {
                $users = new User();
                $user = Auth::user();

                $messaging = new Messaging();
                $last_pm = $messaging->select('last_pm_timestamp')->where('user_id', $user->id)->orderBy('last_pm_timestamp', 'desc')->first();
                if (!$last_pm) {
                    return true;
                }
                if (time() - 30 < $last_pm->last_pm_timestamp) {
                    return false;
                }
                return true;
            },
            "You're going to fast please wait 30sec between sending pm's"
        );
    }

    public function inbox(Request $request, Messaging $messaging)
    {
        $user = $request->user();

        $messages = $messaging->select('username as from', 'code', 'subject', 'private_messages.active', 'private_messages.created_at')
            ->join('users', 'private_messages.user_id', '=', 'users.id')
            ->where('to_user_id', $user->id)
            ->orderBy('private_messages.last_pm_timestamp', 'desc')->paginate(50);

        return view('messaging.inbox')->with([
            'messages' => $messages
        ]);
    }

    public function ViewMessage($code, Request $request, Messaging $messaging)
    {
        $message = $messaging->select('id', 'code', 'main_msg_id', 'active')->where('code', $code)->where('to_user_id', $request->user()->id)->first();
        if (!$message) {
            flash("Message not found", 'danger');
            return redirect(route('messages.inbox'));
        }
        if (!$message->main_msg_id) {
            flash("Sorry, something wen't wrong while loading your messages", 'danger');
            return redirect(route('messages.inbox'));
        }
        $message->active = 0;
        $message->save();

        $messages = $messaging->select('username as from', 'subject', 'message', 'user_id', 'private_messages.created_at')
            ->join('users', 'private_messages.user_id', '=', 'users.id')
            ->where('main_msg_id', $message->main_msg_id)
            ->orderBy('created_at', 'asc')->paginate(200);

        $messaging->select('active')->where('to_user_id', $request->user()->id)->where('main_msg_id', $message->main_msg_id)->update(['active' => false]);

        return view('messaging.view_message')->with([
            'messages' => $messages,
            'user' => $request->user()
        ]);
    }

    public function ReplyMessage($code, Request $request, Messaging $messaging)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|min:10|max:5000|pm_cooldown'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = $request->user();
        $pm = $messaging->select('id', 'subject', 'code', 'user_id', 'to_user_id', 'main_msg_id')->where('code', $code)->where('to_user_id', $user->id)->first();
        if (!$pm) {
            flash("Message not found", 'danger');
            return redirect(route('messages.inbox'));
        }
        $messages = $messaging->where('main_msg_id', $pm->main_msg_id)->get();

        $messaging->create([
            'code' => $messaging->getCode(),
            'user_id' => $user->id,
            'to_user_id' => $pm->user_id,
            'main_msg_id' => $pm->main_msg_id,
            'subject' => 'Re: ' . $messages->where('id', $pm->main_msg_id)->first()->subject,
            'message' => preg_replace("/(\r?\n){2,}/", "\n\n", $request->input('reply')),
            'active' => 1,
            'last_pm_timestamp' => time()
        ]);

        return redirect()->back();
    }

    public function GetSendMessage(Request $request)
    {
        $username = $request->segment(3);

        return view('messaging.send_message')->with([
            'username' => $username
        ]);
    }

    public function PostSendMessage(Request $request, Messaging $messaging, User $user)
    {
        $validator = Validator::make($request->all(), [
            'subject' => "required|min:5:max:150|regex:/(^[A-Za-z0-9\.\,\+\-\?\! ]+$)+/",
            'message' => 'required|min:10|max:5000',
            'to' => 'required|username'
        ]);
        if ($validator->fails()) {
            return redirect(route('messages.send'))->withErrors($validator)->withInput();
        }

        $toUser = $user->where('username', $request->input('to'))->first();

        $msg = $messaging->create([
            'code' => $messaging->getCode(),
            'user_id' => $request->user()->id,
            'to_user_id' => $toUser->id,
            'subject' => $request->input('subject'),
            'message' => preg_replace("/(\r?\n){2,}/", "\n\n", $request->input('message')),
            'active' => true,
            'last_pm_timestamp' => time()
        ]);

        $msg->main_msg_id = $msg->id;
        $msg->save();

        flash('Your message has been sent', "success");
        return redirect(route('messages.inbox'));
    }

    public function MarkAllRead(Request $request, Messaging $messaging)
    {
        $messaging->where('to_user_id', $request->user()->id)->update(['active' => false]);
        flash("All your messages have been marked as read", 'success');
        return redirect(route('messages.inbox'));
    }
}
