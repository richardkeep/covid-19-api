<?php

namespace App;

use Sushi\Sushi;
use Goutte\Client;
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
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.worldometers.info/coronavirus/');

        return collect($crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return trim($td->text());
            });
        }))->filter(function ($item, $key) {
            // Skip table headers
            return $key > 0;
        })->map(function ($item, $k) {
            foreach ($this->titles as $kk => $v) {
                // Replace array keys with titles
                $data[$v] = str_replace(',', '', $item[$kk]);
            }

            return $data;
        })
        ->pop() // remove last item: (Totals)
        ->values()
        ->all();
    }
}
