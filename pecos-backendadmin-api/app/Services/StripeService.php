<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for the given amount
     *
     * @param float $amount Amount in dollars
     * @param string $currency Currency code (default: usd)
     * @param array $metadata Additional metadata
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = []): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $this->convertToCents($amount),
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'metadata' => $metadata,
        ]);
    }

    /**
     * Retrieve a payment intent by ID
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Confirm a payment intent
     *
     * @param string $paymentIntentId
     * @param string $paymentMethodId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId): PaymentIntent
    {
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        return $paymentIntent->confirm([
            'payment_method' => $paymentMethodId,
        ]);
    }

    /**
     * Check if a payment intent was successful
     *
     * @param PaymentIntent $paymentIntent
     * @return bool
     */
    public function isPaymentSuccessful(PaymentIntent $paymentIntent): bool
    {
        return $paymentIntent->status === 'succeeded';
    }

    /**
     * Convert dollars to cents for Stripe
     *
     * @param float $amount
     * @return int
     */
    private function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Get the publishable key for frontend use
     *
     * @return string
     */
    public static function getPublishableKey(): string
    {
        return config('services.stripe.key');
    }
}
