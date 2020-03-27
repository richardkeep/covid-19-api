<?php

namespace App\Http\Controllers;

use App\Country;
use App\Summary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function index()
    {
        return redirect('https://github.com/richardkeep/covid-19-api');
    }

    public function summary()
    {
        return Cache::remember('COVID19-all', Carbon::parse('10 minutes'), function () {
            return Summary::first();
        });
    }

    public function countries()
    {
        return Cache::remember('COVID19', Carbon::parse('10 minutes'), function () {
            return Country::all();
        });
    }

    public function realtimeSummary()
    {
        return Summary::first();
    }

    public function realtimeCountries()
    {
        return Country::all();
    }
}
