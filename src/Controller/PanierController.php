<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function add(Request $request, SessionInterface $session): Response
    {
        $id = $request->request->get('id');
        $name = $request->request->get('name');

        $panier = $session->get('panier', []);

        // 🔹 Calcul total actuel
        $totalItems = 0;
        foreach ($panier as $item) {
            $totalItems += $item['quantity'];
        }

        // 🔹 Bloquer si déjà 2 articles
        if ($totalItems >= 2) {
            $this->addFlash('error', 'Vous ne pouvez réserver que 2 articles maximum.');
            return $this->redirectToRoute('panier');
        }

        // 🔹 Sinon ajouter
        if (isset($panier[$id])) {
            $panier[$id]['quantity']++;
        } else {
            $panier[$id] = [
                'name' => $name,
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
    public function update(string $id, Request $request, SessionInterface $session): Response
    {
        $action = $request->request->get('action');
        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            return $this->redirectToRoute('panier');
        }

        // Calcul total actuel
        $totalItems = 0;
        foreach ($panier as $item) {
            $totalItems += $item['quantity'];
        }

        if ($action === 'plus') {

            // Bloquer si déjà 2 articles
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


    #[Route('/reservation/validation', name: 'validation', methods: ['POST'])]
    public function validateReservation(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('panier');
        }


        // Vider le panier
        $session->remove('panier');

        $this->addFlash('success', 'Réservation validée avec succès !');

        return $this->redirectToRoute('app_reserve');
    }
}