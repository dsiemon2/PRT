<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Payment Manager - Factory and Manager for Payment Gateways
 *
 * Manages all payment gateway instances and provides a unified interface
 * for payment processing operations.
 *
 * Usage:
 *   $manager = app(PaymentManager::class);
 *   $result = $manager->createPayment(99.99, 'usd', ['order_id' => '12345']);
 */
class PaymentManager
{
    /**
     * Registered gateway instances
     */
    protected array $gateways = [];

    /**
     * Gateway configurations from database
     */
    protected array $configurations = [];

    /**
     * Currently active gateway identifier
     */
    protected ?string $activeGateway = null;

    /**
     * Available gateway classes
     */
    protected array $gatewayClasses = [
        'stripe' => StripeGateway::class,
        'braintree' => BraintreeGateway::class,
        'paypal' => PayPalGateway::class,
        'square' => SquareGateway::class,
        'authorizenet' => AuthorizeNetGateway::class,
    ];

    public function __construct()
    {
        $this->loadConfigurations();
    }

    /**
     * Load gateway configurations from database
     */
    protected function loadConfigurations(): void
    {
        try {
            // Cache configurations for 5 minutes
            $this->configurations = Cache::remember('payment_gateway_configs', 300, function () {
                return $this->fetchConfigurationsFromDatabase();
            });

            // Initialize enabled gateways
            foreach ($this->configurations as $identifier => $config) {
                if ($config['enabled'] ?? false) {
                    $this->initializeGateway($identifier, $config);

                    // Set first enabled gateway as active
                    if ($this->activeGateway === null) {
                        $this->activeGateway = $identifier;
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but don't crash - allows app to run without payment config
            report($e);
        }
    }

    /**
     * Fetch configurations from settings table (features group)
     */
    protected function fetchConfigurationsFromDatabase(): array
    {
        $configs = [];

        try {
            // Get all feature settings from settings table
            $settings = DB::table('settings')
                ->where('setting_group', 'features')
                ->pluck('setting_value', 'setting_key')
                ->toArray();

            // Stripe configuration
            if (!empty($settings['stripe_enabled']) && $this->isTruthy($settings['stripe_enabled'])) {
                $configs['stripe'] = [
                    'enabled' => true,
                    'publishable_key' => $settings['stripe_publishable_key'] ?? '',
                    'secret_key' => $settings['stripe_secret_key'] ?? '',
                    'webhook_secret' => $settings['stripe_webhook_secret'] ?? '',
                    'test_mode' => $this->isTruthy($settings['stripe_test_mode'] ?? false),
                    'ach_enabled' => $this->isTruthy($settings['stripe_ach_enabled'] ?? false),
                ];
            }

            // Braintree configuration
            if (!empty($settings['braintree_enabled']) && $this->isTruthy($settings['braintree_enabled'])) {
                $configs['braintree'] = [
                    'enabled' => true,
                    'merchant_id' => $settings['braintree_merchant_id'] ?? '',
                    'public_key' => $settings['braintree_public_key'] ?? '',
                    'private_key' => $settings['braintree_private_key'] ?? '',
                    'sandbox' => $this->isTruthy($settings['braintree_sandbox'] ?? false),
                ];
            }

            // PayPal configuration
            if (!empty($settings['paypal_enabled']) && $this->isTruthy($settings['paypal_enabled'])) {
                $configs['paypal'] = [
                    'enabled' => true,
                    'client_id' => $settings['paypal_client_id'] ?? '',
                    'client_secret' => $settings['paypal_client_secret'] ?? '',
                    'sandbox' => $this->isTruthy($settings['paypal_sandbox'] ?? false),
                ];
            }

            // Square configuration
            if (!empty($settings['square_enabled']) && $this->isTruthy($settings['square_enabled'])) {
                $configs['square'] = [
                    'enabled' => true,
                    'application_id' => $settings['square_application_id'] ?? '',
                    'access_token' => $settings['square_access_token'] ?? '',
                    'location_id' => $settings['square_location_id'] ?? '',
                    'sandbox' => $this->isTruthy($settings['square_sandbox'] ?? false),
                ];
            }

            // Authorize.net configuration
            if (!empty($settings['authorizenet_enabled']) && $this->isTruthy($settings['authorizenet_enabled'])) {
                $configs['authorizenet'] = [
                    'enabled' => true,
                    'login_id' => $settings['authorizenet_login_id'] ?? '',
                    'transaction_key' => $settings['authorizenet_transaction_key'] ?? '',
                    'signature_key' => $settings['authorizenet_signature_key'] ?? '',
                    'sandbox' => $this->isTruthy($settings['authorizenet_sandbox'] ?? false),
                ];
            }
        } catch (Exception $e) {
            report($e);
        }

        return $configs;
    }

    /**
     * Check if value is truthy
     */
    protected function isTruthy($value): bool
    {
        return $value === true || $value === '1' || $value === 'true' || $value === 1;
    }

    /**
     * Initialize a specific gateway
     */
    protected function initializeGateway(string $identifier, array $config): void
    {
        if (!isset($this->gatewayClasses[$identifier])) {
            return;
        }

        $gatewayClass = $this->gatewayClasses[$identifier];
        $gateway = new $gatewayClass();
        $gateway->initialize($config);

        $this->gateways[$identifier] = $gateway;
    }

    /**
     * Get a specific gateway instance
     */
    public function gateway(string $identifier): ?PaymentGatewayInterface
    {
        return $this->gateways[$identifier] ?? null;
    }

    /**
     * Get the active gateway instance
     */
    public function getActiveGateway(): ?PaymentGatewayInterface
    {
        if ($this->activeGateway === null) {
            return null;
        }

        return $this->gateways[$this->activeGateway] ?? null;
    }

    /**
     * Set the active gateway
     */
    public function setActiveGateway(string $identifier): self
    {
        if (isset($this->gateways[$identifier])) {
            $this->activeGateway = $identifier;
        }

        return $this;
    }

    /**
     * Get all enabled gateway identifiers
     */
    public function getEnabledGateways(): array
    {
        return array_keys($this->gateways);
    }

    /**
     * Get all available gateway identifiers
     */
    public function getAvailableGateways(): array
    {
        return array_keys($this->gatewayClasses);
    }

    /**
     * Check if any payment gateway is enabled
     */
    public function hasEnabledGateway(): bool
    {
        return !empty($this->gateways);
    }

    /**
     * Check if a specific gateway is enabled
     */
    public function isGatewayEnabled(string $identifier): bool
    {
        return isset($this->gateways[$identifier]);
    }

    /**
     * Clear cached configurations (use after updating settings)
     */
    public function clearCache(): void
    {
        Cache::forget('payment_gateway_configs');
        $this->gateways = [];
        $this->configurations = [];
        $this->activeGateway = null;
        $this->loadConfigurations();
    }

    /**
     * Get frontend configuration for all enabled gateways
     */
    public function getFrontendConfigs(): array
    {
        $configs = [];

        foreach ($this->gateways as $identifier => $gateway) {
            $configs[$identifier] = $gateway->getFrontendConfig();
        }

        return $configs;
    }

    /**
     * Create a payment using the active gateway
     */
    public function createPayment(float $amount, string $currency = 'usd', array $options = []): array
    {
        $gateway = $this->getActiveGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'No payment gateway configured',
            ];
        }

        return $gateway->createPayment($amount, $currency, $options);
    }

    /**
     * Create a payment using a specific gateway
     */
    public function createPaymentWith(string $identifier, float $amount, string $currency = 'usd', array $options = []): array
    {
        $gateway = $this->gateway($identifier);

        if (!$gateway) {
            return [
                'success' => false,
                'error' => "Gateway '{$identifier}' not configured",
            ];
        }

        return $gateway->createPayment($amount, $currency, $options);
    }

    /**
     * Retrieve a payment from any enabled gateway
     */
    public function retrievePayment(string $paymentId, ?string $gatewayIdentifier = null): array
    {
        // If gateway specified, use it
        if ($gatewayIdentifier) {
            $gateway = $this->gateway($gatewayIdentifier);
            if ($gateway) {
                return $gateway->retrievePayment($paymentId);
            }
        }

        // Try active gateway first
        $gateway = $this->getActiveGateway();
        if ($gateway) {
            $result = $gateway->retrievePayment($paymentId);
            if ($result['success']) {
                return $result;
            }
        }

        // Try all enabled gateways
        foreach ($this->gateways as $gateway) {
            $result = $gateway->retrievePayment($paymentId);
            if ($result['success']) {
                return $result;
            }
        }

        return [
            'success' => false,
            'error' => 'Payment not found',
        ];
    }

    /**
     * Confirm a payment
     */
    public function confirmPayment(string $paymentId, array $options = [], ?string $gatewayIdentifier = null): array
    {
        $gateway = $gatewayIdentifier
            ? $this->gateway($gatewayIdentifier)
            : $this->getActiveGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'No payment gateway configured',
            ];
        }

        return $gateway->confirmPayment($paymentId, $options);
    }

    /**
     * Cancel a payment
     */
    public function cancelPayment(string $paymentId, ?string $gatewayIdentifier = null): array
    {
        $gateway = $gatewayIdentifier
            ? $this->gateway($gatewayIdentifier)
            : $this->getActiveGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'No payment gateway configured',
            ];
        }

        return $gateway->cancelPayment($paymentId);
    }

    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentId, ?float $amount = null, ?string $reason = null, ?string $gatewayIdentifier = null): array
    {
        $gateway = $gatewayIdentifier
            ? $this->gateway($gatewayIdentifier)
            : $this->getActiveGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'No payment gateway configured',
            ];
        }

        return $gateway->refundPayment($paymentId, $amount, $reason);
    }

    /**
     * Create a customer
     */
    public function createCustomer(array $customerData, ?string $gatewayIdentifier = null): array
    {
        $gateway = $gatewayIdentifier
            ? $this->gateway($gatewayIdentifier)
            : $this->getActiveGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'error' => 'No payment gateway configured',
            ];
        }

        return $gateway->createCustomer($customerData);
    }

    /**
     * Verify a webhook
     */
    public function verifyWebhook(string $payload, string $signature, string $gatewayIdentifier): array
    {
        $gateway = $this->gateway($gatewayIdentifier);

        if (!$gateway) {
            return [
                'success' => false,
                'error' => "Gateway '{$gatewayIdentifier}' not configured",
            ];
        }

        return $gateway->verifyWebhook($payload, $signature);
    }

    /**
     * Get supported payment methods for all enabled gateways
     */
    public function getAllSupportedMethods(): array
    {
        $methods = [];

        foreach ($this->gateways as $identifier => $gateway) {
            $methods[$identifier] = $gateway->getSupportedMethods();
        }

        return $methods;
    }
}
