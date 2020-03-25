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

    protected $mappings = [
        'UK' => 'United Kingdom',
        'S. Korea' => 'South Korea',
        'N. Korea' => 'North Korea',
        'USA' => 'United States',
        'Hong Kong' => 'Hong Kong SAR China',
        'UAE' => 'United Arab Emirates',
        'Palestine' => 'Palestinian Territories',
        'Bosnia and Herzegovina' => 'Bosnia & Herzegovina',
        'North Macedonia' => 'Macedonia',
        'Macao' => 'Macau SAR China',
        'DRC' => 'Congo - Kinshasa',
        'Congo' => 'Congo - Kinshasa',
        'Saint Martin' => 'St. Martin',
        'Saint Lucia' => 'St. Lucia',
        'Trinidad and Tobago' => 'Trinidad & Tobago',
        'Antigua and Barbuda' => 'Antigua & Barbuda',
        'Ivory Coast' => 'Côte d’Ivoire',
        'St. Vincent Grenadines' => 'St. Vincent & Grenadines',
        'Faeroe Islands' => 'Faroe Islands',
        'Turks and Caicos' => 'Turks & Caicos Islands',
        'Myanmar' => 'Myanmar (Burma)',
        'Cabo Verde' => 'Cape Verde',
        'CAR' => 'Central African Republic',
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
        $country = array_key_exists($country, $this->mappings) ? $this->mappings[$country] : $country;

        return collect(json_decode(file_get_contents(__DIR__.'/flags.json'), true))->firstWhere('name', $country)['emoji'];
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

        return $crawler->filter('#main_table_countries_today')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $j) {
                return trim($td->text());
            });
        });
    }
}
