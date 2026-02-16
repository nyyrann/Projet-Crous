<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaniniController extends AbstractController
{
    #[Route('/reservation/panini', name: 'panini_type')]
    public function index(): Response
    {
                return $this->render(view: 'panini/index.html.twig');
    }
}