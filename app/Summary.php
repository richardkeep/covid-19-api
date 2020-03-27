<?php

namespace App;

use Sushi\Sushi;
use Goutte\Client;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use Sushi;

    protected $titles = [
        'title', 'cases', 'todayCases', 'deaths',
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
        return $this->getData();
    }

    private function getData()
    {
        $item = $this->crawl();

        foreach ($this->titles as $key => $value) {
            $data[$value] = $item[$key];
        }

        return [$data];
    }

    private function crawl()
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
