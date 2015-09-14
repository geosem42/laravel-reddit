<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class ContactController extends BaseController {

    public function index() {
        return view('contact');
    }
}

