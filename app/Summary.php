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
        $item = static::crawl();

        foreach (static::$titles as $key => $value) {
            $data[$value] = $value == 'title' ? $item[$key] : intval($item[$key]);
        }

        return $data;
    }

    private static function crawl()
    {
        $crawler = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/');

        $data = $crawler->filter('#main_table_countries_today')->filter('tr.total_row')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return str_replace(',', '', trim($td->text()));
            });
        });

        return $data[0];
    }
}
