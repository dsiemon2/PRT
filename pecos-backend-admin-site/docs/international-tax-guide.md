# International Sales Tax Guide

**Last Updated:** 2025-11-21

## Overview

This document explains how the Pecos & Company admin system handles sales tax for international transactions, including US domestic, Canadian (GST/HST/PST), and Mexican (IVA) sales.

---

## Current Tax Settings Location

**Admin Panel:** http://localhost:8301/admin/settings/tax

---

## How International Sales Tax Works

### Tax Calculation Flow

1. **Customer places order** with shipping/billing address
2. **System identifies country** from address (US, CA, MX)
3. **Looks up applicable tax rates** based on:
   - Country code
   - State/Province code
   - City (for local taxes, US only)
4. **Calculates tax** based on settings:
   - Shipping address (default)
   - Billing address
   - Store address (origin-based)

---

## Country-Specific Tax Handling

### United States (US)

**Tax Type:** Sales Tax (state + local)

**Structure:**
- State-level taxes (0% - 7.25%)
- County/City local taxes (additional 0% - 5%)
- Some states have no sales tax (OR, MT, NH, DE, AK)

**Key Features:**
- Compound taxes supported (local applied after state)
- Nexus-based collection requirements
- Tax on shipping varies by state

**Example:**
```
Texas Order:
- State Tax: 6.25%
- Local Tax: 2.00%
- Total: 8.25%
```

### Canada (CA)

**Tax Types:**
- **GST** (Goods and Services Tax) - 5% federal
- **PST** (Provincial Sales Tax) - varies by province
- **HST** (Harmonized Sales Tax) - combined GST+PST

**Provincial Breakdown:**
| Province | GST | PST | HST | Total |
|----------|-----|-----|-----|-------|
| Alberta | 5% | - | - | 5% |
| British Columbia | 5% | 7% | - | 12% |
| Ontario | - | - | 13% | 13% |
| Quebec | 5% | 9.975% | - | 14.975% |
| Saskatchewan | 5% | 6% | - | 11% |

**Key Features:**
- GST is federal and applies to all provinces
- PST may be exempt for certain product categories
- Quebec uses QST (similar to PST)

### Mexico (MX)

**Tax Type:** IVA (Impuesto al Valor Agregado)

**Rates:**
- Standard rate: 16%
- Border zone rate: 8% (northern border states)
- Exempt: Food, medicine, books

**Key Features:**
- Single federal tax rate (no state variations)
- Applied to most goods and services
- Some categories exempt or zero-rated

---

## What Happens with International Orders

### Scenario 1: US Customer

1. Customer enters Texas shipping address
2. System finds TX state rate (6.25%) and city rate (if applicable)
3. Tax calculated on subtotal (+ shipping if configured)
4. Order shows itemized state and local taxes

### Scenario 2: Canadian Customer

1. Customer enters Ontario shipping address
2. System finds Ontario HST rate (13%)
3. Single combined tax applied
4. Receipt shows "HST 13%"

### Scenario 3: Mexican Customer

1. Customer enters Mexico City shipping address
2. System finds Mexico IVA rate (16%)
3. Standard IVA applied
4. Receipt shows "IVA 16%"

### Scenario 4: International (Other Countries)

Currently, the system supports US, CA, and MX only.

**For other countries:**
- No tax is automatically calculated
- Orders may need manual tax adjustment
- Consider using tax exemption for international wholesale

---

## Tax Exemptions for International

### Resale Exemptions

International wholesale buyers can be marked tax-exempt:

1. Go to **Tax Settings > Tax Exemptions**
2. Click **Add Exemption**
3. Select customer
4. Choose type: "Resale Certificate"
5. Enter certificate number
6. Set expiration (if applicable)

### Export Exemptions

Sales exported outside the US may be zero-rated:

1. Mark customer as tax-exempt
2. Use exemption type: "Other"
3. Add reason: "Export sale - shipped outside US"

---

## API Endpoints for Tax

### Get Tax Rates
```
GET /api/v1/admin/tax/rates
GET /api/v1/admin/tax/rates?country=CA
```

### Update Tax Rate
```
PUT /api/v1/admin/tax/rates/{id}
{
  "rate": 8.25,
  "is_compound": false,
  "tax_shipping": true,
  "is_active": true
}
```

### Get Tax Settings
```
GET /api/v1/admin/tax/settings
```

### Update Tax Settings
```
POST /api/v1/admin/tax/settings
{
  "tax_enabled": true,
  "tax_calculation_address": "shipping",
  "tax_display_mode": "excluding",
  "tax_round_at_subtotal": true
}
```

---

## Configuration Options

### Calculate Tax Based On

| Option | Description | Use Case |
|--------|-------------|----------|
| **Shipping Address** | Tax based on where goods are delivered | Default, most common |
| **Billing Address** | Tax based on customer's billing location | Digital goods |
| **Store Address** | Origin-based taxation | Some US states require this |

### Tax Display Mode

| Option | Description | Example |
|--------|-------------|---------|
| **Excluding Tax** | Prices shown without tax | $100.00 + $8.25 tax |
| **Including Tax** | Prices include tax | $108.25 (incl. tax) |

### Compound Taxes

When enabled, taxes are applied in sequence:

```
Subtotal: $100.00
State Tax (6.25%): $6.25
Subtotal after state: $106.25
Local Tax (2% compound): $2.13
Total Tax: $8.38
```

---

## Database Structure

### tax_rates Table
```sql
- id
- country_code (US, CA, MX)
- state_code
- state_name
- city (null for state-level)
- rate (decimal)
- is_compound
- tax_shipping
- is_active
```

### tax_exemptions Table
```sql
- id
- user_id
- exemption_type (resale, nonprofit, government, other)
- certificate_number
- reason
- expires_at
- status (active, expired, revoked)
```

---

## Future Considerations

### Not Yet Implemented

1. **VAT for EU Countries** - Requires country-specific rates and reverse charge mechanism
2. **Automatic Tax Rate Updates** - Integration with tax rate services (Avalara, TaxJar)
3. **Multi-currency Tax Display** - Show tax in local currency
4. **Digital Goods Tax** - Different rates for digital vs physical goods
5. **Cross-border Duties/Customs** - Import duties and customs fees

### Recommended Improvements

1. **Add more countries** - Expand tax_rates for EU, UK, AU
2. **Tax service integration** - Automatic rate lookups via API
3. **Product-specific rates** - Different tax classes per product
4. **Threshold-based exemptions** - De minimis for small orders

---

## Troubleshooting

### Common Issues

**1. No tax calculated on order**
- Check if tax is enabled in settings
- Verify country/state has active tax rate
- Check if customer has tax exemption

**2. Wrong tax rate applied**
- Verify address is correctly parsed
- Check "Calculate Tax Based On" setting
- Review compound tax configuration

**3. Canadian orders showing wrong tax**
- Ensure province code matches (ON, BC, QC, etc.)
- Verify HST vs GST+PST setup
- Check if product category is PST-exempt

**4. Mexican orders not taxed**
- Add MX country rates to tax_rates table
- Set IVA rate to 16% for standard rate
- Create border zone rates at 8% if needed

---

## Related Documentation

- [API Integration Tracker](./api-integration-tracker.md) - Tax API endpoints
- [Backend Admin Guide](../../PRT2/docs/backend-admin.md) - Full admin features

---

## Support

For tax configuration assistance or to report issues:
- Check the tax settings at http://localhost:8301/admin/settings/tax
- Review the tax report for collected taxes this month
- Contact support for custom tax scenarios
