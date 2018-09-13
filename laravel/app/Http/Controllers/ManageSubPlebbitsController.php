<?php

namespace App\Http\Controllers;

use App\subPlebbit;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Moderator;
use Illuminate\Validation\Factory as ValidationFactory;
use Validator;
use Image;

class ManageSubPlebbitsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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


    public function getNewSubPlebbit()
    {
        return view('subPlebbits.newSubPlebbit');
    }

    public function postNewSubPlebbit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:sub_plebbits|max:60|regex:/(^[A-Za-z0-9\.\,\+\-\?\! ]+$)+/|min:3|alpha_dash',
            'title' => 'required|min:3|max:100',
            'description' => 'required|max:2000',
            'social_description' => 'max:200',
        ]);

        $plebbit = new subPlebbit();
        $plebbit->name = $request->input('name');
        $plebbit->title = $request->input('title');
        $plebbit->description = preg_replace("/(\r?\n){2,}/", "\n\n",$request->input('description'));
        if (!empty($request->input('social_description'))) {
            $plebbit->description_social = $request->input('social_description');
        }
        $plebbit->header_type = 'repeat';
        $plebbit->owner_id = Auth::user()->id;
        $plebbit->save();

        (new \App\Moderator)->create([
            'user_id' => $request->user()->id,
            'sub_plebbit_id' => $plebbit->id
        ]);

        return redirect('/p/'.$plebbit->name);
    }

    public function getEditPlebbit($name, Request $request, subPlebbit $subPlebbit, Moderator $moderator)
    {
        $user = Auth::user();
        $plebbit = $subPlebbit->where('name', $name)->first();
        if (!$plebbit) {
            flash("This subplebbit does not exist therefore you can't edit it", 'danger');
            return redirect('/');
        }

        $check = env('ADMIN_ID') == $user->id;

        if (!$check && $plebbit->owner_id !== $user->id) {
            flash("You are not allowed to edit /p/".$plebbit->name, 'danger');
            return redirect('/');
        }

        $mods = $moderator->getBySubPlebbitId($plebbit->id);
        $mods_string = '';
        foreach ($mods as $mod) {
            $mods_string.=$mod->username . ',';
        }

        return view('subPlebbits.editSubPlebbit', array('plebbit' => $plebbit, 'mods' => $mods_string));
    }

    public function postEditPlebbit($name, Request $request, subPlebbit $subPlebbit, Moderator $moderator)
    {
        $user = Auth::user();
        $plebbit = $subPlebbit->where('name', $name)->first();
        if (!$plebbit) {
            flash("This subplebbit does not exist therefore you can't edit it", 'danger');
            return redirect('/');
        }

        $check = env('ADMIN_ID') == $user->id;

        if (!$check && $plebbit->owner_id !== $user->id) {
            flash("You are not allowed to edit /p/".$plebbit->name, 'danger');
            return redirect('/');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:100',
            'description' => 'max:2000',
            'social_description' => 'max:200',
            'moderator' => 'moderator|moderator_valid',
            'header' => 'mimes:jpeg,jpg,png,PNG,JPG,JPEG | max:3000',
            'icon' => 'mimes:jpeg,jpg,png,PNG,JPG,JPEG | max:3000',
            'header_color' => 'max:6|regex:/(^[A-Za-z0-9\.\,\+\-\?\! ]+$)+/',
            'color' => 'max:6|regex:/(^[A-Za-z0-9\.\,\+\-\?\! ]+$)+/'
        ]);


        if ($validator->fails())
        {
            return redirect('/p/'.$plebbit->name . '/edit')->withErrors($validator)->withInput();
        }

        $header = $request->file('header');
        if ($header) {
            if ($plebbit->header) {
                unlink('images/plebbits/headers/' . $plebbit->header);
            }
            $newName = $plebbit->name . '-' . str_random(4) . '.' . $header->getClientOriginalExtension();
            $header->move('images/plebbits/headers/', $newName);
            $plebbit->header = $newName;
        }
        $icon = $request->file('icon');
        if ($icon) {
            if ($plebbit->icon) {
                unlink('images/plebbits/icons/' . $plebbit->icon);
            }
            $randomHash =  substr($icon->getClientOriginalName(), 0, 10) . str_random(40);
            $newName = 'images/plebbits/icons/' . $randomHash . '.png';
            Image::make($icon->getRealPath())->fit(107, 59)->save($newName);
            $plebbit->icon = $randomHash . '.png';
        }

        if ($request->input('delete_header') == 'on') {
            if ($plebbit->header) {
                unlink('images/plebbits/headers/' . $plebbit->header);
            }
            $plebbit->header = null;
        }
        if ($request->input('delete_icon') == 'on') {
            if ($plebbit->icon) {
                unlink('images/plebbits/icons/' . $plebbit->icon);
            }
            $plebbit->icon = null;
        }
        if ($request->input('header_type') == 'on') {
            $plebbit->header_type = 'fit';
        } else {
            $plebbit->header_type = 'repeat';
        }

        if (!empty($request->input('title'))) {
            $plebbit->title = $request->input('title');
        }
        if (!empty($request->input('description'))) {
            $plebbit->description = preg_replace("/(\r?\n){2,}/", "\n\n", $request->input('description'));
        }
        if (!empty($request->input('social_description'))) {
            $plebbit->description_social = $request->input('social_description');
        }
        if (!empty($request->input('moderator'))) {
            $mods = explode(',', $request->input('moderator'));
            $user_ids = array();
            foreach ($mods as $m) {
                $user = User::where('username', $m)->first();
                array_push($user_ids, $user->id);
                $added = $moderator->where('user_id', $user->id)->where('sub_plebbit_id', $plebbit->id)->first();
                if (!$added) {
                    $mod = new Moderator();
                    $mod->user_id = $user->id;
                    $mod->sub_plebbit_id = $plebbit->id;
                    $mod->save();
                }
            }
            $moderator->whereNotIn('user_id', $user_ids)->where('sub_plebbit_id', $plebbit->id)->delete();
        }
        if (!empty($request->input('header_color'))) {
            $plebbit->header_color = '#' . $request->input('header_color');
        }
        if (!empty($request->input('color'))) {
            $plebbit->color = '#' . $request->input('color');
        }
        $plebbit->save();

        return redirect('/p/' . $plebbit->name . '/edit');
    }

    public function getEditPlebbitCss($name, Request $request, subPlebbit $subPlebbit)
    {
        $user = Auth::user();
        $plebbit = $subPlebbit->where('name', $name)->first();
        if (!$plebbit) {
            flash("This subplebbit does not exist therefore you can't edit it", 'danger');
            return redirect('/');
        }
        $check = env('ADMIN_ID') == $user->id;

        if (!$check && $plebbit->owner_id !== $user->id) {
            flash("You are not allowed to edit /p/".$plebbit->name, 'danger');
            return redirect('/');
        }

        return view('subPlebbits.edit_css', array('plebbit' => $plebbit));
    }

    public function postEditPlebbitCss($name, Request $request, subPlebbit $subPlebbit)
    {
        $user = Auth::user();
        $plebbit = $subPlebbit->where('name', $name)->first();
        if (!$plebbit) {
            flash("This subplebbit does not exist therefore you can't edit it", 'danger');
            return redirect('/');
        }
        $check = env('ADMIN_ID') == $user->id;

        if (!$check && $plebbit->owner_id !== $user->id) {
            flash("You are not allowed to edit /p/".$plebbit->name, 'danger');
            return redirect('/');
        }

        $css = $request->input('custom_css');
        $len  = strlen($request);
        if ($len < 1000000) {
            $urls = $this->getExternalSources($css);
            if ($this->getCharsets($css) !== false) {
                flash("@charset not allowed", 'danger');
                return redirect('/p/' . $plebbit->name . '/edit/css');
            }
            if ($this->getImport($css) !== false) {
                flash("@import not allowed", 'danger');
                return redirect('/p/' . $plebbit->name . '/edit/css');
            }
            if ($this->getNamespace($css) !== false) {
                flash("@namespace not allowed", 'danger');
                return redirect('/p/' . $plebbit->name . '/edit/css');
            }
        } else {
            flash("Exceeded maximum of 1mil characters", 'danger');
            return redirect('/p/' . $plebbit->name . '/edit/css');
        }

        $allowed_hosts = array('i.imgur.com', 'imgur.com', 'plebbit.net');
        $badurls = array();

        foreach ($urls as $url) {
            $url = str_replace("'", '', $url);
            $url = str_replace('"', '', $url);
            $host = parse_url($url);
            if (isset($host['host'])) {
               if (!in_array($host['host'], $allowed_hosts)) {
                   array_push($badurls, $url);
               }
            }
        }

        if (count($badurls) > 0) {
            flash("External css resources from non whitelisted hosts not allowed", 'danger');
            return redirect('/p/'.$plebbit->name.'/edit/css');
        }

        $plebbit->custom_css = htmlspecialchars($css);
        try {
            $plebbit->save();
        } catch (QueryException $e) {
            flash("Exceeded maximum of 16mil characters", 'danger');
            return redirect('/p/'.$plebbit->name.'/edit/css');
        }
        flash("Custom CSS saved", 'success');
        return redirect('/p/'.$plebbit->name.'/edit/css');
    }

    public function loadcss($name, Request $request, Response $response, subPlebbit $subPlebbit)
    {
        $subPlebbit = $subPlebbit->select('custom_css')->where('name', $name)->first();
        if (!$subPlebbit) {
            return response('', 404);
        }
        return response($subPlebbit->custom_css, 200)
            ->header('Content-Type', 'text/css');
    }

    public function getCharsets($input_string) {
        $matches = stripos($input_string, 'charset');
        return $matches;
    }

    public function getImport($input_string) {
        $matches = stripos($input_string, 'import');
        return $matches;
    }

    public function getNamespace($input_string) {
        $matches = stripos($input_string, 'namespace');
        return $matches;
    }

    public function getExternalSources($input)
    {
       $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
       $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
       $regex .= "([a-z0-9-.]*)\.([a-z]{2,4})"; // Host or IP
       $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
       $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor


       $matches = array(); //create array
       $pattern = "/$regex/";

       preg_match_all($pattern, $input, $matches);
       return $matches[0];
    }

}
