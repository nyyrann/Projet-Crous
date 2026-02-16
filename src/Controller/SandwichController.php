<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SandwichController extends AbstractController
{
    #[Route('/reservation/sandwich', name: 'sandwich_type')]
    public function index(): Response
    {
        return $this->render(view: 'sandwich/index.html.twig');
    }
}