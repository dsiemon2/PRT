/**
 * Geolocation Handler
 * Manages browser geolocation requests and user location tracking
 */

class GeolocationManager {
    constructor(options = {}) {
        this.options = {
            // Auto-prompt on first visit
            autoPrompt: options.autoPrompt ?? true,
            // Use IP fallback if GPS denied
            ipFallback: options.ipFallback ?? true,
            // Show custom modal instead of browser prompt
            customModal: options.customModal ?? false,
            // Session storage key
            storageKey: 'prt_user_location',
            // Callback functions
            onSuccess: options.onSuccess || null,
            onError: options.onError || null,
            onDenied: options.onDenied || null,
            ...options
        };

        this.location = null;
        this.init();
    }

    init() {
        // Check if already have location
        const stored = this.getStoredLocation();
        if (stored && !this.isLocationExpired(stored)) {
            this.location = stored;
            if (this.options.onSuccess) {
                this.options.onSuccess(stored);
            }
            return;
        }

        // Check if user previously denied
        const deniedStatus = localStorage.getItem('prt_location_denied');
        if (deniedStatus === 'true') {
            this.tryIPFallback();
            return;
        }

        // Auto-prompt if enabled and first visit
        if (this.options.autoPrompt) {
            const prompted = sessionStorage.getItem('prt_location_prompted');
            if (!prompted) {
                setTimeout(() => this.promptUser(), 2000); // Delay 2 seconds after page load
                sessionStorage.setItem('prt_location_prompted', 'true');
            }
        }
    }

    /**
     * Show location permission modal
     */
    promptUser() {
        if (this.options.customModal) {
            this.showCustomModal();
        } else {
            this.requestBrowserLocation();
        }
    }

    /**
     * Show custom modal asking for location permission
     */
    showCustomModal() {
        // Check if modal already exists
        if (document.getElementById('locationModal')) {
            return;
        }

        const modal = document.createElement('div');
        modal.id = 'locationModal';
        modal.className = 'modal fade';
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-geo-alt-fill"></i> Know Your Location
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            We'd like to use your location to provide:
                        </p>
                        <ul class="list-unstyled mb-3">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <strong>Accurate shipping estimates</strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <strong>Local tax calculations</strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <strong>Faster checkout</strong> with pre-filled address
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                <strong>Delivery availability</strong> in your area
                            </li>
                        </ul>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-shield-check"></i>
                            Your privacy is important. We only use your location to improve your shopping experience.
                        </p>
                    </div>
                    <div class="modal-footer flex-column">
                        <button type="button" class="btn btn-primary w-100 mb-2" id="allowLocationBtn">
                            <i class="bi bi-geo-alt"></i> Allow Location
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mb-2" id="allowOnceBtn">
                            Allow This Time Only
                        </button>
                        <button type="button" class="btn btn-outline-danger w-100" id="denyLocationBtn">
                            Don't Allow
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Initialize Bootstrap modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Log that modal was shown
        this.logLocationAction('prompt_shown');

        // Event listeners
        document.getElementById('allowLocationBtn').addEventListener('click', () => {
            localStorage.removeItem('prt_location_denied');
            bsModal.hide();
            this.requestBrowserLocation();
        });

        document.getElementById('allowOnceBtn').addEventListener('click', () => {
            bsModal.hide();
            this.requestBrowserLocation(true); // One-time only
        });

        document.getElementById('denyLocationBtn').addEventListener('click', () => {
            localStorage.setItem('prt_location_denied', 'true');
            this.logLocationAction('denied');
            bsModal.hide();
            if (this.options.onDenied) {
                this.options.onDenied();
            }
            // Try IP fallback
            this.tryIPFallback();
        });

        // Clean up modal on hide
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    /**
     * Request browser geolocation
     */
    requestBrowserLocation(oneTime = false) {
        if (!navigator.geolocation) {
            console.error('Geolocation not supported');
            this.tryIPFallback();
            return;
        }

        const options = {
            enableHighAccuracy: false,
            timeout: 10000,
            maximumAge: oneTime ? 0 : 3600000 // 1 hour cache unless one-time
        };

        navigator.geolocation.getCurrentPosition(
            (position) => this.handleSuccess(position, oneTime),
            (error) => this.handleError(error),
            options
        );
    }

    /**
     * Handle successful location retrieval
     */
    handleSuccess(position, oneTime = false) {
        const locationData = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy,
            source: 'gps',
            timestamp: Date.now()
        };

        this.location = locationData;

        // Store in session (not localStorage for privacy)
        if (!oneTime) {
            sessionStorage.setItem(this.options.storageKey, JSON.stringify(locationData));
        }

        // Send to server
        this.saveToServer(locationData);

        // Log success
        this.logLocationAction('allowed');

        // Callback
        if (this.options.onSuccess) {
            this.options.onSuccess(locationData);
        }

        // Show success toast
        this.showToast('Location detected successfully!', 'success');
    }

    /**
     * Handle geolocation error
     */
    handleError(error) {
        console.error('Geolocation error:', error);

        let action = 'error';
        let message = 'Unable to get your location';

        switch (error.code) {
            case error.PERMISSION_DENIED:
                action = 'denied';
                message = 'Location access denied';
                localStorage.setItem('prt_location_denied', 'true');
                break;
            case error.POSITION_UNAVAILABLE:
                message = 'Location information unavailable';
                break;
            case error.TIMEOUT:
                action = 'timeout';
                message = 'Location request timed out';
                break;
        }

        this.logLocationAction(action);

        if (this.options.onError) {
            this.options.onError(error);
        }

        // Try IP fallback
        this.tryIPFallback();
    }

    /**
     * Try to get location from IP address (fallback)
     */
    async tryIPFallback() {
        if (!this.options.ipFallback) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/get-location-from-ip.php`);
            const data = await response.json();

            if (data.success) {
                const locationData = {
                    ...data.location,
                    source: 'ip',
                    timestamp: Date.now()
                };

                this.location = locationData;
                sessionStorage.setItem(this.options.storageKey, JSON.stringify(locationData));

                if (this.options.onSuccess) {
                    this.options.onSuccess(locationData);
                }
            }
        } catch (error) {
            console.error('IP fallback failed:', error);
        }
    }

    /**
     * Save location to server
     */
    async saveToServer(locationData) {
        try {
            const formData = new FormData();
            formData.append('latitude', locationData.latitude);
            formData.append('longitude', locationData.longitude);
            formData.append('source', locationData.source);
            formData.append('csrf_token', CSRF_TOKEN);

            await fetch(`${BASE_URL}/api/save-location.php`, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error saving location:', error);
        }
    }

    /**
     * Log location action for analytics
     */
    async logLocationAction(action) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('csrf_token', CSRF_TOKEN);

            await fetch(`${BASE_URL}/api/log-location-action.php`, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error logging location action:', error);
        }
    }

    /**
     * Get stored location from session
     */
    getStoredLocation() {
        const stored = sessionStorage.getItem(this.options.storageKey);
        return stored ? JSON.parse(stored) : null;
    }

    /**
     * Check if stored location is expired (older than 1 hour)
     */
    isLocationExpired(location) {
        const oneHour = 3600000; // milliseconds
        return (Date.now() - location.timestamp) > oneHour;
    }

    /**
     * Get current location (from memory, storage, or new request)
     */
    async getLocation() {
        // Return cached location if available and fresh
        if (this.location && !this.isLocationExpired(this.location)) {
            return this.location;
        }

        // Check storage
        const stored = this.getStoredLocation();
        if (stored && !this.isLocationExpired(stored)) {
            this.location = stored;
            return stored;
        }

        // Request new location
        return new Promise((resolve, reject) => {
            this.options.onSuccess = resolve;
            this.options.onError = reject;
            this.requestBrowserLocation();
        });
    }

    /**
     * Calculate shipping estimate based on location
     */
    async getShippingEstimate(weight = 1) {
        const location = await this.getLocation();
        if (!location) {
            return null;
        }

        try {
            const response = await fetch(
                `${BASE_URL}/api/estimate-shipping.php?lat=${location.latitude}&lon=${location.longitude}&weight=${weight}`
            );
            return await response.json();
        } catch (error) {
            console.error('Error getting shipping estimate:', error);
            return null;
        }
    }

    /**
     * Show Bootstrap toast notification
     */
    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'info'} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    /**
     * Clear stored location
     */
    clearLocation() {
        this.location = null;
        sessionStorage.removeItem(this.options.storageKey);
        localStorage.removeItem('prt_location_denied');
    }
}

// Global instance
window.geoManager = null;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize geolocation manager
    window.geoManager = new GeolocationManager({
        autoPrompt: true,
        customModal: true,
        ipFallback: true,
        onSuccess: (location) => {
            console.log('Location obtained:', location);
            // Update UI elements that depend on location
            updateLocationDependentUI(location);
        },
        onError: (error) => {
            console.log('Location error:', error);
        },
        onDenied: () => {
            console.log('Location denied by user');
        }
    });
});

/**
 * Update UI elements that depend on location
 */
function updateLocationDependentUI(location) {
    // Update shipping estimate if on cart/checkout page
    if (document.getElementById('shipping-estimate')) {
        updateShippingEstimate(location);
    }

    // Pre-fill address fields if on checkout page
    if (document.getElementById('checkout-form')) {
        prefillAddressFields(location);
    }

    // Show location indicator in navbar
    updateLocationIndicator(location);
}

/**
 * Update shipping estimate display
 */
async function updateShippingEstimate(location) {
    const estimate = await window.geoManager.getShippingEstimate(1);
    if (estimate && estimate.success) {
        const container = document.getElementById('shipping-estimate');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-truck"></i> <strong>Shipping to your area:</strong><br>
                    Standard (${estimate.standard.days} days): $${estimate.standard.cost}<br>
                    Express (${estimate.express.days} days): $${estimate.express.cost}
                </div>
            `;
        }
    }
}

/**
 * Pre-fill checkout address fields
 */
async function prefillAddressFields(location) {
    try {
        const response = await fetch(`${BASE_URL}/api/suggest-address.php`);
        const data = await response.json();

        if (data.success && data.address) {
            if (data.address.city) document.getElementById('city').value = data.address.city;
            if (data.address.state) document.getElementById('state').value = data.address.state;
            if (data.address.postal_code) document.getElementById('zip').value = data.address.postal_code;
            if (data.address.country) document.getElementById('country').value = data.address.country;
        }
    } catch (error) {
        console.error('Error prefilling address:', error);
    }
}

/**
 * Update location indicator in navbar
 */
function updateLocationIndicator(location) {
    const indicator = document.getElementById('location-indicator');
    if (indicator && location.source === 'gps') {
        indicator.innerHTML = `<i class="bi bi-geo-alt-fill text-success"></i>`;
        indicator.title = 'Location detected';
    }
}
