@php
    use App\Services\FeaturesService;
    use App\Services\FooterService;

    // Get comparison count
    $comparisonCount = session('comparison', []);
    $comparisonCount = is_array($comparisonCount) ? count($comparisonCount) : 0;

    // Get services
    $footerFeaturesService = new FeaturesService();
    $footerService = new FooterService();
    $footerConfig = $footerService->getConfig();
@endphp

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            @foreach($footerConfig as $column)
                @if(!($column['is_visible'] ?? true))
                    @continue
                @endif

                @if(($column['column_type'] ?? 'links') === 'newsletter')
                    @if($footerFeaturesService->isEnabled('newsletter'))
                    <div class="col-12 col-md-3 mb-4">
                        <h5>{{ $column['title'] ?? 'Newsletter' }}</h5>
                        <p class="small">Get exclusive deals, new arrivals, and western wear tips!</p>
                        <form id="newsletterForm" class="newsletter-form">
                            @csrf
                            <div class="mb-2">
                                <input type="email" name="email" id="newsletter_email" class="form-control form-control-sm" placeholder="Your email" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-envelope"></i> Subscribe
                            </button>
                            <div id="newsletter-message" class="mt-2 small"></div>
                        </form>
                    </div>
                    @endif
                @else
                    <div class="col-12 col-md-3 mb-4">
                        <h5>{{ $column['title'] ?? 'Links' }}</h5>
                        <ul class="list-unstyled">
                            @foreach($column['links'] ?? [] as $link)
                                @php
                                    // Skip if link is not visible
                                    if (!($link['is_visible'] ?? true)) continue;

                                    // Skip if feature flag is set and feature is disabled
                                    $featureFlag = $link['feature_flag'] ?? null;
                                    if ($featureFlag && !$footerFeaturesService->isEnabled($featureFlag)) continue;

                                    // Build URL
                                    $linkUrl = $link['url'] ?? '#';
                                    $isExternal = (str_starts_with($linkUrl, 'http://') || str_starts_with($linkUrl, 'https://'));
                                    if (!$isExternal && !str_starts_with($linkUrl, '/')) {
                                        $linkUrl = '/' . $linkUrl;
                                    }
                                    $target = $isExternal ? ' target="_blank" rel="noopener"' : '';
                                @endphp
                                <li>
                                    <a href="{{ $isExternal ? $linkUrl : url($linkUrl) }}"{!! $target !!}>
                                        <i class="bi bi-chevron-right"></i> {{ $link['label'] ?? 'Link' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="footer-bottom text-center">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Product Comparison Widget -->
<div id="comparisonWidget" class="comparison-widget position-fixed bottom-0 end-0 m-3" style="z-index: 1040; {{ $comparisonCount > 0 ? '' : 'display: none;' }}">
    <a href="{{ route('products.compare') }}" class="btn btn-info btn-lg shadow-lg text-white">
        <i class="bi bi-arrow-left-right"></i>
        Compare (<span class="comparison-count">{{ $comparisonCount }}</span>)
    </a>
</div>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" title="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- Newsletter Signup Handler -->
<script>
document.getElementById('newsletterForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const messageDiv = document.getElementById('newsletter-message');
    const email = form.querySelector('#newsletter_email').value;

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subscribing...';

    try {
        const formData = new FormData(form);

        const response = await fetch('{{ url("/newsletter/subscribe") }}', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            messageDiv.className = 'mt-2 small text-success';
            messageDiv.textContent = data.message;
            form.reset();
        } else {
            messageDiv.className = 'mt-2 small text-danger';
            messageDiv.textContent = data.message;
        }
    } catch (error) {
        console.error('Newsletter signup error:', error);
        messageDiv.className = 'mt-2 small text-danger';
        messageDiv.textContent = 'An error occurred. Please try again.';
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-envelope"></i> Subscribe';
    }
});
</script>
