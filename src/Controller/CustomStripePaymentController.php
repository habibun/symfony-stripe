<?php

namespace App\Controller;

use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CustomStripePaymentController extends AbstractController
{
    #[Route('/custom-stripe-payment/checkout', name: 'custom_stripe_payment_checkout')]
    public function checkout(): Response
    {
        return $this->render('custom_stripe_payment/checkout.html.twig');
    }

    #[Route('/custom-stripe-payment/create', name: 'custom_stripe_payment_create')]
    public function create(Request $request): Response
    {
        $stripeSecretKey = $this->getParameter('stripe')['secret_key'];
        $stripe = new StripeClient($stripeSecretKey);
        header('Content-Type: application/json');
        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            // Create a PaymentIntent with amount and currency
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $this->calculateOrderAmount($jsonObj->items),
                'currency' => 'usd',
                // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
                // [DEV]: For demo purposes only, you should avoid exposing the PaymentIntent ID in the client-side code.
                'dpmCheckerLink' => "https://dashboard.stripe.com/settings/payment_methods/review?transaction_id={$paymentIntent->id}",
            ];

            echo json_encode($output);
        } catch (\Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit();
    }

    #[Route('/custom-stripe-payment/complete', name: 'custom_stripe_payment_complete')]
    public function complete(): Response
    {
        return $this->render('custom_stripe_payment/complete.html.twig');
    }

    private function calculateOrderAmount(array $items): int {
        // Calculate the order total on the server to prevent
        // people from directly manipulating the amount on the client
        $total = 0;
        foreach($items as $item) {
            $total += $item->amount;
        }
        return $total;
    }
}
