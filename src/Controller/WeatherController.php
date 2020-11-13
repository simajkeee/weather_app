<?php


namespace App\Controller;
use App\Service\WeatherAPI;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController {
    private $weather;

    public function __construct( WeatherAPI $weather ) {
        $weather->setHttpClient( GuzzleAdapter::createWithConfig( [] ) );
        $weather->setHttpRequestFactory( new RequestFactory() );
        $this->weather = $weather->load();
    }
    /**
     * @Route("/weather")
     */
    public function index() {
        //Moscow, Astrakhan, Kaliningrad
        //$groupWeather = $this->weather->getGroupWeatherForecast([524894, 580497, 554234 ], 'metric', 'ru');
        $weather = $this->weather->getWeatherForecast('Astrakhan', 'metric', 'ru');


        return $this->render('index.html.twig');
    }
}