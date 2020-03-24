<?php

namespace App;

use Sushi\Sushi;
use Goutte\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        return Cache::remember('COVID19-SUMMARY', Carbon::parse('10 minutes'), function () {
            return $this->getData();
        });
    }

    private function getData()
    {
        $item = collect($this->crawl())->shift();

        foreach ($this->titles as $kk => $v) {
            $data[$v] = str_replace(',', '', $item[$kk]);
        }

        return [$data];
    }

    private function crawl()
    {
        $crawler = (new Client())->request('GET', 'https://www.worldometers.info/coronavirus/');

        return $crawler->filter('tr.total_row')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return trim($td->text());
            });
        });
    }
}
