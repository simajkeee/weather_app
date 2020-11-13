<?php


namespace App\Command;


use App\Entity\Weather;
use App\Service\WeatherAPI;
use Doctrine\ORM\EntityManagerInterface;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Factory\Guzzle\RequestFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherCommand extends Command {
    protected static $defaultName = 'app:weather';

    public function __construct( WeatherAPI $weather, EntityManagerInterface $em )
    {
        $weather->setHttpClient( GuzzleAdapter::createWithConfig( [] ) );
        $weather->setHttpRequestFactory( new RequestFactory() );
        $this->em = $em;
        $this->weatherApi = $weather->load();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Weather fetching and saving command.')
             ->setHelp('The command allows to update weather data within 24 hours from the moment it is fired.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $section = $output->section();
        $section->writeln('Fetching data...');
        $weather = $this->weatherApi->getWeatherForecast('Astrakhan', 'metric', 'ru');
        $timezone = $weather->city->timezone;
        $section->overwrite('Updating database...');
        $lastDbRow = $this->getLastRow();
        foreach ( $weather as $forecastThreeHours ) {
            $forecastThreeHours->time->to->setTimezone( $timezone );
            if ( $lastDbRow['date_to'] < $forecastThreeHours->time->to->format('Y-m-d H:i:s') ) {
                $weather = new Weather();
                $forecastThreeHours->time->from->setTimezone( $timezone );
                $weather->setDateFrom( $forecastThreeHours->time->from )
                    ->setDateTo( $forecastThreeHours->time->to )
                    ->setCity( $forecastThreeHours->city->name )
                    ->setTemperature( round( $forecastThreeHours->temperature->now->getValue() ) )
                    ->setWeatherDescription( $forecastThreeHours->weather->description )
                ;
                $this->em->persist( $weather );
            }
        }
        $this->em->flush();
        $section->overwrite('Ready');
        return Command::SUCCESS;
    }

    private function getLastRow() {
        $connection = $this->em->getConnection();
        $driver = $connection->executeQuery('SELECT * FROM weather ORDER BY id DESC LIMIT 0, 1');
        return $driver->fetch( \PDO::FETCH_ASSOC );
    }
}