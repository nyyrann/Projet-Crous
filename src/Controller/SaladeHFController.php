<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SaladeHFController extends AbstractController
{
    #[Route('/reservation/salade_hf', name: 'salade_hf_type')]
    public function index(): Response
    {
                return $this->render(view: 'salade_hf/index.html.twig');
       
    }
}