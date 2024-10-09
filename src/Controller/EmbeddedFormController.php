<?php

namespace App\Controller;

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmbeddedFormController extends AbstractController
{
    /**
     * @throws ApiErrorException
     */
    #[Route('/embedded-form/checkout', name: 'app_embedded_form_checkout')]
    public function checkout(Request $request): Response
    {

//        dd($this->generateUrl('app_embedded_form_return', ['session_id' => "{CHECKOUT_SESSION_ID}"], UrlGeneratorInterface::ABSOLUTE_URL));
        if (!$request->isMethod('POST')) {
            return $this->render('embedded_form/checkout.html.twig');
        }

        $stripeSecretKey = $this->getParameter('stripe')['secret_key'];
        $stripe = new StripeClient($stripeSecretKey);
        header('Content-Type: application/json');

        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'line_items' => [[
                'price' => 'price_1Q7RTtKrr8S24r6DAEtZGzXc',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'return_url' => $this->generateUrl('app_embedded_form_return', ['session_id' => "{CHECKOUT_SESSION_ID}"], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

//        echo json_encode(array('clientSecret' => $checkout_session->client_secret));

        return $this->json(['clientSecret' => $checkout_session->client_secret]);
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
