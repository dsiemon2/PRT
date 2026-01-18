# SEO & Marketing Implementation Guide

Complete guide for implementing and using the SEO & Marketing features in Pecos River Trading Company website.

## Table of Contents
1. [Overview](#overview)
2. [Meta Tags & Open Graph](#meta-tags--open-graph)
3. [Structured Data (Schema.org)](#structured-data-schemaorg)
4. [XML Sitemaps](#xml-sitemaps)
5. [Robots.txt](#robotstxt)
6. [Google Shopping Feed](#google-shopping-feed)
7. [Google Analytics](#google-analytics)
8. [Facebook Pixel](#facebook-pixel)
9. [Best Practices](#best-practices)

---

## Overview

All SEO functions are centralized in `includes/seo-functions.php` for easy reuse across pages.

### Quick Start

```php
<?php
require_once(__DIR__ . '/includes/seo-functions.php');
require_once(__DIR__ . '/config/tracking.php');
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    // Generate meta tags
    echo generateMetaTags([
        'title' => 'Your Page Title',
        'description' => 'Your page description',
        'image' => '/PRT2/assets/images/page-image.jpg',
        'type' => 'website'
    ]);

    // Add structured data
    echo generateOrganizationSchema();
    ?>

    <?php renderGoogleAnalytics(); ?>
    <?php renderFacebookPixel(); ?>
</head>
<body>
    <!-- Your content -->
</body>
</html>
```

---

## Meta Tags & Open Graph

### Using generateMetaTags()

The `generateMetaTags()` function automatically generates:
- Standard meta description and keywords
- Canonical URL
- Open Graph tags (Facebook, LinkedIn)
- Twitter Card tags

**Parameters:**

```php
$config = [
    'title' => 'Page Title',                    // REQUIRED
    'description' => 'Page description',        // REQUIRED
    'keywords' => 'keyword1, keyword2',         // Optional
    'image' => '/path/to/image.jpg',           // Optional (defaults to logo)
    'url' => $_SERVER['REQUEST_URI'],          // Optional (auto-detected)
    'type' => 'website',                       // Optional (website|article|product)
    'twitter_card' => 'summary_large_image',   // Optional
    'twitter_site' => '@YourHandle'            // Optional
];

echo generateMetaTags($config);
```

**Example for Product Page:**

```php
echo generateMetaTags([
    'title' => $product['ShortDescription'] . ' - Pecos River Trading Company',
    'description' => strip_tags($product['LongDescription']),
    'keywords' => 'cowboy boots, western wear, ' . $product['Category'],
    'image' => '/PRT2/assets/' . $product['Image'],
    'type' => 'product'
]);
```

**Example for Blog Post:**

```php
echo generateMetaTags([
    'title' => $post['meta_title'] ?: $post['title'],
    'description' => $post['meta_description'],
    'keywords' => $post['meta_keywords'],
    'image' => $post['featured_image'] ? '/PRT2/assets/' . $post['featured_image'] : '/PRT2/assets/images/blog-default.jpg',
    'type' => 'article'
]);
```

---

## Structured Data (Schema.org)

Structured data helps search engines understand your content better, leading to rich snippets in search results.

### Organization Schema

Add once on your homepage or in a global footer:

```php
echo generateOrganizationSchema();
```

This outputs:
- Business name, URL, logo
- Physical address
- Contact information
- Social media profiles

### Product Schema

Add to product detail pages:

```php
// After fetching product data
echo generateProductSchema($product);
```

**Required product fields:**
- ShortDescription (name)
- Image
- UnitPrice
- ItemNumber (SKU)

**Optional but recommended:**
- LongDescription
- Category
- UPC/GTIN
- Stock status

### Breadcrumb Schema

Add to any page with breadcrumb navigation:

```php
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Products', 'url' => '/Products/products.php'],
    ['name' => $product['ShortDescription'], 'url' => $_SERVER['REQUEST_URI']]
];

echo generateBreadcrumbSchema($breadcrumbs);
```

### FAQ Schema

Add to your FAQ page:

```php
// Convert your FAQs to the required format
$faqData = array_map(function($faq) {
    return [
        'question' => $faq['question'],
        'answer' => $faq['answer']
    ];
}, $faqs);

echo generateFAQSchema($faqData);
```

### Blog Post Schema

Add to individual blog post pages:

```php
echo generateBlogPostSchema($post);
```

---

## XML Sitemaps

### Main Sitemap Index

Access: `http://yourdomain.com/PRT2/sitemap.xml.php`

Points to all sub-sitemaps.

### Pages Sitemap

File: `sitemap-pages.xml.php`

Contains:
- Homepage
- Product listing
- Blog index
- FAQ page
- About, Contact, Policies
- Gift cards

**To add new pages:**

Edit `sitemap-pages.xml.php` and add to the `$pages` array:

```php
$pages = [
    ['url' => '/new-page.php', 'priority' => '0.6', 'changefreq' => 'monthly'],
];
```

### Products Sitemap

File: `sitemap-products.xml.php`

Automatically generated from database. Includes:
- All products with descriptions
- Product images (sitemap image extension)
- Last modified dates

### Blog Sitemap

File: `sitemap-blog.xml.php`

Automatically generated from published blog posts.

### Submit to Search Engines

**Google Search Console:**
1. Verify your site
2. Go to Sitemaps section
3. Submit: `https://yourdomain.com/PRT2/sitemap.xml.php`

**Bing Webmaster Tools:**
1. Verify your site
2. Submit sitemap URL

---

## Robots.txt

File: `robots.txt` (root of PRT2 directory)

### What's Blocked

- Admin areas (`/admin/`, `/config/`)
- Authentication pages (`/auth/`)
- Cart and checkout (no SEO value)
- Handler scripts (`*-handler.php`)
- Search result pages (duplicate content)

### What's Allowed

- Product listings and details
- Blog posts
- Information pages
- Static content

### Customization

To block additional paths:

```
Disallow: /path-to-block/
```

To allow specific files in blocked directory:

```
Disallow: /admin/
Allow: /admin/public-page.php
```

---

## Google Shopping Feed

File: `google-shopping-feed.xml.php`

Access: `http://yourdomain.com/PRT2/google-shopping-feed.xml.php`

### Setup in Google Merchant Center

1. Create/Sign in to Google Merchant Center account
2. Go to Products > Feeds
3. Click "+" to create new feed
4. Select:
   - Country: United States
   - Language: English
5. Choose "Scheduled fetch"
6. Enter feed URL: `https://yourdomain.com/PRT2/google-shopping-feed.xml.php`
7. Set fetch frequency: Daily
8. Click "Create Feed"

### Feed Contents

For each product:
- ID (ItemNumber)
- Title (ShortDescription)
- Description
- Link (product URL)
- Image URL
- Price (with USD currency)
- Availability (in stock/out of stock/limited)
- GTIN/UPC (if available)
- Brand
- Condition (new)
- Google Product Category
- Size (if available)
- MPN (ItemNumber)

### Customization

To improve category mapping, edit `google-shopping-feed.xml.php`:

```php
// Category mapping
$googleCategory = 'Apparel & Accessories > Clothing';
if (stripos($product['Category'], 'boots') !== false) {
    $googleCategory = 'Apparel & Accessories > Shoes > Boots';
}
```

[Google Product Category Taxonomy](https://support.google.com/merchants/answer/6324436)

---

## Google Analytics

### Setup

1. Create Google Analytics 4 property
2. Get your Measurement ID (format: G-XXXXXXXXXX)
3. Edit `config/tracking.php`:

```php
define('GA_MEASUREMENT_ID', 'G-YOUR-ID-HERE');
define('GA_ENABLED', true);
```

### Implementation

Add to all pages in `<head>`:

```php
<?php
require_once(__DIR__ . '/config/tracking.php');
renderGoogleAnalytics();
?>
```

### Features Included

- Page view tracking (automatic)
- Anonymized IP addresses (GDPR compliant)
- E-commerce purchase tracking
- Cookie consent flags

### E-commerce Tracking

Purchase events are automatically tracked when orders complete. The tracking includes:
- Transaction ID
- Total value
- Currency
- Item details (ID, name, price, quantity)

---

## Facebook Pixel

### Setup

1. Create Facebook Business Manager account
2. Create Pixel in Events Manager
3. Get your Pixel ID (15-16 digit number)
4. Edit `config/tracking.php`:

```php
define('FB_PIXEL_ID', 'YOUR-PIXEL-ID-HERE');
define('FB_PIXEL_ENABLED', true);
```

### Implementation

Add to all pages in `<head>`:

```php
<?php
require_once(__DIR__ . '/config/tracking.php');
renderFacebookPixel();
?>
```

### Events Tracked

**1. PageView (Automatic)**
Fires on every page load.

**2. ViewContent (Product Pages)**

```php
// On product detail page
trackProductView($product);
```

**3. AddToCart**

```php
// After adding to cart
trackAddToCart($product, $quantity);
```

**4. Purchase (Order Confirmation)**

```php
// On order confirmation page
trackPurchase($orderTotal, $orderId, $items);
```

### Custom Events

Add custom tracking:

```php
if (FB_PIXEL_ENABLED) {
    ?>
<script>
fbq('track', 'CustomEvent', {
    custom_param: 'value'
});
</script>
    <?php
}
```

---

## Best Practices

### Meta Descriptions
- Keep under 160 characters
- Include primary keyword
- Make it compelling (drives click-through)
- Unique for every page

### Title Tags
- Keep under 60 characters
- Format: "Primary Keyword - Brand Name"
- Include target keywords
- Unique for every page

### Images
- Use descriptive filenames (`cowboy-boots-brown.jpg` vs `IMG001.jpg`)
- Add alt text to all images
- Compress images (under 100KB when possible)
- Use WebP format for better compression

### Structured Data
- Validate with [Google Rich Results Test](https://search.google.com/test/rich-results)
- Keep data accurate and up-to-date
- Don't mark up hidden content
- Use most specific type available

### Internal Linking
- Link to related products
- Use descriptive anchor text
- Create content hubs (category pages)
- Implement breadcrumbs on all pages

### Mobile Optimization
- All pages must be mobile-responsive
- Test with Google Mobile-Friendly Test
- Optimize for Core Web Vitals
- Use responsive images

### Page Speed
- Minimize HTTP requests
- Enable GZIP compression
- Leverage browser caching
- Minify CSS/JS files
- Use CDN for static assets

### Content Strategy
- Regular blog posts (weekly ideal)
- Product descriptions >300 words
- Unique content (no duplicates)
- Answer customer questions (FAQ)
- User-generated content (reviews)

---

## Testing & Validation

### Meta Tags
- [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
- [Twitter Card Validator](https://cards-dev.twitter.com/validator)
- [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/)

### Structured Data
- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [Schema.org Validator](https://validator.schema.org/)

### Sitemaps
- [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html)
- Google Search Console (submit and check for errors)

### Overall SEO
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [GTmetrix](https://gtmetrix.com/)
- [Screaming Frog SEO Spider](https://www.screamingfrogseoseo.co.uk/)

---

## Maintenance

### Weekly
- Check Google Search Console for errors
- Review Analytics for traffic changes
- Monitor page speed

### Monthly
- Update sitemap (automatic for dynamic pages)
- Review and optimize underperforming pages
- Check for broken links
- Update meta descriptions based on performance

### Quarterly
- Full SEO audit
- Competitor analysis
- Update structured data
- Review and refresh old blog posts

---

## Troubleshooting

### Sitemap not showing products
- Check database connection in `sitemap-products.xml.php`
- Verify products have `ShortDescription`
- Check file permissions

### Structured data errors
- Validate JSON-LD syntax
- Ensure all required fields present
- Check for special characters (escape properly)

### Analytics not tracking
- Verify Measurement ID is correct
- Check GA_ENABLED is `true`
- Look for browser console errors
- Test with Google Tag Assistant

### Facebook Pixel not firing
- Verify Pixel ID is correct
- Check FB_PIXEL_ENABLED is `true`
- Use Facebook Pixel Helper browser extension
- Check browser console for errors

---

## Support Resources

- **Google Search Central**: https://developers.google.com/search
- **Schema.org**: https://schema.org/
- **Open Graph Protocol**: https://ogp.me/
- **Google Merchant Center Help**: https://support.google.com/merchants
- **Google Analytics Help**: https://support.google.com/analytics
- **Facebook Business Help**: https://www.facebook.com/business/help

---

## Next Steps

1. **Enable Analytics**
   - Get GA4 Measurement ID
   - Update `config/tracking.php`
   - Test tracking with real data

2. **Enable Facebook Pixel**
   - Create Business Manager account
   - Get Pixel ID
   - Update `config/tracking.php`
   - Verify events with Pixel Helper

3. **Submit Sitemaps**
   - Verify site in Google Search Console
   - Submit sitemap URL
   - Monitor for errors

4. **Setup Google Merchant Center**
   - Create account
   - Add feed URL
   - Fix any data quality issues

5. **Monitor & Optimize**
   - Weekly: Check Search Console
   - Monthly: Analyze traffic patterns
   - Quarterly: Full SEO audit

---

**Last Updated**: November 18, 2025
