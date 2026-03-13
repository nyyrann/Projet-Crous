<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_EMPLOYEE')]
class EmployeeController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('admin/dashboard.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/{id}', name: 'admin_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservation_show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/validate', name: 'admin_reservation_validate', methods: ['POST'])]
    public function validate(
        Reservation $reservation,
        EntityManagerInterface $em
    ): Response {

        if ($reservation->getStatus() === 'retiree') {
            $this->addFlash('error', 'Réservation déjà retirée.');
            return $this->redirectToRoute('admin_reservation_show', [
                'id' => $reservation->getId()
            ]);
        }

        $reservation->setStatus('retiree');
        $em->flush();

        $this->addFlash('success', 'Réservation validée.');

        return $this->redirectToRoute('admin_reservation_show', [
            'id' => $reservation->getId()
        ]);
    }

    #[Route('/scan', name: 'admin_scan')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function scan(): Response
    {
        return $this->render('employee/scan.html.twig');
    }


    #[Route('/validateqr/{code}', name: 'admin_validate')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function validateQr(string $code, EntityManagerInterface $em, ReservationRepository $repo): Response
    {
        $reservation = $repo->findOneBy(['qrCode' => $code]);

        if (!$reservation) {
            return $this->json([
                'message' => 'Réservation introuvable'
            ]);
        }

        $reservation->setStatus('retire');
        $em->flush();

        return $this->json([
            'message' => 'Réservation validée'
        ]);
    }
}