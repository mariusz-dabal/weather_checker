<?php

namespace App\Service\WeatherApi;

use App\Entity\Weather;
use App\Service\WeatherApi\Providers\OpenWeatherApi;
use App\Service\WeatherApi\Providers\WeatherApiInterface;
use App\Service\WeatherApi\Providers\WeatherBitApi;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WeatherManager
{
    private array $providers = [];

    public function __construct(
        OpenWeatherApi $openWeatherApi,
        WeatherBitApi $weatherBitApi
        )
    {
        $this->addProvider($openWeatherApi);
        $this->addProvider($weatherBitApi);
    }

    private function addProvider(WeatherApiInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getAverageWeatherForecast(string $city): ?Weather
    {
        if (count($this->providers) === 0) {
            return false;
        }

        $averageTemp = 0;
        $description = [];

        foreach ($this->providers as $provider) {
            try {
                $provider->connect(['city' => $city]);
            } catch (\Exception $error) {
                throw new NotFoundHttpException('elo');
            }

            /** @var Weather $weather */
            $weather = $provider->getWeather();

            $averageTemp += $weather->getTemperature();
            $description[] = strtolower($weather->getDescription());
        }
        
        $averageTemp = $averageTemp / count($this->providers);
        $description = implode(' ', array_unique($description));

        /** @var Weather $weather */
        $weather = new Weather();

        $weather->setTemperature($averageTemp);
        $weather->setDescription($description);
        $weather->setCity($city);
        $weather->setCreatedAt(new DateTimeImmutable('now'));

        return $weather;
    }
}