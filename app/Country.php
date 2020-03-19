<?php

namespace App;

use Sushi\Sushi;
use Goutte\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use Sushi;

    protected $titles = [
        'country', 'cases', 'todayCases', 'deaths',
        'todayDeaths', 'recovered', 'activeCases', 'critical',
    ];

    protected $casts = [
        'todayCases' => 'integer',
        'todayDeaths' => 'integer',
        'cases' => 'integer',
        'deaths'    => 'integer',
        'recovered' => 'integer',
        'activeCases'   => 'integer',
        'critical' => 'integer',
    ];

    public function getRows()
    {
        return Cache::remember('COVID19', Carbon::parse('10 minutes'), function () {
            return $this->getData();
        });
    }

    public function getData()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.worldometers.info/coronavirus/');

        $collection = collect($crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return trim($td->text());
            });
        }));

        // Remove the 1st and last items
        $reversed = $collection->reverse();
        $reversed->pop();
        $reversed = $reversed->reverse();
        $reversed->pop();

        return $reversed->map(function ($item, $k) {
            foreach ($this->titles as $kk => $v) {
                // Replace array keys with titles
                $data[$v] = str_replace(',', '', $item[$kk]);
            }

            return $data;
        })
        ->values()
        ->all();
    }
}
