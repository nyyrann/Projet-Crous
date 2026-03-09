<?php

namespace App\Controller;

use App\Entity\Reservation;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;



#[IsGranted('ROLE_USER')]
class ReserveController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_EMPLOYEE', $user->getRoles())) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if (in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('app_reserve');
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route('/reserve', name: 'app_reserve')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['active' => true]);

        return $this->render('reservation/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/qr', name: 'reservation_qr')]
    public function qr(Reservation $reservation): Response
    {
        $qrCode = new QrCode(
            data: $reservation->getQrCode(),
            size: 300,
            margin: 10
        );

        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        return new Response(
            $result->getString(),
            200,
            ['Content-Type' => 'image/svg+xml']
        );
    }
}