<?php

$router->get('/', 'ApiController@index');
$router->get('/all', 'ApiController@all');
$router->get('/countries', 'ApiController@countries');
