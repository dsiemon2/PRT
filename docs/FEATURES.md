# Pecos River Traders - Feature Documentation

## Overview

This document describes all the features implemented in the Pecos River Traders (PRT) e-commerce platform.

**Last Updated:** January 15, 2026

---

## Core Features

### Products
- Full CRUD operations
- Image management (multiple images per product)
- Category assignment
- Search and filtering
- Featured products
- Specialty products
- Product reviews and ratings
- Q&A system

### Orders
- Full order lifecycle management
- Guest checkout support
- Order status tracking
- Order notes
- Order cancellation
- Order history

### Customers
- Account management
- Profile management
- Multiple addresses
- Wishlist functionality
- Customer preferences

### Payments (5 Gateway Integration)
- **Stripe** - Credit/debit card processing
- **Braintree** - PayPal and credit cards
- **PayPal** - Direct PayPal integration
- **Square** - Point of sale integration
- **Authorize.net** - Merchant services

---

## Advanced Features (Implemented January 2026)

### 1. Returns/RMA System
**Status:** Implemented

Full returns management system including:
- Return request creation and management
- RMA (Return Merchandise Authorization) tracking
- Return reason codes and categories
- Partial return support
- Return status workflow
- Restocking fee management
- Return labels generation
- Refund processing workflow

**Admin Routes:**
- `/admin/returns` - Returns management dashboard
- `/api/v1/admin/returns/*` - API endpoints

**Database Tables:**
- `returns` - Main returns table
- `return_items` - Line items for returns
- `return_reasons` - Reason codes

---

### 2. Multi-Currency Support
**Status:** Implemented

International currency support including:
- Currency master table
- Exchange rate tracking and auto-updates
- Product prices per currency
- Store currency configuration
- Currency conversion on checkout
- Multi-currency reporting

**Admin Routes:**
- `/admin/currencies` - Currency management
- `/api/v1/admin/currencies/*` - API endpoints

**Database Tables:**
- `currencies` - Currency definitions
- `exchange_rates` - Rate history
- `exchange_rate_history` - Historical tracking
- `product_prices` - Per-currency pricing

---

### 3. Multi-Language Support
**Status:** Implemented

Full internationalization support:
- Language settings configuration
- Content translation tables
- Translatable fields mapping
- Language-specific product descriptions
- Admin UI for translations
- Language switcher component

**Admin Routes:**
- `/admin/languages` - Language management
- `/api/v1/admin/languages/*` - API endpoints

**Database Tables:**
- `languages` - Supported languages
- `translation_keys` - Translation key registry
- `translations` - Translation values
- `product_translations` - Product content translations
- `category_translations` - Category translations
- `page_translations` - Static page translations

---

### 4. Email Marketing Campaigns
**Status:** Implemented

Full email marketing platform:
- Email lists/audiences management
- Email campaign CRUD
- Automated email sequences (drip campaigns)
- A/B testing support
- Campaign analytics (open rates, click rates)
- Subscriber management
- Unsubscribe handling

**Admin Routes:**
- `/admin/email-marketing` - Campaign management
- `/api/v1/admin/email-marketing/*` - API endpoints

**Database Tables:**
- `email_lists` - Mailing lists
- `email_subscribers` - Subscriber records
- `email_campaigns` - Campaign definitions
- `campaign_recipients` - Send tracking
- `campaign_links` - Click tracking
- `email_automations` - Drip campaigns
- `automation_steps` - Sequence steps
- `campaign_ab_variants` - A/B test variants

---

### 5. SMS/Push Notifications
**Status:** Implemented

Multi-channel notification system:
- SMS provider integration (Twilio, AWS SNS ready)
- Push notification system
- Notification scheduling
- Customer notification preferences
- SMS/Push templates management

**Admin Routes:**
- `/admin/notifications` - Notifications management
- `/api/v1/admin/notifications/*` - API endpoints

**Database Tables:**
- `sms_templates` - SMS message templates
- `push_templates` - Push notification templates
- `notification_channels` - Provider configuration
- `sms_messages` - SMS log
- `push_notifications` - Push log
- `customer_device_tokens` - Mobile devices
- `customer_notification_preferences` - Preferences
- `notification_campaigns` - Campaigns
- `notification_automations` - Triggered notifications

---

### 6. Advanced Search with Facets
**Status:** Implemented

Enterprise-grade search capabilities:
- Faceted search configuration
- Filter rules management
- Search autocomplete with suggestions
- Search synonyms management
- Search redirects
- Search analytics
- Product boosting/burying

**Admin Routes:**
- `/admin/search` - Search management
- `/api/v1/admin/search/*` - API endpoints

**Database Tables:**
- `search_facets` - Facet definitions
- `search_synonyms` - Synonym mappings
- `search_redirects` - Search redirects
- `search_boosts` - Product boosting
- `search_buried` - Product hiding
- `search_queries` - Query logging
- `popular_searches` - Popular terms
- `search_suggestions` - Autocomplete
- `search_filter_rules` - Dynamic filters
- `search_analytics` - Daily analytics
- `search_clicks` - Click tracking

---

### 7. Product Variants/SKU Management
**Status:** Implemented

Full variant system for products:
- Attribute types (Size, Color, Material, etc.)
- Attribute values with display options
- SKU per variant
- Variant-specific pricing
- Variant-specific images
- Stock tracking per variant
- Variant matrix editing
- Price rules (quantity, customer group, date)
- Inventory logging

**Admin Routes:**
- `/admin/variants` - Variant management
- `/api/v1/admin/variants/*` - API endpoints

**Database Tables:**
- `product_attribute_types` - Attribute definitions
- `product_attribute_values` - Value options
- `product_variants` - SKU-level variants
- `variant_attribute_values` - Variant-attribute links
- `variant_images` - Variant photos
- `product_attribute_assignments` - Product-attribute mapping
- `variant_price_rules` - Pricing rules
- `variant_inventory_logs` - Stock change history

---

### 8. Live Chat Support
**Status:** Implemented

Real-time customer support system:
- Real-time chat widget
- Chat agent management
- Department routing
- Chat history persistence
- Canned responses for quick replies
- Proactive chat triggers
- Offline message handling
- Chat analytics
- Rating and feedback

**Admin Routes:**
- `/admin/livechat` - Chat management
- `/api/v1/admin/chat/*` - API endpoints

**Database Tables:**
- `chat_agents` - Agent profiles
- `chat_sessions` - Chat conversations
- `chat_messages` - Individual messages
- `chat_canned_responses` - Quick replies
- `chat_departments` - Department config
- `chat_department_agents` - Agent assignments
- `chat_triggers` - Proactive rules
- `chat_offline_messages` - Offline messages
- `chat_widget_settings` - Widget config
- `chat_analytics` - Daily stats

---

## Existing Features

### Marketing
- Coupon management
- Loyalty program (4-tier system)
- Gift cards

### Content Management
- Blog system
- Events calendar
- FAQs
- Announcements
- Homepage banners

### Shipping
- Shipping zones
- Shipping methods
- Shipping classes
- Carrier integration

### Tax Management
- Tax rates
- Tax classes
- Tax exemptions
- Tax calculation engine

### CRM System
- Customer tags
- Customer segments
- Customer timeline
- Customer notes
- Communications log
- 360-degree customer view

### Support System
- Ticket management
- Canned responses
- Support ratings

### B2B Features
- Dropshipper management
- Wholesale accounts
- Lead management
- Deals pipeline

### Admin Features
- Dashboard with analytics
- User management
- Settings management
- API logging

### SEO
- Meta tags management
- Open Graph support
- Sitemap generation
- Structured data

---

## Running Migrations

After updating the code, run the migrations to create the new tables:

```bash
# For PRT (using Docker)
docker-compose exec api php artisan migrate

# Or directly
cd pecos-backendadmin-api
php artisan migrate
```

### Seeders Available

```bash
# Run all feature seeders
php artisan db:seed --class=ReturnsSeeder
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=EmailMarketingSeeder
php artisan db:seed --class=NotificationsSeeder
php artisan db:seed --class=SearchSeeder
php artisan db:seed --class=VariantsSeeder
php artisan db:seed --class=ChatSeeder
```

---

## API Documentation

All features are accessible via RESTful API endpoints at `/api/v1/admin/*`.

API documentation is available via Swagger at:
- `http://localhost:8300/api/documentation`

---

## Admin Panel

All features have corresponding admin panel pages accessible from the sidebar navigation.

---

*Document generated: January 15, 2026*
