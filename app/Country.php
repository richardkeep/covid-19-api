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

    private function generateEmoji($country)
    {
        $flag = file_get_contents(__DIR__.'/flags.json');

        return collect(json_decode($flag, true))->firstWhere('name', $country)['emoji'];
    }

    private function getData()
    {
        return collect($this->crawl())->map(function ($item, $k) {
            foreach ($this->titles as $key => $value) {
                $data[$value] = $item[$key];
            }

            $data['emoji'] = $this->generateEmoji($item[0]);

            return $data;
        })
        ->sortByDesc('deaths')
        ->values()
        ->all();
    }

    private function crawl()
    {
        $crawler = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/');

        $data = $crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return str_replace(',', '', trim($td->text()));
            });
        });

        array_pop($data);
        array_shift($data);

        return $data;
    }
}
