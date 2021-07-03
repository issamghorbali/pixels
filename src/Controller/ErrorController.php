<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\SettingsType;
use ContainerQHRiH41\getSettingsRepositoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ErrorController extends AbstractController
{
    /**
     * @Route("/404", name="404")
     */
    public function show(Request $request, EntityManagerInterface $manager): Response
    {
        return $this->render('error-404.html.twig', [
            'controller_name' => 'SettingsController',
            'title'=>'Error 404'
        ]);
    }

}
