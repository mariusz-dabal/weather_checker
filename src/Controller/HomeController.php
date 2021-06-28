<?php

namespace App\Controller;

use App\Form\WeatherLocationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $form = $this->createForm(WeatherLocationFormType::class);

        return $this->render('home/index.html.twig', [
            'locationForm' => $form->createView(),
        ]);
    }
}
