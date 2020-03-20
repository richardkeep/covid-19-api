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

    private function getData()
    {
        $collection = collect($this->crawl());

        $collection->shift(); // Remove 1st item
        $collection->pop(); // Remove last item

        return $collection->map(function ($item, $k) {
            foreach ($this->titles as $kk => $v) {
                // Replace array keys with titles
                $data[$v] = str_replace(',', '', $item[$kk]);
            }

            return $data;
        })
        ->sortByDesc('deaths')
        ->values()
        ->all();
    }

    private function crawl()
    {
        $crawler = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/');

        return $crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return trim($td->text());
            });
        });
    }
}
