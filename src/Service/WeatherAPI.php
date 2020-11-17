<?php

namespace App\Service;

use Cmfcmf\OpenWeatherMap;
use Http\Adapter\Guzzle6\Client;
use Http\Factory\Guzzle\RequestFactory;

class WeatherAPI
{
    private const API_KEY = '227933e6fb630ad4f45e15ef37c373d5'; //temporary const
    private $apiGate;

    public function __construct(Client $httpClient, RequestFactory $factory)
    {
        $this->apiGate = new OpenWeatherMap(self::API_KEY, $httpClient, $factory);
    }

    public function getGroupWeatherForecast(array $queries, $units = 'imperial', $lang = 'en', $app_id = '', $days = 1)
    {
        $bulkResult = [];
        foreach ($queries as $query) {
                $bulkResult[] = $this->apiGate->getWeatherForecast(
                    $query,
                    $units = 'imperial',
                    $lang = 'en',
                    $app_id = '',
                    $days = 1
                );
        }
        return $bulkResult;
    }

    public function getWeatherForecast(string $city, string $metric)
    {
        return $this->apiGate->getWeatherForecast($city, $metric, 'ru');
    }
}
