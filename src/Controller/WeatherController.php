<?php


namespace App\Controller;
use App\Entity\Weather;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController {
    public function index() {
        $templateVariables = [];
        $templateVariables['weather'] = $this->getDoctrine()
                    ->getRepository(Weather::class )
                    ->findBy([], ['id' => 'DESC'], 8);
        if ( !empty( $templateVariables['weather'] ) ) {
            $templateVariables['weather'] = array_reverse( $templateVariables['weather'] );
            $templateVariables['column_names'] = $templateVariables['weather'][0]->getObjectVarsKeys();
        }
        return $this->render('index.html.twig', $templateVariables );
    }
}