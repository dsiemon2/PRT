
# 🌍 International E-Commerce Tax Guide  
**How Modern E-Commerce Systems Handle Global Tax Rates**

This guide explains how e-commerce platforms (Shopify, WooCommerce, BigCommerce, custom sites) handle international taxes such as VAT, GST, OSS/IOSS, and US state sales tax. It includes reference tables, platform workflows, and sample code.

---

# 1. Tax Types Around the World

| Region | Tax Type | Notes |
|--------|----------|-------|
| USA | Sales Tax | Varies by state/county/city. Based on economic nexus. |
| EU | VAT | Charged based on buyer's country. Supports OSS/IOSS. |
| UK | VAT | Required if selling into the UK. |
| Canada | GST/HST/PST | Provinces differ. |
| Australia | GST | Foreign sellers must collect it. |
| New Zealand | GST | Required on imports. |

---

# 2. How Platforms Handle Taxes

## 2.1 Shopify
- Automatic tax calculations for U.S., EU, UK, AUS, NZ, Norway, etc.
- Supports:
  - **VAT OSS/IOSS**
  - **Tax-inclusive pricing**
  - **Product exemptions**
- Automatically tracks **nexus** and threshold rules.

## 2.2 WooCommerce / WordPress
- Uses tax tables (manual or automated).
- Supports GEO IP detection.
- Plugins for automation:
  - WooCommerce Tax
  - TaxJar
  - Avalara AvaTax

## 2.3 BigCommerce
- Native Avalara AvaTax integration.
- International tax zones.
- Full VAT/GST support.

## 2.4 Custom E-Commerce (Node.js, PHP, .NET, etc.)
Most custom implementations use tax APIs:

- **Stripe Tax**
- **TaxJar**
- **Avalara**
- **Vertex**

These APIs provide:
- Automated tax calculation
- VAT ID validation
- Real-time VAT/GST rates
- State-level U.S. sales tax
- Monthly filing automation (optional)

---

# 3. International Tax Calculation Flow

## Step 1 — Identify Buyer Location
- Shipping address (primary)
- Billing address
- IP geolocation (fallback)

## Step 2 — Determine Seller Obligations
Platforms check:
- VAT/GST registrations
- US economic nexus thresholds
- OSS/IOSS enrollment
- Custom exemptions (e.g., B2B reverse charge)

## Step 3 — Calculate Tax Rate
Based on:
- Country rules
- Region rules (U.S., Canada, Australia)
- Whether prices include/exclude tax
- Buyer type (B2B vs B2C)

## Step 4 — Collect and Store
Platforms store:
- Taxes per region
- VAT breakdowns
- B2B reversible VAT logs
- Monthly report summaries

## Step 5 — Invoice Requirements
EU/UK/AUS require:
- Tax ID (if applicable)
- VAT breakdown
- Reverse charge statements for B2B buyers

---

# 4. Special International Rules

## 4.1 EU OSS/IOSS
If selling into the EU:
- Register for **OSS** (for EU-to-EU transactions)
- Register for **IOSS** (imports under €150)
- Charge VAT based on buyer’s country
- Submit a single VAT return in an EU hub country

## 4.2 B2B Reverse Charge (EU)
If buyer enters a valid VAT ID:
- VAT is **not charged**
- Invoice must show:

> “VAT Reverse Charge — customer liable for VAT under Article 194.”

Systems validate VAT IDs using **VIES API**.

## 4.3 U.S. Economic Nexus
Charge sales tax where your business:
- Has physical presence **or**
- Meets thresholds (e.g., $100,000 revenue or 200 orders in a state)

Shopify, Stripe Tax, and TaxJar handle this automatically.

---

# 5. Built-In Tax Engines vs API Services

| Method | Pros | Cons |
|--------|------|------|
| **Platform-built (Shopify/Woo)** | Easy setup, global coverage | Less flexibility |
| **API Services (Stripe Tax/Avalara/TaxJar)** | Highly accurate, global, automated filings | Monthly cost |
| **Manual tax tables** | Total control | High maintenance, error-prone |

---

# 6. Examples: Real Code

## 6.1 Stripe Tax Example (Node.js)

```js
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);

const session = await stripe.checkout.sessions.create({
  line_items: [
    {
      price_data: {
        currency: 'usd',
        unit_amount: 4999,
        product_data: { name: "Denim Jacket" }
      },
      quantity: 1
    }
  ],
  mode: 'payment',
  automatic_tax: { enabled: true },
  shipping_address_collection: {
    allowed_countries: ['US','CA','GB','AU','DE','FR']
  }
});

console.log(session.url);
```

---

## 6.2 TaxJar Example (PHP)

```php
$client = TaxJar\Client::withApiKey("TAXJAR_API_KEY");

$orderTax = $client->taxForOrder([
    'from_country' => 'US',
    'from_zip' => '84043',
    'to_country' => 'GB',
    'to_zip' => 'N1 9GU',
    'amount' => 49.99,
    'shipping' => 5.00,
]);

echo $orderTax->amount_to_collect;
```

---

# 7. Recommended Setup by Store Type

## Shopify
- Enable **Automatic Taxes**
- Enter your tax registrations
- Shopify does:
  - U.S. sales tax  
  - EU VAT (OSS/IOSS)  
  - UK VAT  
  - AUS/NZ GST  

## WooCommerce
Install one:
- WooCommerce Tax
- TaxJar plugin
- Avalara

Enable GEOIP.

## Custom Store
Use **Stripe Tax** or **TaxJar** for:
- VAT/GST automation  
- U.S. nexus handling  
- Global tax updates  

---

# 8. Summary

Modern platforms use:
- Buyer location detection  
- Merchant obligations  
- Tax engines  
- VAT/GST rules  
- OSS/IOSS  
- Reverse charge for B2B  
- Real-time tax updates  

---

If you'd like additional `.md` files (payment gateways, live chat options, fulfillment models), just ask!
