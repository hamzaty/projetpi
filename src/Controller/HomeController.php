<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/shopDetails', name: 'shopdetail')]
    public function sDetails(): Response
    {
        return $this->render('detail/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/shop', name: 'shop')]
    public function shop(): Response
    {
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    
    
}
