# SEO Guide - Pecos River Traders

Comprehensive Search Engine Optimization strategy and implementation guide for the Pecos River Traders e-commerce platform.

## Table of Contents

1. [Current SEO Status](#current-seo-status)
2. [On-Page SEO](#on-page-seo)
3. [Technical SEO](#technical-seo)
4. [Content Strategy](#content-strategy)
5. [Local SEO](#local-seo)
6. [E-commerce SEO](#e-commerce-seo)
7. [Performance Optimization](#performance-optimization)
8. [Analytics & Monitoring](#analytics--monitoring)
9. [Implementation Checklist](#implementation-checklist)

---

## Current SEO Status

### Completed
- ✓ Responsive Bootstrap 5 design
- ✓ Clean URL structure
- ✓ Product detail pages
- ✓ Category organization
- ✓ Basic breadcrumb navigation
- ✓ Contact information page

### To Implement
- Meta descriptions for all pages
- Schema.org structured data
- XML sitemap
- robots.txt configuration
- Open Graph tags
- Image optimization
- Page speed improvements
- SSL/HTTPS (production)

---

## On-Page SEO

### Page Titles

Each page should have a unique, descriptive title following this format:

**Homepage:**
```html
<title>Pecos River Traders | Quality Western Boots, Footwear & Gear</title>
```

**Product Pages:**
```html
<title>[Product Name] | Pecos River Traders</title>
<!-- Example: -->
<title>Kakadu Classic Western Boot | Pecos River Traders</title>
```

**Category Pages:**
```html
<title>[Category Name] - Shop [Category Type] | Pecos River Traders</title>
<!-- Example: -->
<title>Men's Boots - Shop Western Footwear | Pecos River Traders</title>
```

**Implementation Example:**
```php
<?php
$pageTitle = "Product Name";
$siteName = "Pecos River Traders";
?>
<title><?php echo htmlspecialchars($pageTitle . ' | ' . $siteName); ?></title>
```

### Meta Descriptions

Include compelling 150-160 character descriptions on every page.

**Homepage:**
```html
<meta name="description" content="Shop quality Western boots, footwear, and gear at Pecos River Traders. Family-owned since 1985, offering authentic Western wear and exceptional service. Free shipping on orders $75+.">
```

**Product Pages:**
```html
<meta name="description" content="[Product Name] - [Key Features]. [Price] with free shipping over $75. In stock and ready to ship. Shop now at Pecos River Traders.">
```

**Category Pages:**
```html
<meta name="description" content="Browse our selection of [Category]. Quality [Category Type] with free shipping on orders $75+. Shop now at Pecos River Traders.">
```

### Header Tags (H1-H6)

Proper heading hierarchy for each page type:

**Product Detail Page:**
```html
<h1>Product Name</h1>
<h2>Product Description</h2>
<h3>Features</h3>
<h3>Specifications</h3>
<h3>Shipping Information</h3>
```

**Category Page:**
```html
<h1>Category Name</h1>
<p>Category description with keywords</p>
<h2 class="sr-only">Products</h2>
<!-- Product cards -->
```

### URL Structure

Current structure is good, maintain:
```
https://pecosrivertraders.com/
https://pecosrivertraders.com/products.php
https://pecosrivertraders.com/products.php?catid=5
https://pecosrivertraders.com/product-detail.php?id=123
https://pecosrivertraders.com/contact-us.php
```

**Future Enhancement (URL Rewriting):**
```apache
# .htaccess
RewriteEngine On
RewriteRule ^products/([0-9]+)$ products.php?catid=$1 [L]
RewriteRule ^product/([0-9]+)$ product-detail.php?id=$1 [L]
```

Result:
```
https://pecosrivertraders.com/products/5
https://pecosrivertraders.com/product/123
```

### Canonical URLs

Add to all pages to prevent duplicate content:

```html
<link rel="canonical" href="https://pecosrivertraders.com/products.php?catid=5">
```

```php
<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$canonical = $protocol . '://' . $host . $uri;
?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
```

---

## Technical SEO

### Structured Data (Schema.org)

#### Product Schema

Add to all product detail pages:

```php
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo htmlspecialchars($product['ShortDescription']); ?>",
  "image": "<?php echo 'https://pecosrivertraders.com/assets/' . $product['Image']; ?>",
  "description": "<?php echo htmlspecialchars($product['LngDescription']); ?>",
  "sku": "<?php echo $product['ItemNumber']; ?>",
  "brand": {
    "@type": "Brand",
    "name": "<?php echo htmlspecialchars($product['Category']); ?>"
  },
  "offers": {
    "@type": "Offer",
    "url": "<?php echo 'https://pecosrivertraders.com/product-detail.php?id=' . $product['ID']; ?>",
    "priceCurrency": "USD",
    "price": "<?php echo $product['UnitPrice']; ?>",
    "availability": "<?php echo ($product['QTY'] > 0) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'; ?>",
    "seller": {
      "@type": "Organization",
      "name": "Pecos River Traders"
    }
  }
}
</script>
```

#### Organization Schema

Add to homepage and footer:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Pecos River Traders",
  "image": "https://pecosrivertraders.com/assets/images/headerCenter.jpg",
  "telephone": "717-914-8124",
  "email": "contact@pecosrivertraders.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Your Street Address",
    "addressLocality": "Your City",
    "addressRegion": "PA",
    "postalCode": "Your Zip",
    "addressCountry": "US"
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
      "opens": "09:00",
      "closes": "18:00"
    },
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": "Saturday",
      "opens": "10:00",
      "closes": "16:00"
    }
  ],
  "sameAs": [
    "https://www.facebook.com/PecosRiverTraders",
    "https://www.instagram.com/pecosrivertraders"
  ]
}
</script>
```

#### Breadcrumb Schema

Add to product and category pages:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://pecosrivertraders.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Products",
      "item": "https://pecosrivertraders.com/products.php"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "<?php echo htmlspecialchars($product['Category']); ?>",
      "item": "https://pecosrivertraders.com/products.php?catid=<?php echo $product['CategoryCode']; ?>"
    }
  ]
}
</script>
```

### XML Sitemap

Create `sitemap.xml` in root directory:

```php
<?php
// generate-sitemap.php
require_once(__DIR__ . '/config/database.php');

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Homepage
echo '<url>';
echo '<loc>https://pecosrivertraders.com/</loc>';
echo '<changefreq>daily</changefreq>';
echo '<priority>1.0</priority>';
echo '</url>';

// Static pages
$pages = ['products.php', 'about-us.php', 'contact-us.php', 'events.php'];
foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>https://pecosrivertraders.com/' . $page . '</loc>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Product pages
$stmt = $dbConnect->query("SELECT ID, LastModified FROM products3 WHERE ID IS NOT NULL");
while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<url>';
    echo '<loc>https://pecosrivertraders.com/product-detail.php?id=' . $product['ID'] . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($product['LastModified'])) . '</lastmod>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

// Category pages
$stmt = $dbConnect->query("SELECT CategoryCode FROM categories WHERE IsBottom = 1");
while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<url>';
    echo '<loc>https://pecosrivertraders.com/products.php?catid=' . $cat['CategoryCode'] . '</loc>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
```

### robots.txt

Create `robots.txt` in root:

```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /config/
Disallow: /includes/
Disallow: /cart.php
Disallow: /checkout.php
Disallow: /account.php
Disallow: /account-settings.php

Sitemap: https://pecosrivertraders.com/sitemap.xml
```

### Open Graph Tags

Add to all pages for social media sharing:

```html
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
<meta property="og:url" content="<?php echo $canonical; ?>">
<meta property="og:image" content="<?php echo $ogImage; ?>">
<meta property="og:site_name" content="Pecos River Traders">
```

**Product Page Example:**
```php
<meta property="og:type" content="product">
<meta property="og:title" content="<?php echo htmlspecialchars($product['ShortDescription']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($product['LngDescription']); ?>">
<meta property="og:url" content="https://pecosrivertraders.com/product-detail.php?id=<?php echo $product['ID']; ?>">
<meta property="og:image" content="https://pecosrivertraders.com/assets/<?php echo $product['Image']; ?>">
<meta property="product:price:amount" content="<?php echo $product['UnitPrice']; ?>">
<meta property="product:price:currency" content="USD">
```

### Twitter Cards

```html
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
<meta name="twitter:image" content="<?php echo $ogImage; ?>">
```

---

## Content Strategy

### Product Descriptions

Each product should have:

1. **Short Description** (50-100 chars): Product title/name
2. **Long Description** (200-500 words): Detailed features, benefits, materials
3. **Key Features** (bullet points): Quick scan for users
4. **Size Guide**: Help with conversions
5. **Care Instructions**: Added value

**SEO-Optimized Product Description Template:**

```
[Product Name] - [Primary Keyword]

[Opening paragraph with main keyword and benefit]

FEATURES:
• [Feature 1 with keyword]
• [Feature 2]
• [Feature 3]

DETAILS:
[Detailed description with natural keyword usage, addressing customer questions]

SPECIFICATIONS:
• Material: [Material]
• Size: [Size options]
• Color: [Colors available]
• Care: [Care instructions]

SHIPPING & RETURNS:
Free shipping on orders over $75. 30-day return policy.
```

### Category Descriptions

Add 200-300 word descriptions to category pages:

**Example (Men's Boots):**
```
Shop our extensive collection of men's Western boots at Pecos River Traders.
Our selection includes classic cowboy boots, work boots, and dress boots from
top brands like Kakadu and [Brand]. Whether you're looking for traditional
leather boots or modern styles, we have the perfect pair for every occasion.

Our Western boots feature:
• Genuine leather construction
• Comfortable fit for all-day wear
• Traditional Western styling
• Durable soles for long-lasting performance

[Continue with benefits, size information, care tips, etc.]
```

### Blog Content Strategy

Create blog section for content marketing:

**Topics:**
- Western Boot Care Guide
- How to Choose the Right Boot Size
- Western Style Guide 2025
- Boot Breaking-In Tips
- Leather Care and Maintenance
- History of Western Footwear
- Event Coverage and Photos
- Customer Stories
- New Product Announcements

**Blog URL Structure:**
```
/blog/
/blog/how-to-choose-western-boots
/blog/boot-care-guide
```

---

## Local SEO

### Google Business Profile

1. **Claim Your Listing**: https://business.google.com
2. **Complete Profile**:
   - Business name: Pecos River Traders
   - Category: Shoe Store, Western Apparel Store
   - Address: [Full address]
   - Phone: 717-914-8124
   - Website: https://pecosrivertraders.com
   - Hours: Monday-Friday 9AM-6PM, Saturday 10AM-4PM
3. **Add Photos**: Storefront, products, interior
4. **Collect Reviews**: Ask satisfied customers
5. **Post Updates**: Events, sales, new products

### NAP Consistency

Ensure Name, Address, Phone are identical everywhere:

```
Pecos River Traders
[Street Address]
[City, State ZIP]
717-914-8124
```

Include on:
- Website footer
- Contact page
- Google Business Profile
- Social media profiles
- Online directories
- Citations

### Local Citations

Submit to:
- Yelp
- Yellow Pages
- Facebook Business
- Bing Places
- Apple Maps
- Chamber of Commerce
- Local business directories
- Industry-specific directories

---

## E-commerce SEO

### Product Optimization

**Each product needs:**
- Unique description (no manufacturer descriptions)
- High-quality images (alt text with keywords)
- Customer reviews (implement review system)
- Related products
- Size/fit information
- In-stock status
- Price clearly displayed
- Add to cart button visible
- Breadcrumb navigation

### Category Page Optimization

**Best practices:**
- Unique description at top
- Filter options (size, price, brand)
- Clear product grid
- Pagination with rel="next" and rel="prev"
- Product count display
- Sort options
- Mobile-friendly layout

### Product Images

**Image SEO:**
```html
<img src="assets/images/kakadu/boot-123.jpg"
     alt="Kakadu Classic Western Boot in Brown Leather"
     title="Kakadu Classic Western Boot"
     width="800"
     height="800"
     loading="lazy">
```

**Best practices:**
- Descriptive filenames: `kakadu-western-boot-brown.jpg`
- Alt text with keywords
- Compress images (target <200KB)
- Use WebP format with JPG fallback
- Implement lazy loading
- Set width/height to prevent layout shift

### Internal Linking

**Strategy:**
- Link related products
- Link categories from products
- Breadcrumb navigation
- "You may also like" section
- Footer navigation
- Related blog posts

---

## Performance Optimization

### Page Speed

**Critical for SEO:**
- Target: <2 seconds load time
- First Contentful Paint: <1.8s
- Largest Contentful Paint: <2.5s
- Cumulative Layout Shift: <0.1

**Optimizations:**

1. **Enable Compression** (gzip/brotli):
```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

2. **Browser Caching**:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

3. **Minify CSS/JS**:
- Use minified Bootstrap version
- Combine custom CSS files
- Remove unused CSS

4. **Database Optimization**:
- Add indexes to frequently queried columns
- Use LIMIT on queries
- Implement caching (Redis/Memcached)

5. **CDN Implementation**:
- Already using Bootstrap CDN ✓
- Consider image CDN (Cloudflare, CloudFront)

### Mobile Optimization

**Google Mobile-First Indexing:**
- Responsive design ✓ (Bootstrap 5)
- Touch-friendly buttons
- Readable font sizes
- No horizontal scrolling
- Fast mobile load time
- Mobile-friendly navigation ✓

---

## Analytics & Monitoring

### Google Analytics 4

**Setup:**

```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

**E-commerce Tracking:**

```javascript
// Product view
gtag('event', 'view_item', {
  items: [{
    item_id: "<?php echo $product['ItemNumber']; ?>",
    item_name: "<?php echo $product['ShortDescription']; ?>",
    price: <?php echo $product['UnitPrice']; ?>,
    item_category: "<?php echo $product['Category']; ?>"
  }]
});

// Add to cart
gtag('event', 'add_to_cart', {
  items: [{
    item_id: productId,
    item_name: productName,
    price: price,
    quantity: quantity
  }]
});
```

### Google Search Console

**Setup:**
1. Verify site ownership
2. Submit sitemap.xml
3. Monitor:
   - Index coverage
   - Search queries
   - Click-through rates
   - Mobile usability
   - Core Web Vitals

### Monitoring Tools

**Essential:**
- Google Analytics 4
- Google Search Console
- Google PageSpeed Insights
- GTmetrix
- SEMrush or Ahrefs (paid)
- Bing Webmaster Tools

**Track:**
- Organic traffic
- Keyword rankings
- Conversion rates
- Bounce rates
- Page load times
- Error pages (404s)
- Backlinks

---

## Implementation Checklist

### Phase 1: Foundation (Week 1-2)

- [ ] Add unique meta titles to all pages
- [ ] Add meta descriptions to all pages
- [ ] Implement canonical URLs
- [ ] Create robots.txt
- [ ] Generate XML sitemap
- [ ] Set up Google Analytics 4
- [ ] Set up Google Search Console
- [ ] Submit sitemap to Search Console

### Phase 2: Content (Week 3-4)

- [ ] Optimize product descriptions
- [ ] Add alt text to all images
- [ ] Create category descriptions
- [ ] Implement breadcrumb navigation
- [ ] Add structured data (Product schema)
- [ ] Add structured data (Organization schema)
- [ ] Add structured data (Breadcrumb schema)

### Phase 3: Technical (Week 5-6)

- [ ] Optimize images (compress, convert to WebP)
- [ ] Implement lazy loading
- [ ] Enable gzip compression
- [ ] Set up browser caching
- [ ] Optimize database queries
- [ ] Add page speed optimizations
- [ ] Implement SSL/HTTPS (production)
- [ ] Test mobile responsiveness

### Phase 4: Enhancement (Week 7-8)

- [ ] Add Open Graph tags
- [ ] Add Twitter Card tags
- [ ] Create Google Business Profile
- [ ] Build local citations
- [ ] Implement internal linking strategy
- [ ] Set up e-commerce tracking
- [ ] Add customer review system
- [ ] Create blog section

### Phase 5: Monitoring (Ongoing)

- [ ] Monitor search rankings
- [ ] Track organic traffic
- [ ] Analyze user behavior
- [ ] Fix crawl errors
- [ ] Update content regularly
- [ ] Build quality backlinks
- [ ] Collect customer reviews
- [ ] Create new blog content

---

## SEO Best Practices Summary

### DO:
✓ Write unique, descriptive content
✓ Use keywords naturally
✓ Optimize page load speed
✓ Ensure mobile-friendliness
✓ Build quality backlinks
✓ Collect customer reviews
✓ Update content regularly
✓ Monitor analytics

### DON'T:
✗ Keyword stuff
✗ Use duplicate content
✗ Buy backlinks
✗ Hide text or links
✗ Use doorway pages
✗ Ignore mobile users
✗ Neglect page speed
✗ Forget alt text on images

---

## Resources

### Tools
- [Google Analytics](https://analytics.google.com)
- [Google Search Console](https://search.google.com/search-console)
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [Schema.org](https://schema.org/)
- [Screaming Frog SEO Spider](https://www.screamingfrog.co.uk/)

### Learning
- [Google SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
- [Moz Beginner's Guide to SEO](https://moz.com/beginners-guide-to-seo)
- [Ahrefs Blog](https://ahrefs.com/blog/)

---

**Last Updated**: November 2025
**Version**: 1.0
