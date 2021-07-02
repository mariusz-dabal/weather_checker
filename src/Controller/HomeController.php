<?php

namespace App\Controller;

use App\Form\WeatherLocationFormType;
use App\Repository\WeatherRepository;
use App\Service\WeatherApi\WeatherService;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em, Request $request, WeatherService $weatherService, WeatherRepository $weatherRepo): Response
    {
        $form = $this->createForm(WeatherLocationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Weather $weather */
            $weather = $form->getData();

            $city = strtolower($weather->getCity());
            $currentTime = new DateTime('now');

            if ($weatherRepo->findOneBy(['city' => $city]) &&
                $weatherRepo->findOneBy(['city' => $city])->getCreatedAt()->add(new DateInterval('PT5M')) > $currentTime
            ) {
                $weather = $weatherRepo->findOneBy(['city' => $city]);
            } else {
                try {
                    $weather = $weatherService->getWeather($city);

                    if ($weatherRepo->findOneBy(['city' => $city])) {
                        $oldWeather = $weatherRepo->findOneBy(['city' => $city]);
                        $oldWeather->setTemperature($weather->getTemperature());
                        $oldWeather->setDescription($weather->getDescription());
                        $oldWeather->setCreatedAt($weather->getCreatedAt());
                    } else {
                        $em->persist($weather);
                    }
                } catch (\Exception $error) {
                    $this->addFlash('fail', 'Location Not Found!');

                    return $this->render('home/index.html.twig', [
                        'locationForm' => $form->createView(),
                    ]);
                }

                $em->flush();
            }

            $this->addFlash('success', 'Location Found!');

            return $this->render('home/index.html.twig', [
                'locationForm' => $form->createView(),
                'weather' => $weather,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'locationForm' => $form->createView(),
        ]);
    }
}
