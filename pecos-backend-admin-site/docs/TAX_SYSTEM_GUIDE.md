# Tax System Guide

**Last Updated:** November 28, 2025

## Overview

This document explains how the Pecos & Company tax system works, including the database structure, admin configuration, API endpoints, frontend integration, and handling of international orders.

### Tax Provider Options

The system supports **three tax calculation providers**:

| Provider | Cost | Features | Best For |
|----------|------|----------|----------|
| **Custom Tax Table** | Free | Admin-configured rates, full control, offline | Simple US-only sales |
| **Stripe Tax** | 0.5%/txn | Auto rates, global, nexus tracking | Stripe users, automation |
| **TaxJar** | $19-99/mo | Multi-channel, auto filing, API | High volume, compliance |

Configure the provider in **Admin > Features > Tax Calculation Provider**.

---

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Tax Providers](#tax-providers)
3. [Database Tables](#database-tables)
4. [Admin Panel Configuration](#admin-panel-configuration)
5. [API Endpoints](#api-endpoints)
6. [Frontend Integration](#frontend-integration)
7. [International Orders](#international-orders)
8. [Tax Calculation Flow](#tax-calculation-flow)

---

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        ADMIN PANEL                               │
│              http://localhost:8301/admin/settings/tax            │
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │  Tax Rates   │  │  Settings    │  │  Exemptions  │           │
│  │  by State    │  │  & Classes   │  │  Management  │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ API Calls
┌─────────────────────────────────────────────────────────────────┐
│                        API (Laravel)                             │
│              http://localhost:8300/api/v1/admin/tax              │
│                                                                  │
│  TaxController.php                                               │
│  - GET  /rates          - Get all tax rates                     │
│  - POST /rates          - Create tax rate                       │
│  - PUT  /rates/{id}     - Update tax rate                       │
│  - GET  /settings       - Get tax settings                      │
│  - POST /settings       - Update tax settings                   │
│  - GET  /classes        - Get tax classes                       │
│  - GET  /exemptions     - Get customer exemptions               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ Database Queries
┌─────────────────────────────────────────────────────────────────┐
│                      MySQL Database                              │
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │  tax_rates   │  │ tax_settings │  │ tax_classes  │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
│                        ┌──────────────┐                          │
│                        │tax_exemptions│                          │
│                        └──────────────┘                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼ Dynamic Tax Calculation
┌─────────────────────────────────────────────────────────────────┐
│                     FRONTEND (PRT2)                              │
│              http://localhost:80/cart/checkout.php               │
│                                                                  │
│  ✅ Dynamic tax calculation based on shipping address           │
│  ✅ Country dropdown (US, CA, MX, Other International)          │
│  ✅ State/Province dropdown updates per country                 │
│  ✅ AJAX recalculation on address changes                       │
│  ✅ Export exempt for international orders                      │
│                                                                  │
│  Files:                                                          │
│  - includes/tax-helpers.php (API client)                         │
│  - cart/calculate-tax.php (AJAX endpoint)                        │
│  - cart/checkout.php (UI and calculations)                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Database Tables

### tax_rates

Stores tax rates by country, state/province, and city.

```sql
CREATE TABLE tax_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(2) NOT NULL,       -- US, CA, MX
    state_code VARCHAR(10),                  -- TX, ON, BCN, etc.
    state_name VARCHAR(100),                 -- Texas, Ontario, etc.
    city VARCHAR(100),                       -- NULL for state-level rates
    rate DECIMAL(6,3) NOT NULL,              -- 8.250 = 8.25%
    is_compound TINYINT(1) DEFAULT 0,        -- Apply after other taxes
    tax_shipping TINYINT(1) DEFAULT 0,       -- Apply to shipping cost
    is_active TINYINT(1) DEFAULT 1,          -- Enable/disable rate
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Sample Data:**
| country_code | state_code | state_name | city | rate |
|--------------|------------|------------|------|------|
| US | TX | Texas | NULL | 6.250 |
| US | TX | Texas | Austin | 2.000 |
| US | PA | Pennsylvania | NULL | 6.000 |
| CA | ON | Ontario | NULL | 13.000 |
| MX | NULL | NULL | NULL | 16.000 |

### tax_settings

Global tax configuration settings.

```sql
CREATE TABLE tax_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Available Settings:**
| Key | Values | Description |
|-----|--------|-------------|
| `tax_enabled` | 1/0 | Master toggle for tax calculation |
| `tax_calculation_address` | shipping/billing/store | Which address to use for tax |
| `tax_display_mode` | excluding/including | Show prices with or without tax |
| `tax_round_at_subtotal` | 1/0 | Round at subtotal vs per-line |

### tax_classes

Categories for different tax treatment.

```sql
CREATE TABLE tax_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,              -- "Standard", "Reduced Rate"
    description VARCHAR(255),
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Default Classes:**
- **Standard** - Default rate for most products
- **Reduced Rate** - Lower rate for clothing (some states)
- **Zero Rate** - No tax (gift cards, exempt items)

### tax_exemptions

Customer-specific tax exemptions.

```sql
CREATE TABLE tax_exemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                    -- Foreign key to users
    exemption_type ENUM('resale', 'nonprofit', 'government', 'other'),
    certificate_number VARCHAR(100),          -- Resale certificate number
    reason VARCHAR(255),                      -- Explanation
    expires_at DATE,                          -- NULL = no expiration
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Admin Panel Configuration

### Location
**URL:** http://localhost:8301/admin/settings/tax

### Features

#### 1. Tax Rates Table
- View all rates grouped by country
- Expandable state rows to show city/local taxes
- Inline editing of rate, compound, tax shipping, active
- Bulk save all changes
- Filter by country (US, CA, MX)

#### 2. General Settings
- **Enable Taxes** - Master on/off toggle
- **Calculate Based On** - Shipping address (default), billing, or store address
- **Tax Display** - Show prices excluding or including tax
- **Round at Subtotal** - Round once at subtotal vs per-item

#### 3. Tax Classes
- Create custom tax classes
- Assign products to classes for different rates
- Default class cannot be deleted

#### 4. Tax Exemptions
- Add customers as tax-exempt
- Support for:
  - Resale certificates (wholesale buyers)
  - Nonprofit organizations
  - Government agencies
- Certificate number tracking
- Expiration date support
- Revoke exemptions when needed

#### 5. Tax Report
- Monthly summary of taxes collected by region
- Taxable sales and tax collected amounts

---

## API Endpoints

### Base URL
```
http://localhost:8300/api/v1
```

### Tax Rates

#### Get All Tax Rates
```http
GET /admin/tax/rates
GET /admin/tax/rates?country=CA
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "country_code": "US",
      "state_code": "TX",
      "state_name": "Texas",
      "city": null,
      "rate": "6.250",
      "is_compound": 0,
      "tax_shipping": 1,
      "is_active": 1
    }
  ]
}
```

#### Update Tax Rate
```http
PUT /admin/tax/rates/{id}
Content-Type: application/json

{
  "rate": 8.25,
  "is_compound": false,
  "tax_shipping": true,
  "is_active": true
}
```

### Tax Settings

#### Get Settings
```http
GET /admin/tax/settings
```

**Response:**
```json
{
  "success": true,
  "data": {
    "tax_enabled": "1",
    "tax_calculation_address": "shipping",
    "tax_display_mode": "excluding",
    "tax_round_at_subtotal": "0"
  }
}
```

#### Update Settings
```http
POST /admin/tax/settings
Content-Type: application/json

{
  "tax_enabled": true,
  "tax_calculation_address": "shipping",
  "tax_display_mode": "excluding",
  "tax_round_at_subtotal": true
}
```

### Tax Exemptions

#### Get All Exemptions
```http
GET /admin/tax/exemptions
```

#### Create Exemption
```http
POST /admin/tax/exemptions
Content-Type: application/json

{
  "user_id": 123,
  "exemption_type": "resale",
  "certificate_number": "TX-12345",
  "reason": "Wholesale buyer",
  "expires_at": "2025-12-31"
}
```

#### Revoke Exemption
```http
PUT /admin/tax/exemptions/{id}/revoke
```

---

## Frontend Integration

### ✅ Implementation Complete (November 2025)

The frontend now uses **dynamic tax calculation** based on the shipping address.

### Files

| File | Purpose |
|------|---------|
| `includes/tax-helpers.php` | Tax calculation helper functions |
| `cart/calculate-tax.php` | AJAX endpoint for real-time recalculation |
| `cart/checkout.php` | Checkout page with country/state dropdowns |

### How It Works

1. **Initial Load**: Checkout calculates tax based on default address (US/TX)
2. **Country Change**: Dropdown updates state/province options and recalculates tax
3. **State Change**: AJAX call recalculates tax for new location
4. **City Blur**: Recalculates for potential local taxes

### Tax Helper Functions

**File:** `includes/tax-helpers.php`

```php
// Main calculation function
calculateTax($country, $state, $city, $subtotal, $shipping, $userId)

// Convenience functions
getTaxAmount($country, $state, $subtotal, $shipping, $userId)
checkTaxExemption($country, $userId)
getTaxableCountries() // Returns ['US', 'CA', 'MX']
getCountryOptions()   // Returns dropdown options
getUSStates()         // Returns US state list
getCanadianProvinces() // Returns CA province list
getMexicanStates()    // Returns MX state list
```

### AJAX Endpoint

**File:** `cart/calculate-tax.php`

```http
POST /cart/calculate-tax.php
Content-Type: application/json

{
  "country": "US",
  "state": "TX",
  "city": "Austin",
  "subtotal": 100.00,
  "shipping": 0,
  "user_id": null
}
```

**Response:**
```json
{
  "success": true,
  "tax_amount": 8.25,
  "tax_rate": 8.25,
  "is_exempt": false,
  "reason": null,
  "breakdown": [...]
}
```

### Backend API Endpoint

**File:** `TaxController.php`

```http
POST /api/v1/tax/calculate
Content-Type: application/json

{
  "country": "US",
  "state": "TX",
  "city": "Austin",
  "subtotal": 100.00,
  "shipping": 0,
  "user_id": null
}
```

Handles:
- Tax enabled check
- Customer exemptions
- Export exempt for non-US/CA/MX countries
- Compound tax calculation
- City/local taxes

### Checkout Implementation

**File:** `cart/checkout.php`

```php
require_once(__DIR__ . '/../includes/tax-helpers.php');

// Default address
$defaultCountry = 'US';
$defaultState = 'TX';
$userId = $_SESSION['user_id'] ?? null;

// Calculate tax dynamically
$taxResult = calculateTax($defaultCountry, $defaultState, null, $subtotalAfterCoupon, $shipping, $userId);
$tax = $taxResult['tax_amount'];
$taxRate = $taxResult['tax_rate'];
$taxExempt = $taxResult['is_exempt'];

// Country dropdown with dynamic state updates
$countryOptions = getCountryOptions();
$usStates = getUSStates();
$canadianProvinces = getCanadianProvinces();
$mexicanStates = getMexicanStates();
```

### JavaScript Tax Recalculation

```javascript
document.getElementById('state').addEventListener('change', async function() {
    const state = this.value;
    const country = document.getElementById('country').value || 'US';

    const response = await fetch(`/cart/calculate-tax.php?state=${state}&country=${country}&subtotal=${SUBTOTAL}`);
    const result = await response.json();

    document.getElementById('tax-amount').textContent = '$' + result.tax.toFixed(2);
    document.getElementById('order-total').textContent = '$' + result.total.toFixed(2);
});
```

---

## International Orders

### Currently Supported Countries

| Country | Code | Tax Type | Rate Structure |
|---------|------|----------|----------------|
| United States | US | Sales Tax | State + Local (varies 0-10%) |
| Canada | CA | GST/HST/PST | Province-based (5-15%) |
| Mexico | MX | IVA | Federal (16%, border 8%) |

### US Orders

**How it works:**
1. Customer enters shipping address with state
2. System looks up state tax rate (e.g., Texas = 6.25%)
3. Optionally adds local/city tax if configured
4. If "Tax Shipping" is enabled for that state, shipping cost is also taxed

**No Sales Tax States:**
- Oregon (OR)
- Montana (MT)
- New Hampshire (NH)
- Delaware (DE)
- Alaska (AK) - some local taxes

### Canadian Orders

**Tax Types:**
- **GST** (Goods and Services Tax) - 5% federal, applies everywhere
- **PST** (Provincial Sales Tax) - Varies by province
- **HST** (Harmonized Sales Tax) - Combined GST+PST in some provinces

**Provincial Rates:**
| Province | Rate | Type |
|----------|------|------|
| Alberta | 5% | GST only |
| British Columbia | 12% | GST 5% + PST 7% |
| Ontario | 13% | HST |
| Quebec | 14.975% | GST 5% + QST 9.975% |
| Saskatchewan | 11% | GST 5% + PST 6% |

### Mexican Orders

**IVA (Impuesto al Valor Agregado):**
- Standard rate: 16%
- Border zone rate: 8% (northern border states)
- Some items exempt (food, medicine, books)

### Other International Orders

**Current handling:**
- Countries not in the system = **No tax calculated**
- Orders may need manual adjustment
- Consider marking as export/tax-exempt

**Recommended approach for other countries:**
1. Create exemption for international customers
2. Use exemption type "Other" with reason "Export - shipped outside US"
3. Customer is responsible for import duties/VAT in their country

---

## Tax Calculation Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     ORDER PLACED                                 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 1: Check if taxes are enabled                              │
│         GET /api/v1/admin/tax/settings                          │
│         → tax_enabled = "1"?                                    │
│                                                                  │
│         If NO → Tax = $0.00 (skip calculation)                  │
└─────────────────────────────────────────────────────────────────┘
                              │ YES
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 2: Check for customer tax exemption                        │
│         GET /api/v1/admin/tax/exemptions                        │
│         → Customer has active exemption?                        │
│                                                                  │
│         If YES → Tax = $0.00, mark as "Tax Exempt"             │
└─────────────────────────────────────────────────────────────────┘
                              │ NO
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 3: Determine address for tax calculation                   │
│         Based on tax_calculation_address setting:               │
│         - "shipping" → Use shipping address state               │
│         - "billing"  → Use billing address state                │
│         - "store"    → Use store's home state                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 4: Look up applicable tax rates                            │
│         GET /api/v1/admin/tax/rates?country=XX                  │
│         Find matching:                                           │
│         1. Country code (US, CA, MX)                            │
│         2. State/Province code                                   │
│         3. City (if local taxes exist)                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 5: Calculate tax                                            │
│                                                                  │
│ Base tax:                                                        │
│   tax = subtotal × (rate / 100)                                 │
│                                                                  │
│ If tax_shipping = true:                                          │
│   tax += shipping × (rate / 100)                                │
│                                                                  │
│ If is_compound = true (local tax):                              │
│   local_tax = (subtotal + state_tax) × (local_rate / 100)       │
│   total_tax = state_tax + local_tax                             │
│                                                                  │
│ If tax_round_at_subtotal = true:                                │
│   tax = round(tax, 2)                                           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│ Step 6: Apply to order                                           │
│                                                                  │
│ Order Total = Subtotal + Shipping + Tax                         │
│                                                                  │
│ Display based on tax_display_mode:                              │
│ - "excluding": Show subtotal + tax separately                   │
│ - "including": Show combined price with "(incl. tax)"           │
└─────────────────────────────────────────────────────────────────┘
```

---

## Tax Reporting

The admin panel includes a tax report showing taxes collected by region for the current month.

**Report displays:**
- Region (state/province)
- Taxable sales amount
- Tax collected amount
- Monthly totals

**For tax filing purposes:**
- Export data by state for US sales tax filing
- Canadian GST/HST remittance by province
- Mexican IVA reporting

---

## Future Improvements

### Not Yet Implemented

1. **Dynamic frontend tax calculation** - Currently hardcoded 8.25%
2. **EU VAT support** - Country-specific rates, reverse charge
3. **Automatic rate updates** - Integration with Avalara/TaxJar
4. **Product-specific tax classes** - Different rates per product
5. **Multi-currency display** - Show tax in local currency
6. **Cross-border duties** - Import duties/customs fees
7. **Digital goods tax** - Different rates for digital vs physical
8. **Threshold exemptions** - De minimis for small orders

### Priority Recommendations

| Priority | Improvement | Effort |
|----------|-------------|--------|
| HIGH | Connect frontend to API for dynamic tax | Medium |
| HIGH | Add state dropdown to checkout | Low |
| MEDIUM | Add EU countries to tax_rates | Medium |
| MEDIUM | Product-level tax class assignment | High |
| LOW | Automatic rate update service | High |

---

## Troubleshooting

### Common Issues

**1. Tax not being calculated**
- Check `tax_enabled` setting is "1"
- Verify the state has an active rate (`is_active = 1`)
- Check customer isn't tax-exempt

**2. Wrong rate applied**
- Verify country code matches (US not USA)
- Check state code format (TX not Texas)
- Ensure address is being read from correct source

**3. Canadian orders wrong tax**
- Verify province code (ON, BC, QC)
- Check HST vs GST+PST configuration
- Some categories may be PST-exempt

**4. No tax on international orders**
- Country must exist in tax_rates table
- Add rates for new countries as needed

---

## Related Documentation

- [International Tax Guide](./international-tax-guide.md) - Country-specific details
- [API Integration Tracker](./api-integration-tracker.md) - All API endpoints
- [FEATURES.md](./FEATURES.md) - Complete feature list

---

## Support

For tax configuration assistance:
1. Access: http://localhost:8301/admin/settings/tax
2. Check tax report for verification
3. Review API responses for debugging
4. Contact support for custom scenarios
