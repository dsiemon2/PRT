/**
 * Pecos River Traders - Main JavaScript
 * Modern interactive features for Bootstrap 5 site
 */

// Back to Top Button
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');

    if (backToTopButton) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        // Scroll to top when clicked
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Fade in animations - only for category cards, not product listings
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Only apply animation to category cards (not product cards to prevent flashing)
    document.querySelectorAll('.category-card').forEach(card => {
        observer.observe(card);
    });
});

// Shopping Cart Functions are now handled via AddToCart.php redirect
// No AJAX needed - simple page redirects work better for this use case

// Image lazy loading fallback for older browsers
if ('loading' in HTMLImageElement.prototype) {
    // Browser supports native lazy loading
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src || img.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}

// Form validation enhancement
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

// Product image zoom on hover (for product detail pages)
document.querySelectorAll('.product-image-zoom').forEach(image => {
    image.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
    });

    image.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

// Newsletter signup form
const newsletterForm = document.getElementById('newsletterForm');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = this.querySelector('input[type="email"]').value;
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Subscribing...';
        submitButton.disabled = true;

        // Send subscription request
        fetch('newsletter-signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                submitButton.innerHTML = '<i class="bi bi-check-circle"></i> Subscribed!';
                submitButton.classList.add('btn-success');
                this.reset();
            } else {
                submitButton.innerHTML = '<i class="bi bi-exclamation-circle"></i> Error';
                submitButton.classList.add('btn-danger');
            }

            setTimeout(() => {
                submitButton.innerHTML = originalText;
                submitButton.classList.remove('btn-success', 'btn-danger');
                submitButton.disabled = false;
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
}
