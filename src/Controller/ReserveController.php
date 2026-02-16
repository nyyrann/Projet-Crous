<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReserveController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_reserve');
    }

    
    #[Route('/reserve', name: 'app_reserve')]
    public function index(): Response
    {
        return $this->render(view: 'reserve/index.html.twig');
    }
}