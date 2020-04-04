<?php

$router->get('/', 'ApiController@index');

// Cached for 10 minutes
$router->get('/all', 'ApiController@summary');
$router->get('/countries', 'ApiController@countries');
