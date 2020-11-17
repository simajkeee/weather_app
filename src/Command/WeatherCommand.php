<?php

namespace App\Command;

use App\Entity\Weather;
use App\Service\WeatherAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherCommand extends Command
{
    protected static $defaultName = 'app:weather';
    private $weatherApi;
    private $em;

    public function __construct(WeatherAPI $weather, EntityManagerInterface $em)
    {
        $this->weatherApi = $weather;
        $this->em = $em;

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
        $weather = $this->weatherApi->getWeatherForecast('Astrakhan', 'metric');
        $timezone = $weather->city->timezone;
        $section->overwrite('Updating database...');
        $lastDbRow = $this->getLastRow();
        foreach ($weather as $forecastThreeHours) {
            $forecastThreeHours->time->to->setTimezone($timezone);
            if ($lastDbRow['date_to']->format('Y-m-d H:i:s') < $forecastThreeHours->time->to->format('Y-m-d H:i:s')) {
                $weather = new Weather();
                $forecastThreeHours->time->from->setTimezone($timezone);
                $weather->setDateFrom($forecastThreeHours->time->from)
                        ->setDateTo($forecastThreeHours->time->to)
                        ->setCity($forecastThreeHours->city->name)
                        ->setTemperature(round($forecastThreeHours->temperature->now->getValue()))
                        ->setWeatherDescription($forecastThreeHours->weather->description);
                $this->em->persist($weather);
            }
        }
        $this->em->flush();
        $section->overwrite('Ready');
        return 1;
    }

    private function getLastRow()
    {
        $query = $this->em
            ->getRepository(Weather::class)
            ->createQueryBuilder('w')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->orderBy('w.id', 'DESC')
            ->getQuery();
        $result = $query->getResult(2);
        return array_pop($result);
    }
}
