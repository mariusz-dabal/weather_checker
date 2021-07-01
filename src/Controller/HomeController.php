<?php

namespace App\Controller;

use App\Form\WeatherLocationFormType;
use App\Repository\WeatherRepository;
use App\Service\WeatherApi\WeatherService;
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

            if ($weatherRepo->findOneBy(['city' => $city])) {
                $weather = $weatherRepo->findOneBy(['city' => $city]);
            } else {
                try {
                    $weather = $weatherService->getWeather($city);
                } catch (\Exception $error) {
                    $this->addFlash('fail', 'Location Not Found!');

                    return $this->render('home/index.html.twig', [
                        'locationForm' => $form->createView(),
                    ]);
                }
                
                $em->persist($weather);
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
