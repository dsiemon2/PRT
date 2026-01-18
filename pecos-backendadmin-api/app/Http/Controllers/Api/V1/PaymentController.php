<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Payments\PaymentManager;

class PaymentController extends Controller
{
    protected PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Get available payment gateways and their frontend configuration
     */
    public function getGateways(): JsonResponse
    {
        if (!$this->paymentManager->hasEnabledGateway()) {
            return response()->json([
                'success' => false,
                'message' => 'No payment gateways configured',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'gateways' => $this->paymentManager->getFrontendConfigs(),
            'enabled' => $this->paymentManager->getEnabledGateways(),
            'supported_methods' => $this->paymentManager->getAllSupportedMethods(),
        ]);
    }

    /**
     * Create a payment intent/order
     */
    public function createPayment(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'string|size:3',
            'gateway' => 'string|nullable',
            'order_id' => 'string|nullable',
            'customer_email' => 'email|nullable',
        ]);

        $amount = $request->input('amount');
        $currency = $request->input('currency', 'usd');
        $gateway = $request->input('gateway');

        $options = [];

        if ($request->has('order_id')) {
            $options['order_id'] = $request->input('order_id');
        }

        if ($request->has('customer_email')) {
            $options['receipt_email'] = $request->input('customer_email');
        }

        if ($request->has('description')) {
            $options['description'] = $request->input('description');
        }

        if ($request->has('metadata')) {
            $options['metadata'] = $request->input('metadata');
        }

        // Payment method specific options
        if ($request->has('payment_method_nonce')) {
            // Braintree
            $options['payment_method_nonce'] = $request->input('payment_method_nonce');
        }

        if ($request->has('source_id')) {
            // Square
            $options['source_id'] = $request->input('source_id');
        }

        if ($request->has('opaque_data')) {
            // Authorize.net Accept.js token
            $options['opaque_data'] = $request->input('opaque_data');
        }

        // Use specific gateway if provided, otherwise use default
        if ($gateway) {
            $result = $this->paymentManager->createPaymentWith($gateway, $amount, $currency, $options);
        } else {
            $result = $this->paymentManager->createPayment($amount, $currency, $options);
        }

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Retrieve payment details
     */
    public function getPayment(Request $request, string $paymentId): JsonResponse
    {
        $gateway = $request->query('gateway');

        $result = $this->paymentManager->retrievePayment($paymentId, $gateway);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Confirm/capture a payment
     */
    public function confirmPayment(Request $request, string $paymentId): JsonResponse
    {
        $gateway = $request->input('gateway');
        $options = [];

        if ($request->has('payment_method')) {
            $options['payment_method'] = $request->input('payment_method');
        }

        $result = $this->paymentManager->confirmPayment($paymentId, $options, $gateway);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Cancel/void a payment
     */
    public function cancelPayment(Request $request, string $paymentId): JsonResponse
    {
        $gateway = $request->input('gateway');

        $result = $this->paymentManager->cancelPayment($paymentId, $gateway);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Request $request, string $paymentId): JsonResponse
    {
        $request->validate([
            'amount' => 'numeric|min:0.01|nullable',
            'reason' => 'string|nullable',
            'gateway' => 'string|nullable',
        ]);

        $amount = $request->input('amount');
        $reason = $request->input('reason');
        $gateway = $request->input('gateway');

        $result = $this->paymentManager->refundPayment($paymentId, $amount, $reason, $gateway);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Handle webhook from payment gateways
     */
    public function webhook(Request $request, string $gateway): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature')
            ?? $request->header('X-Square-Signature')
            ?? $request->header('X-Anet-Signature')
            ?? $request->header('Bt-Signature')
            ?? '';

        $result = $this->paymentManager->verifyWebhook($payload, $signature, $gateway);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Process webhook event based on type
        // This is where you'd update order status, send emails, etc.
        $eventType = $result['type'] ?? '';

        // Log the webhook
        \Log::info("Payment webhook received: {$gateway}", [
            'type' => $eventType,
            'data' => $result['data'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed',
        ]);
    }
}
