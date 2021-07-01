<?php

namespace App\Service\WeatherApi\Providers;

use App\Entity\Weather;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenWeatherApi implements WeatherApiInterface
{
    private Weather $weather;

    private ContainerBagInterface $params;

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, ContainerBagInterface $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    public function connect(array $params = null)
    {
        try {
            $this->api = $this->client->request(
                'GET',
                $this->params->get('app.openWeatherApiUrl'),
                 [
                  'query' => [
                      'q' => $params['city'],
                      'units' => 'metric',
                      'appid' => $this->params->get('app.openWeatherApiKey'),
                  ],
                ]
            );
            $this->setWeather();
    
            return $this;
        } catch (\Exception $error) {
            throw new NotFoundHttpException();
        }
    }

    public function getWeather()
    {
        return $this->weather;
    }

    private function setWeather()
    {
        $content = $this->api->toArray();

        /** @var Weather $weather */
        $this->weather = new Weather();

        $temperature = $content['main']['temp'];
        $description = $content['weather'][0]['description'];

        $this->weather->setTemperature($temperature);
        $this->weather->setDescription($description);
    }
}