<?php

use App\Country;
use App\Http\Controllers\ApiController;

$router->get('/', 'ApiController@index');

// Cached for 10 minutes
$router->get('/all', 'ApiController@summary');
$router->get('/countries', 'ApiController@countries');
$router->get('country/{country}', function ($country) {
    return Country::apicountry($country);
});

