<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected static $titles = [
        'id', 'country', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical', 'emoji'
    ];

    private static function generateEmoji($country = 'YE')
    {
        $flag = file_get_contents(__DIR__ . '/flags.json');
        return collect(json_decode($flag, true))->firstWhere('name', $country);
    }

    private static function generateCode($code)
    {
        $flag = file_get_contents(__DIR__ . '/flags.json');
        return collect(json_decode($flag, true))->firstWhere('code', $code);
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
            'id' => 'asc',
            'deaths' => 'desc',
            'cases' => 'desc',
            'recovered' => 'desc',
        ]);

        return collect($data)
            ->slice(5)
            ->reject(function ($item) {
                return $item[0] == 0;
            })
            ->reject(function ($item) {
                return $item[1] == "";
            })->reject(function ($item) {
                return $item[1] == "Africa";
            })->reject(function ($item) {
                return $item[1] == "Oceania";
            })
            ->map(function ($item) {
                foreach (static::$titles as $key => $value) {
                    $data[$value] = in_array($value, ['country', 'emoji']) ? $item[$key] : intval($item[$key]);
                }
                $data['emoji'] = static::generateEmoji($item[1])['emoji'];
                return $data;
            })
            ->sort($sorter)
            ->values()
            ->all();
    }


    public static function apicountry($country)
    {
        $data = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/')
            ->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $j) {
                    return str_replace(',', '', trim($td->text()));
                });
            });
        $data['countryRequest'] = static::generateCode($country)['name'];
        return collect($data)
            ->slice(5)
            ->reject(function ($item) use ($data) {
                return $item[1] != $data['countryRequest'];
            })
            ->map(function ($item) {
                foreach (static::$titles as $key => $value) {
                    $data[$value] = in_array($value, ['country', 'emoji']) ? $item[$key] : intval($item[$key]);
                }
                $data['emoji'] = static::generateEmoji($item[1])['emoji'];
                return $data;
            })
            ->values()
            ->all();
    }
}
