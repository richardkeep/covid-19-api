<?php

$router->get('/', 'ApiController@index');

$router->get('/all', 'ApiController@summary');
$router->get('/countries', 'ApiController@countries');
