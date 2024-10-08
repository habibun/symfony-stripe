<?php

namespace App\Controller;

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmbeddedFormController extends AbstractController
{
    /**
     * @throws ApiErrorException
     */
    #[Route('/embedded-form/checkout', name: 'app_embedded_form_checkout')]
    public function checkout(Request $request): Response
    {
        if (!$request->isMethod('POST')) {
            return $this->render('embedded_form/checkout.html.twig');
        }

        $stripeSecretKey = $this->getParameter('stripe')['secret_key'];
        $stripe = new StripeClient($stripeSecretKey);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://localhost:4242';

        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => '{{PRICE_ID}}',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'return_url' => $YOUR_DOMAIN . '/return.html?session_id={CHECKOUT_SESSION_ID}',
        ]);

        echo json_encode(array('clientSecret' => $checkout_session->client_secret));
    }

    #[Route('/embedded-form/status', name: 'app_embedded_form_status')]
    public function status(): Response
    {

    }

    #[Route('/stripe-hosted-page/return', name: 'app_embedded_form_return')]
    public function cancel(): Response
    {
        return $this->render('embedded_form/return.html.twig');
    }
}
