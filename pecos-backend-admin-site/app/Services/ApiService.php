<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.api.base_url', 'http://localhost:8300/api/v1');
        $this->timeout = config('services.api.timeout', 30);
    }

    /**
     * Make a GET request to the API.
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API GET request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('API GET request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Make a POST request to the API.
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API POST request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('API POST request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Make a PUT request to the API.
     */
    public function put(string $endpoint, array $data = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->put($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Make a DELETE request to the API.
     */
    public function delete(string $endpoint): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->delete($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ];
        }
    }

    // =====================
    // AUTHENTICATION
    // =====================

    public function login(string $email, string $password): array
    {
        return $this->post('/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    // =====================
    // PRODUCTS
    // =====================

    public function getProducts(array $params = []): array
    {
        return $this->get('/products', $params);
    }

    public function getProduct(string $upc): array
    {
        return $this->get("/products/{$upc}");
    }

    public function getCategories(): array
    {
        return $this->get('/categories');
    }

    // =====================
    // ORDERS
    // =====================

    public function getAdminOrders(array $params = []): array
    {
        return $this->get('/admin/orders', $params);
    }

    public function getAdminOrderStats(): array
    {
        return $this->get('/admin/orders/stats');
    }

    public function getAdminOrder(int $id): array
    {
        return $this->get("/admin/orders/{$id}");
    }

    public function updateOrderStatus(int $id, string $status, string $notes = ''): array
    {
        return $this->put("/admin/orders/{$id}/status", [
            'status' => $status,
            'notes' => $notes
        ]);
    }

    // =====================
    // CUSTOMERS
    // =====================

    public function getAdminCustomers(array $params = []): array
    {
        return $this->get('/admin/customers', $params);
    }

    public function getAdminCustomerStats(): array
    {
        return $this->get('/admin/customers/stats');
    }

    public function getAdminCustomer(int $id): array
    {
        return $this->get("/admin/customers/{$id}");
    }

    public function getAdminCustomerOrders(int $id): array
    {
        return $this->get("/admin/customers/{$id}/orders");
    }

    // =====================
    // INVENTORY
    // =====================

    public function getInventoryStats(): array
    {
        return $this->get('/admin/inventory/stats');
    }

    public function getInventoryProducts(array $params = []): array
    {
        return $this->get('/admin/inventory/products', $params);
    }

    public function getInventoryAlerts(): array
    {
        return $this->get('/admin/inventory/alerts');
    }

    public function getStockAlerts(): array
    {
        return $this->get('/admin/inventory/stock-alerts');
    }

    public function getInventoryReports(string $reportType = 'valuation'): array
    {
        return $this->get('/admin/inventory/reports-export', ['report' => $reportType]);
    }

    public function getBulkUpdateProducts(array $params = []): array
    {
        return $this->get('/admin/inventory/bulk-products', $params);
    }

    public function bulkAdjustManual(array $data): array
    {
        return $this->post('/admin/inventory/bulk-adjust-manual', $data);
    }

    public function bulkAdjustCsv(array $data): array
    {
        return $this->post('/admin/inventory/bulk-adjust-csv', $data);
    }

    public function getInventoryExportData(array $params = []): array
    {
        return $this->get('/admin/inventory/export-data', $params);
    }

    // =====================
    // PURCHASE ORDERS
    // =====================

    public function getPurchaseOrders(array $params = []): array
    {
        return $this->get('/admin/purchase-orders', $params);
    }

    public function getPurchaseOrder(int $id): array
    {
        return $this->get("/admin/purchase-orders/{$id}");
    }

    public function createPurchaseOrder(array $data): array
    {
        return $this->post('/admin/purchase-orders', $data);
    }

    public function updatePurchaseOrder(int $id, array $data): array
    {
        return $this->put("/admin/purchase-orders/{$id}", $data);
    }

    public function updatePurchaseOrderStatus(int $id, string $status): array
    {
        return $this->put("/admin/purchase-orders/{$id}/status", ['status' => $status]);
    }

    public function receivePurchaseOrder(int $id, array $items): array
    {
        return $this->post("/admin/purchase-orders/{$id}/receive", ['items' => $items]);
    }

    public function deletePurchaseOrder(int $id): array
    {
        return $this->delete("/admin/purchase-orders/{$id}");
    }

    public function getPurchaseOrderStats(): array
    {
        return $this->get('/admin/purchase-orders/stats');
    }

    public function getPurchaseOrderSuppliers(): array
    {
        return $this->get('/admin/purchase-orders/suppliers');
    }

    public function getPendingReceiving(): array
    {
        return $this->get('/admin/purchase-orders/pending-receiving');
    }

    // =====================
    // DROPSHIPPERS
    // =====================

    public function getDropshippers(array $params = []): array
    {
        return $this->get('/admin/dropshippers', $params);
    }

    // =====================
    // SUPPLIERS
    // =====================

    public function getSuppliers(array $params = []): array
    {
        return $this->get('/admin/suppliers', $params);
    }

    public function getSupplier(int $id): array
    {
        return $this->get("/admin/suppliers/{$id}");
    }

    public function createSupplier(array $data): array
    {
        return $this->post('/admin/suppliers', $data);
    }

    public function updateSupplier(int $id, array $data): array
    {
        return $this->put("/admin/suppliers/{$id}", $data);
    }

    public function updateSupplierStatus(int $id, string $status): array
    {
        return $this->put("/admin/suppliers/{$id}/status", ['status' => $status]);
    }

    public function deleteSupplier(int $id): array
    {
        return $this->delete("/admin/suppliers/{$id}");
    }

    public function getSupplierStats(): array
    {
        return $this->get('/admin/suppliers/stats');
    }

    // =====================
    // CONTENT
    // =====================

    public function getAdminBlog(array $params = []): array
    {
        return $this->get('/admin/blog', $params);
    }

    public function getBlogCategories(): array
    {
        return $this->get('/blog/categories');
    }

    public function getBlogPost($id): array
    {
        return $this->get('/admin/blog/' . $id);
    }

    public function getAdminReviews(array $params = []): array
    {
        return $this->get('/admin/reviews', $params);
    }

    public function getAdminEvents(array $params = []): array
    {
        return $this->get('/admin/events', $params);
    }

    public function getFaqStats(): array
    {
        return $this->get('/admin/faq-stats');
    }

    // =====================
    // GIFT CARDS
    // =====================

    public function getGiftCards(array $params = []): array
    {
        return $this->get('/admin/gift-cards', $params);
    }

    public function getGiftCardStats(): array
    {
        return $this->get('/admin/gift-cards/stats');
    }

    public function getGiftCard(int $id): array
    {
        return $this->get("/admin/gift-cards/{$id}");
    }

    // =====================
    // COUPONS
    // =====================

    public function getCoupons(array $params = []): array
    {
        return $this->get('/admin/coupons', $params);
    }

    // =====================
    // USERS
    // =====================

    public function getUsers(array $params = []): array
    {
        return $this->get('/admin/users', $params);
    }

    public function getUserStats(): array
    {
        return $this->get('/admin/users/stats');
    }

    // =====================
    // LOYALTY
    // =====================

    public function getLoyaltyStats(): array
    {
        return $this->get('/admin/loyalty/stats');
    }

    public function getLoyaltyMembers(array $params = []): array
    {
        return $this->get('/admin/loyalty/members', $params);
    }

    public function getLoyaltyTiers(): array
    {
        return $this->get('/admin/loyalty/tiers');
    }

    // =====================
    // CRM
    // =====================

    public function getCustomer360(int $id): array
    {
        return $this->get("/admin/crm/customers/{$id}/360");
    }

    public function getCustomerMetrics(int $id): array
    {
        return $this->get("/admin/crm/customers/{$id}/metrics");
    }

    public function getCustomerTags(int $id): array
    {
        return $this->get("/admin/crm/customers/{$id}/tags");
    }

    public function getCustomerActivities(int $id, array $params = []): array
    {
        return $this->get("/admin/crm/customers/{$id}/activities", $params);
    }

    public function getCustomerNotes(int $id): array
    {
        return $this->get("/admin/crm/customers/{$id}/notes");
    }

    public function getCrmTags(): array
    {
        return $this->get('/admin/crm/tags');
    }

    public function getCrmSegments(): array
    {
        return $this->get('/admin/crm/segments');
    }
}
