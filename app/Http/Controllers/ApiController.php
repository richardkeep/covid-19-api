<?php

namespace App\Http\Controllers;

use App\Country;
use App\Summary;

class ApiController extends Controller
{
    public function index()
    {
        return 'https://github.com/richardkeep/covid-19-api';
    }

    public function all()
    {
        return Summary::first();
    }

    public function countries()
    {
        return Country::all();
    }
}
