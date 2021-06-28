<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\WeatherLocationFormType;
use App\Repository\LocationRepository;
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
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(WeatherLocationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var Location $location */
            $location = $form->getData();

            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Data submitted');

            return $this->redirectToRoute('location');
        }

        return $this->render('home/index.html.twig', [
            'locationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/location", name="location")
     */
    public function list(LocationRepository $locationRepo) 
    {
        $locations = $locationRepo->findAll();

        return $this->render('location/list.html.twig', [
            'locations' => $locations,
        ]);
    }
}
