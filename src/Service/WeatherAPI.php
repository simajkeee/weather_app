<?php


namespace App\Service;


use Cmfcmf\OpenWeatherMap;

class WeatherAPI {
    const API_KEY = '227933e6fb630ad4f45e15ef37c373d5'; //temporary const
    private $apiGate;
    private $httpClient;
    private $httpRequestFactory;

    public function setHttpClient( $httpClient ) {
        $this->httpClient = $httpClient;
    }

    public function setHttpRequestFactory( $httpRequestFactory ) {
        $this->httpRequestFactory = $httpRequestFactory;
    }

    public function getGroupWeatherForecast( array $queries, $units = 'imperial', $lang = 'en', $appid = '', $days = 1) {
        $bulkResult = [];
        foreach ( $queries as $query ) {
            $bulkResult[] = $this->apiGate->getWeatherForecast($query, $units = 'imperial', $lang = 'en', $appid = '', $days = 1);
        }
        return $bulkResult;
    }

    public function load() {
        $this->apiGate = new OpenWeatherMap( self::API_KEY, $this->httpClient, $this->httpRequestFactory );
        return $this;
    }

    public function __call( $name, $arguments ) {
        return $this->apiGate->$name(...$arguments);
    }
}