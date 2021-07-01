<?php

namespace App\Service\WeatherApi;

use App\Entity\Weather;
use App\Service\WeatherApi\Providers\WeatherApiInterface;

class WeatherService implements WeatherServiceInterface
{
    private WeatherManager $weather_manager;

    public function __construct(WeatherManager $weather_manager)
    {
        $this->weather_manager = $weather_manager;
    }

    public function getWeather(string $city): Weather
    {
        return $this->weather_manager->getAverageWeatherForecast($city);
    }
}