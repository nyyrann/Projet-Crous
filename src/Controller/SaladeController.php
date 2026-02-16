<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SaladeController extends AbstractController
{
    #[Route('/reservation/salade', name: 'salade_type')]
    public function index(): Response
    {
                return $this->render(view: 'salade/index.html.twig');
    }
}