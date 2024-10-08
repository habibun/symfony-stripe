<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeHostedPageController extends AbstractController
{
    #[Route('/stripe-hosted-page', name: 'app_stripe_hosted_page')]
    public function index(): Response
    {
        return $this->render('stripe_hosted_page/index.html.twig');
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/stripe-hosted-page/checkout', name: 'app_stripe_hosted_page_checkout')]
    public function checkout(): Response
    {
        $stripeSecretKey = $this->getParameter('stripe')['secret_key'];
        Stripe::setApiKey($stripeSecretKey);
        header('Content-Type: application/json');

        $checkout_session = Session::create([
            'line_items' => [[
                'price' => 'price_1Q7RTtKrr8S24r6DAEtZGzXc',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_stripe_hosted_page_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_stripe_hosted_page_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/stripe-hosted-page/success', name: 'app_stripe_hosted_page_success')]
    public function success(): Response
    {
        return $this->render('stripe_hosted_page/success.html.twig');
    }

    #[Route('/stripe-hosted-page/cancel', name: 'app_stripe_hosted_page_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe_hosted_page/cancel.html.twig');
    }
}
