<?php

namespace App\Http\Controllers;

use App\Country;
use App\Summary;

class ApiController extends Controller
{
    public function index()
    {
        return redirect('https://github.com/amolood/covid-19-api');
    }

    public function summary()
    {
        return Summary::api();
    }

    public function country($country)
    {
        return Country::apicountry($country);
    }

    public function countries()
    {
        return Country::api();
    }
}
