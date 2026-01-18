/**
 * Centralized Cart Functions
 *
 * Provides normalized add-to-cart functionality across all pages
 */

/**
 * Add product to cart with size validation
 *
 * @param {string} upc - Product UPC or ItemNumber
 * @param {number} catid - Category ID
 * @param {number} productId - Product ID (used to find size dropdown)
 */
function addToCart(upc, catid, productId) {
    // Get size from dropdown if exists
    const sizeSelect = document.getElementById('sizeSelect_' + productId);

    // If size dropdown exists, validate that a size is selected
    if (sizeSelect && !sizeSelect.value) {
        showToast('warning', '<i class="bi bi-exclamation-triangle"></i> Please select a size before adding to cart');
        sizeSelect.focus();
        sizeSelect.classList.add('is-invalid');
        setTimeout(() => sizeSelect.classList.remove('is-invalid'), 3000);
        return;
    }

    // Build URL based on current location
    const basePath = window.location.pathname.includes('/Products/') ||
                     window.location.pathname.includes('/products/') ||
                     window.location.pathname.includes('/auth/')
                     ? '../cart/AddToCart.php'
                     : 'cart/AddToCart.php';

    let url = basePath + '?upc=' + encodeURIComponent(upc) + '&catid=' + catid;

    // Add size to URL if selected
    if (sizeSelect && sizeSelect.value) {
        url += '&size=' + encodeURIComponent(sizeSelect.value);
    }

    window.location.href = url;
}

/**
 * Show toast notification
 *
 * @param {string} type - Toast type (success, warning, error, info)
 * @param {string} message - Message to display
 */
function showToast(type, message) {
    // Check if toast container exists, if not create it
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999;';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
    toast.style.cssText = 'min-width: 300px; max-width: 500px;';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    toastContainer.appendChild(toast);

    // Auto-dismiss after 4 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 150);
    }, 4000);
}
