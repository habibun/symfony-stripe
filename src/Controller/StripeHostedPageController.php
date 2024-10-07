<?php

namespace App\Controller;

use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeHostedPageController extends AbstractController
{
    #[Route('/stripe-hosted-page', name: 'app_stripe_hosted_page')]
    public function index(): Response
    {
        return $this->render('stripe_hosted_page/index.html.twig', [
            'controller_name' => 'StripeHostedPageController',
        ]);
    }

    #[Route('/stripe-hosted-page/checkout', name: 'app_stripe_hosted_page_checkout')]
    public function checkout(): Response
    {
        $stripeSecretKey = $this->getParameter('stripe')['secret_key'];
        Stripe::setApiKey($stripeSecretKey);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://localhost:4242';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => '{{PRICE_ID}}',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }
}
