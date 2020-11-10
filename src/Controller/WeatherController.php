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
//        try {
//            $weather = $this->weather->getWeather('Berlin', 'metric', 'de');
//        } catch(OWMException $e) {
//            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
//        } catch(\Exception $e) {
//            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
//        }
        return $this->render('index.html.twig');
    }
}