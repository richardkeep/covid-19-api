<?php

namespace App\Http\Controllers;

use App\Country;

class ApiController extends Controller
{
    public function index()
    {
        return 'https://github.com/richardkeep/covid-19-api';
    }

    public function countries()
    {
        return Country::all();
    }
}
