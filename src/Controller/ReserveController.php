<?php

namespace App\Controller;

use App\Repository\ProductRepository;
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
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(
            ['active' => true],
            ['category' => 'ASC']
        );

        return $this->render('reservation/index.html.twig', [
            'products' => $products,
        ]);
    }
}