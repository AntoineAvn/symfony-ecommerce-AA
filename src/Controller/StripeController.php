<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CartsProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{

    #[Route('/create-checkout-session', name: 'app_checkout_create', methods: ['POST'])]
    public function checkoutCreate(CartsProductsRepository $cpRepository): Response
    {
        $stripe_sk = $this->getParameter('stripe_sk');
        $stripe = new \Stripe\StripeClient($stripe_sk);

        /** @var User $user **/
        $user = $this->getUser();
        $userId = $this->getUser()->getId();

        $cart = $user->getCart();
        $cartProducts = $cart->getCartsProducts()->toArray();

        $data = [];

        //on stock nos lignes de tout les produits dans mon panier
        foreach ($cartProducts as $cartProduct) {
            $data[] = [
                'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $cartProduct->getProduct()->getName(),
                ],
                'unit_amount' => $cartProduct->getProduct()->getPrice() * 100,
                ],
                'quantity' => $cartProduct->getQuantity(),
            ];

            //On met à jour la quantité des produits en base
            $qtyProduct = $cartProduct->getProduct()->getQuantity();
            $cartProduct->getProduct()->setQuantity($qtyProduct - $cartProduct->getQuantity());

            //On met à jour le sold des produits en base
            $sold = $cartProduct->getProduct()->getSold();
            $cartProduct->getProduct()->setSold($sold + $cartProduct->getQuantity());
        }

        //Persist et flush pour update les produits
        $cpRepository->save($cartProduct, true);

        //On supprime les produits du panier existant
        foreach ($cartProducts as $cartProduct) {
            $cpRepository->remove($cartProduct, true);
        }
        
        //Creation de session pour Stripe
        $checkout_session = $stripe->checkout->sessions->create([
        'line_items' => [$data],
        'mode' => 'payment',
        'success_url' => 'http://127.0.0.1:8000/stripe-success', //HTTP à changer selon serveur local
        'cancel_url' => 'http://127.0.0.1:8000/stripe-cancel', //A changer ""
        ]);

        //Redirection vers l'url stripe
        return $this->redirect($checkout_session->url);
    }

    #[Route('/stripe-success', name: 'app_stripe_success')]
    public function stripeSuccess(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/stripe-cancel', name: 'app_stripe_cancel')]
    public function stripeCancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
