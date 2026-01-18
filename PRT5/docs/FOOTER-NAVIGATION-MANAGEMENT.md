# Footer Navigation Management - Planning Document

## Overview

This document outlines a comprehensive approach to managing footer navigation for e-commerce sites. The goal is to create a flexible, configurable system that can be reused across multiple sites while maintaining consistency.

---

## Current Footer Structure

```
┌─────────────────────────────────────────────────────────────────────┐
│                           FOOTER                                     │
├─────────────────┬─────────────────┬─────────────────┬───────────────┤
│      SHOP       │    RESOURCES    │ CUSTOMER SERVICE│    CONNECT    │
├─────────────────┼─────────────────┼─────────────────┼───────────────┤
│ Home            │ Sizing Guide    │ Contact Us      │ Social Links  │
│ All Products    │ Pecos Bill      │ Tell-A-Friend   │ Newsletter    │
│ Special Products│ About Pecos     │ Shipping Policy │               │
│ Product List    │ About Us        │ Return Policy   │               │
│ Shopping Cart   │                 │ Privacy Policy  │               │
└─────────────────┴─────────────────┴─────────────────┴───────────────┘
```

---

## Problems with Current Approach

1. **Hardcoded Links** - Footer links are hardcoded in PHP, not configurable
2. **No Toggle Control** - Can't enable/disable individual links
3. **Static Policy Pages** - Policy content requires code changes to update
4. **Site-Specific Content** - "Pecos Bill Legend" is site-specific, not reusable
5. **No Categorization** - No distinction between standard vs optional links
6. **Tell-A-Friend Placement** - Questionable placement in Customer Service

---

## Proposed Solution: Footer Management System

### Link Categories

#### 1. CORE LINKS (Always Present)
These are fundamental e-commerce links that should exist on every site:

| Link | Section | Notes |
|------|---------|-------|
| Home | Shop | Always needed |
| All Products | Shop | Main product catalog |
| Shopping Cart | Shop | Essential for e-commerce |
| About Us | Resources | Standard page |
| Contact Us | Customer Service | Required for business |

#### 2. TOGGLEABLE LINKS (Feature Flags)
Links that can be enabled/disabled in Admin > Features:

| Link | Feature Flag | Default | Section |
|------|--------------|---------|---------|
| Special Products | `specialty_products_enabled` | ON | Shop |
| Gift Cards | `gift_cards_enabled` | ON | Shop |
| Blog | `blog_enabled` | ON | Resources |
| Events | `events_enabled` | ON | Resources |
| FAQ | `faq_enabled` | ON | Customer Service |
| Tell-A-Friend | `tell_a_friend_enabled` | ON | Customer Service |
| Wishlists | `wishlists_enabled` | ON | Account |
| Loyalty Rewards | `loyalty_enabled` | ON | Account |

#### 3. EDITABLE POLICY PAGES
Pages with content managed via TinyMCE in admin:

| Page | Admin Location | Notes |
|------|---------------|-------|
| Shipping Policy | Content > Pages | Editable content |
| Return Policy | Content > Pages | Editable content |
| Privacy Policy | Content > Pages | Editable content |
| Terms of Service | Content > Pages | Editable content |
| Sizing Guide | Content > Pages | Could be editable |

#### 4. CUSTOM/SITE-SPECIFIC LINKS
Links unique to a specific site (should be manageable):

| Link | Type | Notes |
|------|------|-------|
| Pecos Bill Legend | Custom Page | Site-specific content |
| About Pecos River | Custom Page | Site-specific content |
| Brand Story | Custom Page | Site-specific |

---

## Recommended Admin Structure

### Option A: Dedicated Footer Management Page

**Location:** Admin > Appearance > Footer Navigation

```
┌─────────────────────────────────────────────────────────────────────┐
│                    Footer Navigation Management                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  COLUMN 1: Shop                              [Edit Title]           │
│  ─────────────────────────────────────────────────────────────────  │
│  ☑ Home                                      [Core - Always On]     │
│  ☑ All Products                              [Core - Always On]     │
│  ☑ Special Products                          [Toggle]               │
│  ☑ Gift Cards                                [Toggle]               │
│  ☑ Shopping Cart                             [Core - Always On]     │
│  [+ Add Custom Link]                                                │
│                                                                      │
│  COLUMN 2: Resources                         [Edit Title]           │
│  ─────────────────────────────────────────────────────────────────  │
│  ☑ About Us                                  [Core - Always On]     │
│  ☑ Blog                                      [Toggle]               │
│  ☑ Events                                    [Toggle]               │
│  ☑ Sizing Guide                              [Page]                 │
│  ☐ Pecos Bill Legend                         [Custom]               │
│  [+ Add Custom Link]                                                │
│                                                                      │
│  COLUMN 3: Customer Service                  [Edit Title]           │
│  ─────────────────────────────────────────────────────────────────  │
│  ☑ Contact Us                                [Core - Always On]     │
│  ☑ FAQ                                       [Toggle]               │
│  ☐ Tell-A-Friend                             [Toggle]               │
│  ☑ Shipping Policy                           [Page]                 │
│  ☑ Return Policy                             [Page]                 │
│  ☑ Privacy Policy                            [Page]                 │
│  [+ Add Custom Link]                                                │
│                                                                      │
│  COLUMN 4: Connect                           [Edit Title]           │
│  ─────────────────────────────────────────────────────────────────  │
│  Social media links (managed in Settings > Social Media)            │
│  Newsletter signup (managed in Settings > Marketing)                │
│                                                                      │
│                                              [Save Changes]         │
└─────────────────────────────────────────────────────────────────────┘
```

### Option B: Integrate with Feature Flags + Pages System

**Simpler approach - extend existing systems:**

1. **Feature Flags** (Admin > Features) - Toggle links on/off
2. **Pages System** (Admin > Content > Pages) - Manage editable pages
3. **Footer Settings** (Admin > Settings > Footer) - Column titles, layout

---

## Tell-A-Friend Placement Options

### Current: Customer Service Section
```
Customer Service
├── Contact Us
├── Tell-A-Friend  <-- Current location
├── Shipping Policy
└── ...
```
**Pros:** Groups communication-related items
**Cons:** Not really "customer service"

### Option 1: Move to Account Section
```
Account
├── My Account
├── Order History
├── Wishlists
├── Tell-A-Friend  <-- Moved here
└── Loyalty Rewards
```
**Pros:** More logical - sharing is a user action
**Cons:** Only visible when logged in?

### Option 2: Keep in Customer Service but Rename Section
```
Connect With Us (renamed from Customer Service)
├── Contact Us
├── Tell-A-Friend
├── Newsletter
└── Social Links
```
**Pros:** Better grouping
**Cons:** Requires restructuring

### Option 3: Dedicated "Share" Section (if multiple share features)
```
Share & Earn
├── Tell-A-Friend
├── Refer & Earn (future)
├── Affiliate Program (future)
```
**Pros:** Scalable for referral programs
**Cons:** May be overkill for single feature

### Recommendation
**Option 1 (Account Section)** or keep in Customer Service but make it **toggleable**.

Add to Feature Flags:
```
□ Tell-A-Friend
  Show the Tell-A-Friend page and footer link
```

---

## Editable Policy Pages System

### Proposed Admin Location: Content > Pages

```
┌─────────────────────────────────────────────────────────────────────┐
│  Content > Pages                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  POLICY PAGES                                                        │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │ Page              │ Status    │ Last Updated │ Actions      │    │
│  ├───────────────────┼───────────┼──────────────┼──────────────┤    │
│  │ Shipping Policy   │ Published │ Nov 29, 2025 │ [Edit]       │    │
│  │ Return Policy     │ Published │ Nov 28, 2025 │ [Edit]       │    │
│  │ Privacy Policy    │ Published │ Nov 25, 2025 │ [Edit]       │    │
│  │ Terms of Service  │ Draft     │ Nov 20, 2025 │ [Edit]       │    │
│  │ Sizing Guide      │ Published │ Nov 15, 2025 │ [Edit]       │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│  CUSTOM PAGES                                                        │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │ Page              │ Status    │ Last Updated │ Actions      │    │
│  ├───────────────────┼───────────┼──────────────┼──────────────┤    │
│  │ About Pecos River │ Published │ Nov 10, 2025 │ [Edit]       │    │
│  │ Pecos Bill Legend │ Published │ Nov 10, 2025 │ [Edit]       │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│  [+ Add New Page]                                                    │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Page Editor Interface

```
┌─────────────────────────────────────────────────────────────────────┐
│  Edit Page: Shipping Policy                                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  Page Title: [Shipping Policy                    ]                  │
│                                                                      │
│  URL Slug:   [shipping-policy                    ] (auto-generated) │
│                                                                      │
│  Page Type:  ○ Policy Page  ○ Info Page  ○ Custom Page              │
│                                                                      │
│  Show in Footer: ☑ Yes                                              │
│  Footer Section: [Customer Service ▼]                               │
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │                     TinyMCE Editor                           │    │
│  │  [B] [I] [U] | [H1] [H2] | [•] [1.] | [Link] | [Table]      │    │
│  ├─────────────────────────────────────────────────────────────┤    │
│  │                                                              │    │
│  │  <h2>Shipping Information</h2>                               │    │
│  │                                                              │    │
│  │  <p>We ship to all 50 US states...</p>                      │    │
│  │                                                              │    │
│  │  <h3>Shipping Methods</h3>                                   │    │
│  │  <ul>                                                        │    │
│  │    <li>Standard Shipping (5-7 days): $5.99</li>             │    │
│  │    <li>Express Shipping (2-3 days): $12.99</li>             │    │
│  │  </ul>                                                       │    │
│  │                                                              │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                      │
│  Status: ○ Draft  ● Published                                       │
│                                                                      │
│  [Preview]                              [Save Draft] [Publish]      │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### WYSIWYG Preview Concept

The TinyMCE editor should show content styled similarly to how it appears on the frontend:

```css
/* Apply site styles to TinyMCE editor */
.tox-edit-area__iframe {
    /* Inject site CSS for preview */
}
```

This ensures "what you see is what you get" when editing policy pages.

---

## Database Schema

### New Table: `pages`

```sql
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT,
    page_type ENUM('policy', 'info', 'custom') DEFAULT 'custom',
    show_in_footer BOOLEAN DEFAULT TRUE,
    footer_section VARCHAR(50), -- 'shop', 'resources', 'customer_service'
    sort_order INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### New Table: `footer_links` (for custom ordering)

```sql
CREATE TABLE footer_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section VARCHAR(50) NOT NULL, -- 'shop', 'resources', 'customer_service', 'connect'
    link_type ENUM('core', 'feature', 'page', 'custom') NOT NULL,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255),
    page_id INT NULL, -- FK to pages table
    feature_flag VARCHAR(100) NULL, -- FK to feature flag key
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Extend: `settings` table

```sql
-- Footer section titles
INSERT INTO settings (setting_group, setting_key, setting_value) VALUES
('footer', 'column1_title', 'Shop'),
('footer', 'column2_title', 'Resources'),
('footer', 'column3_title', 'Customer Service'),
('footer', 'column4_title', 'Connect'),
('footer', 'columns_count', '4');
```

---

## Feature Flags to Add

Add to Admin > Features:

```
FOOTER LINKS
─────────────────────────────────────────────

☑ Tell-A-Friend
  Show the Tell-A-Friend page and link in footer

☑ Sizing Guide
  Show the Sizing Guide page and link in footer

☑ Terms of Service
  Show Terms of Service link in footer
```

---

## Implementation Phases

### Phase 1: Tell-A-Friend Toggle (Quick Win)
- [ ] Add `tell_a_friend_enabled` feature flag
- [ ] Update footer to check feature flag
- [ ] Add toggle to Admin > Features

### Phase 2: Editable Policy Pages
- [ ] Create `pages` table
- [ ] Create Pages management in admin
- [ ] Add TinyMCE editor for page content
- [ ] Create dynamic page renderer in frontend
- [ ] Migrate existing policy content to database

### Phase 3: Footer Link Management
- [ ] Create `footer_links` table
- [ ] Build footer configuration UI
- [ ] Update footer to read from database
- [ ] Add drag-drop reordering

### Phase 4: Advanced Features
- [ ] Custom link support
- [ ] Multi-column layout options
- [ ] Footer appearance settings
- [ ] Template system for new sites

---

## Frontend Rendering Logic

```php
// includes/footer.php

function getFooterLinks($section) {
    // 1. Get core links for section
    $coreLinks = getCoreFooterLinks($section);

    // 2. Get feature-flagged links (check if enabled)
    $featureLinks = getFeatureFooterLinks($section);

    // 3. Get page-based links (from pages table)
    $pageLinks = getPageFooterLinks($section);

    // 4. Get custom links
    $customLinks = getCustomFooterLinks($section);

    // 5. Merge and sort
    return sortFooterLinks(array_merge(
        $coreLinks,
        $featureLinks,
        $pageLinks,
        $customLinks
    ));
}

// Example usage
$shopLinks = getFooterLinks('shop');
$resourceLinks = getFooterLinks('resources');
$serviceLinks = getFooterLinks('customer_service');
```

---

## Reusability for New Sites

When creating a new e-commerce site:

1. **Core links** are automatically present
2. **Feature flags** default to ON, can be toggled per site
3. **Policy pages** start with templates, can be customized
4. **Custom pages** can be added for site-specific content
5. **Footer sections** can be renamed per brand

### Site Setup Checklist

```
□ Configure feature flags for enabled features
□ Update policy page content
□ Add/remove custom pages
□ Rename footer section titles if needed
□ Configure social media links
□ Review and adjust link ordering
```

---

## Open Questions

1. **Page Templates**: Should we provide starter templates for policy pages?

2. **Version History**: Should policy pages have revision history for compliance?

3. **Multi-language**: Future consideration for internationalization?

4. **SEO**: Should each page have separate meta title/description fields?

5. **Page Builder**: Should we eventually support a visual page builder beyond TinyMCE?

---

## Summary

| Item | Solution |
|------|----------|
| Tell-A-Friend Toggle | Feature flag in Admin > Features |
| Tell-A-Friend Placement | Keep in Customer Service OR move to Account |
| Policy Pages | New "Content > Pages" section with TinyMCE |
| Custom Pages | Same Pages system, type = "custom" |
| Footer Link Control | Combination of Feature Flags + Pages + Footer Settings |
| Site-Specific Content | Custom pages with toggle visibility |
| Reusability | Database-driven with sensible defaults |

---

*Document created: November 29, 2025*
*Status: Planning/Review*
