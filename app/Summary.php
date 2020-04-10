<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected static $titles = [
        'title', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];

    public static function api()
    {
        $crawler = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/');

        $data = $crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return str_replace(',', '', trim($td->text()));
            });
        });

        $item = $data[8];

        foreach (static::$titles as $key => $value) {
            $dataa[$value] = $value == 'title' ? $item[$key] : intval($item[$key]);
        }

        return $dataa;
    }
}
