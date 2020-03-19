<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function index()
    {
        return '¯\_(ツ)_/¯';
    }

    public function countries()
    {
        return Cache::remember('COVID19', Carbon::parse('10 minutes'), function () {
            return Country::all()->pop()->all();
        });
    }
}
