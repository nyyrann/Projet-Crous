<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/panier/add', name: 'panier_add', methods: ['POST'])]
    public function add(
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository
    ): Response {

        $id = $request->request->get('id');
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->redirectToRoute('app_reserve');
        }

        $panier = $session->get('panier', []);

        $totalItems = 0;
        foreach ($panier as $item) {
            $totalItems += $item['quantity'];
        }

        if ($totalItems >= 2) {
            $this->addFlash('error', 'Maximum 2 articles autorisés.');
            return $this->redirectToRoute('panier');
        }

        if (isset($panier[$id])) {
            $panier[$id]['quantity']++;
        } else {
            $panier[$id] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => 1,
            ];
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_reserve');
    }

    #[Route('/panier/remove/{id}', name: 'panier_remove', methods: ['POST'])]
    public function remove(string $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/update/{id}', name: 'panier_update', methods: ['POST'])]
    public function update(
        string $id,
        Request $request,
        SessionInterface $session
    ): Response {

        $action = $request->request->get('action');
        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            return $this->redirectToRoute('panier');
        }

        $totalItems = 0;
        foreach ($panier as $item) {
            $totalItems += $item['quantity'];
        }

        if ($action === 'plus') {

            if ($totalItems >= 2) {
                $this->addFlash('error', 'Maximum 2 articles autorisés.');
                return $this->redirectToRoute('panier');
            }

            $panier[$id]['quantity']++;
        }

        if ($action === 'minus') {
            $panier[$id]['quantity']--;

            if ($panier[$id]['quantity'] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    #[Route('/payment', name: 'app_payment', methods:['GET','POST'])]
    public function payment(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('panier');
        }

        $total = 0;

        foreach ($panier as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $this->render('payment/index.html.twig', [
            'panier' => $panier,
            'total' => $total
        ]);

    }

    #[Route('/payment/success', name: 'app_payment_success', methods:['POST'])]
    public function paymentSuccess(
        SessionInterface $session,
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        Security $security
    ): Response {

        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('panier');
        }

        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCreatedAt(new \DateTimeImmutable());
        $reservation->setStatus('payee');
        $reservation->setQrCode(Uuid::v4()->toRfc4122());

        $em->persist($reservation);

        foreach ($panier as $id => $item) {

            $product = $productRepository->find($id);

            if (!$product) {
                continue;
            }

            $reservationItem = new ReservationItem();
            $reservationItem->setReservation($reservation);
            $reservationItem->setProduct($product);
            $reservationItem->setQuantity($item['quantity']);

            $em->persist($reservationItem);
        }

        $em->flush();

        $session->remove('panier');

        $this->addFlash('success', 'Paiement effectué avec succès !');

        return $this->redirectToRoute('reservation_show', [
            'id' => $reservation->getId()
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation
        ]);
    }
}