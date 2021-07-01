<?php

namespace App\Service\WeatherApi\Providers;

interface WeatherApiInterface
{
    public function connect(array $params);

    public function getWeather();
}

