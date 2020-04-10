<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected static $titles = [
        'country', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];

    private static function generateEmoji($country)
    {
        $flag = file_get_contents(__DIR__.'/flags.json');

        return collect(json_decode($flag, true))->firstWhere('name', $country)['emoji'];
    }

    public static function api()
    {
        $data = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/')
            ->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $j) {
                    return str_replace(',', '', trim($td->text()));
                });
            });

        $sorter = app()->make('collection.multiSort', [
            'deaths' => 'desc',
            'cases' => 'desc',
            'recovered' => 'desc',
        ]);

        return collect($data)
            ->slice(9)
            ->reject(function ($item) {
                return $item[0] == 'Total:';
            })
            ->map(function ($item) {
                foreach (static::$titles as $key => $value) {
                    $data[$value] = in_array($value, ['country', 'emoji']) ? $item[$key] : intval($item[$key]);
                }
                $data['emoji'] = static::generateEmoji($item[0]);

                return $data;
            })
        ->sort($sorter)
        ->values()
        ->all();
    }
}
