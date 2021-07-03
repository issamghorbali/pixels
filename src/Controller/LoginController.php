<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/login2", name="login")
     */
    public function index(): Response
    {
        return $this->render('login.html.twig', [
            'controller_name' => '',
        ]);
    }

    /**
     * @Route("/reset-passwordd", name="reset_password")
     */
    public function reset_password(): Response
    {
        return $this->render('reset_password.html.twig', [
            'controller_name' => '',
        ]);
    }
}
