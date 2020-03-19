<?php

namespace App\Http\Controllers;

use App\Country;

class ApiController extends Controller
{
    public function index()
    {
        return '¯\_(ツ)_/¯';
    }

    public function countries()
    {
        return Country::all();
    }
}
