# File Organization Documentation

**Date**: November 18, 2025
**Version**: 1.0
**Status**: Completed

---

## Overview

This document describes the reorganization of files in the PRT2 directory to maintain a clean root directory structure with only essential public files.

---

## Root Directory Structure

### Files That Should Remain in Root

The root directory (`/PRT2/`) should **ONLY** contain these files:

1. **index.php** - Home page (main entry point)
2. **404.php** - Custom 404 error page
3. **500.php** - Custom 500 error page
4. **robots.txt** - SEO crawl instructions
5. **organize_files.bat** - File organization script (for maintenance)

All other files have been moved to appropriate subdirectories.

---

## File Reorganization Summary

### Files Moved to `maintenance/` Folder

**Test/Debug Files** (8 files):
- test_add_to_cart.php
- test_button_syntax.php
- test_direct_cart.php
- test_lookup.php
- debug_products.php
- check_wishlist_table.php
- check_products_id.php
- check_duplicate_upcs.php

**Setup/Installation Files** (6 files):
- setup_blog.php
- setup_faq.php
- setup_coupons.php
- setup_loyalty.php
- setup_product_images.php
- fix_inventory.php

**Check/Verification Files** (3 files):
- check_image_columns.php
- check_size_tables.php
- check_sizes_structure.php

**Total: 17 files** moved to `maintenance/`

---

### Files Moved to `pages/` Folder

**Public-Facing Pages** (3 files):
- faq.php
- gift-cards.php
- gift-card-balance.php

**Total: 3 files** moved to `pages/`

---

### Files Moved to `includes/` Folder

**Handler Files** (1 file):
- faq-handler.php

**Total: 1 file** moved to `includes/`

---

### Files Moved to `public/` Folder (NEW)

**XML/Feed Files** (5 files):
- sitemap.xml.php
- sitemap-pages.xml.php
- sitemap-products.xml.php
- sitemap-blog.xml.php
- google-shopping-feed.xml.php

**Total: 5 files** moved to `public/`

---

## Updated File References

### 1. robots.txt

**Updated Sitemap URLs:**
```txt
# OLD
Sitemap: https://localhost/PRT2/sitemap.xml
Sitemap: https://localhost/PRT2/sitemap-products.xml
Sitemap: https://localhost/PRT2/sitemap-blog.xml

# NEW
Sitemap: https://localhost/PRT2/public/sitemap.xml.php
Sitemap: https://localhost/PRT2/public/sitemap-products.xml.php
Sitemap: https://localhost/PRT2/public/sitemap-blog.xml.php
Sitemap: https://localhost/PRT2/public/sitemap-pages.xml.php
```

**Updated Page Paths:**
```txt
# OLD
Allow: /faq.php

# NEW
Allow: /pages/faq.php
Allow: /pages/gift-cards.php
Allow: /pages/gift-card-balance.php
```

---

### 2. pages/faq.php

**Updated Handler URL:**
```javascript
// OLD
fetch('faq-handler.php', {

// NEW
fetch('../includes/faq-handler.php', {
```

**Instances Updated:** 2 (lines 363 and 378)

---

## Directory Structure

```
/PRT2/
├── index.php ✅
├── 404.php ✅
├── 500.php ✅
├── robots.txt ✅
├── organize_files.bat
│
├── /admin/
├── /assets/
├── /auth/
├── /blog/
├── /cart/
├── /config/
│
├── /docs/
│   ├── TODO.md
│   ├── CODING_STANDARDS.md
│   ├── CART_AND_SIZE_IMPROVEMENTS.md
│   └── FILE_ORGANIZATION.md ← THIS FILE
│
├── /includes/
│   ├── layout.php
│   ├── size-functions.php
│   ├── faq-handler.php ← MOVED HERE
│   └── ... (other includes)
│
├── /maintenance/ 📁 NEW LOCATION
│   ├── test_add_to_cart.php
│   ├── test_button_syntax.php
│   ├── test_direct_cart.php
│   ├── test_lookup.php
│   ├── debug_products.php
│   ├── check_wishlist_table.php
│   ├── check_products_id.php
│   ├── check_duplicate_upcs.php
│   ├── check_image_columns.php
│   ├── check_size_tables.php
│   ├── check_sizes_structure.php
│   ├── setup_blog.php
│   ├── setup_faq.php
│   ├── setup_coupons.php
│   ├── setup_loyalty.php
│   ├── setup_product_images.php
│   ├── fix_inventory.php
│   ├── .htaccess (blocks web access)
│   └── README.md
│
├── /pages/
│   ├── about-us.php
│   ├── contact-us.php
│   ├── events.php ← NEW (public events calendar)
│   ├── faq.php ← MOVED HERE
│   ├── gift-cards.php ← MOVED HERE
│   ├── gift-card-balance.php ← MOVED HERE
│   ├── tell-a-friend.php
│   └── ... (other public pages)
│
├── /Products/
│   ├── products.php
│   └── product-detail.php
│
└── /public/ 📁 NEW FOLDER
    ├── sitemap.xml.php ← MOVED HERE
    ├── sitemap-pages.xml.php ← MOVED HERE
    ├── sitemap-products.xml.php ← MOVED HERE
    ├── sitemap-blog.xml.php ← MOVED HERE
    └── google-shopping-feed.xml.php ← MOVED HERE
```

---

## Benefits of This Organization

### 1. **Clean Root Directory**
- Only essential public files in root
- Easier to understand project structure
- Professional appearance

### 2. **Security**
- Test/debug files protected in `maintenance/` with .htaccess
- Setup scripts not publicly accessible
- Reduced attack surface

### 3. **Maintainability**
- Related files grouped together
- Clear separation of concerns
- Easier to find files

### 4. **SEO**
- Clean URL structure
- Proper sitemap organization
- Better crawl efficiency

---

## How to Use Maintenance Scripts

Maintenance scripts in `/maintenance/` folder are protected from web access but can be run via PHP CLI:

```bash
# Run from command line
php C:\xampp\htdocs\PRT2\maintenance\setup_blog.php

# Or using full path
C:\xampp\php\php.exe C:\xampp\htdocs\PRT2\maintenance\check_sizes_structure.php
```

**WARNING**: Never run destructive scripts (fix_*, setup_*) on production without backups!

---

## Migration Checklist

- [x] Move test/debug files to `maintenance/`
- [x] Move setup files to `maintenance/`
- [x] Move check files to `maintenance/`
- [x] Move public pages to `pages/`
- [x] Move handler files to `includes/`
- [x] Create `public/` folder for XML/feeds
- [x] Move sitemap files to `public/`
- [x] Update robots.txt with new paths
- [x] Update faq.php handler references
- [x] Verify only essential files remain in root
- [x] Create this documentation
- [x] Test that moved pages still work

---

## Testing Verification

After reorganization, verify:

1. **Root Directory**: Only index.php, 404.php, 500.php, robots.txt remain
2. **FAQ Page**: `http://localhost/PRT2/pages/faq.php` works and AJAX calls succeed
3. **Gift Cards**: `http://localhost/PRT2/pages/gift-cards.php` loads correctly
4. **Sitemaps**: `http://localhost/PRT2/public/sitemap.xml.php` generates properly
5. **robots.txt**: Search engines can access sitemap URLs

---

## Future Considerations

### Additional Files That Could Be Organized

If additional files accumulate in root, consider:

- **Login/Auth pages** → Move to `/auth/` folder
- **Special landing pages** → Move to `/pages/` folder
- **API endpoints** → Create `/api/` folder
- **Temporary files** → Create `/temp/` folder (add to .gitignore)

### Best Practices

1. **Never put files directly in root** - Always use appropriate subdirectories
2. **Update this document** - When moving files, document the changes here
3. **Check references** - Search for hardcoded paths when moving files
4. **Test after moves** - Always verify functionality after reorganization
5. **Use relative paths** - Prefer `../includes/file.php` over absolute paths

---

## Troubleshooting

### Common Issues After File Organization

**Problem**: Page shows 404 error
**Solution**: Update the path in navigation/links to new location

**Problem**: AJAX requests fail (404)
**Solution**: Update fetch URLs with new relative path

**Problem**: Maintenance script won't run from browser
**Solution**: This is intentional (.htaccess blocks access). Use PHP CLI instead.

**Problem**: Sitemap not found by Google
**Solution**: Update robots.txt and resubmit to Google Search Console

---

## Related Documentation

- [TODO.md](TODO.md) - Project tasks and roadmap
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Code organization standards
- [CART_AND_SIZE_IMPROVEMENTS.md](CART_AND_SIZE_IMPROVEMENTS.md) - Cart system documentation
- [maintenance/README.md](../maintenance/README.md) - Maintenance scripts documentation

---

## Changelog

### November 18, 2025 - Initial Organization
- Moved 17 files to `maintenance/` folder
- Moved 3 files to `pages/` folder
- Moved 1 file to `includes/` folder
- Created `public/` folder and moved 5 XML/feed files
- Updated robots.txt with new file paths
- Updated faq.php handler references
- Created comprehensive documentation

---

## Summary Statistics

- **Files Moved**: 26 total
- **New Folders Created**: 1 (`public/`)
- **Files Remaining in Root**: 4 (down from 28)
- **Reduction**: 86% fewer files in root directory
- **Security Improvement**: All test/debug/setup files now protected

---

*Last Updated: November 18, 2025*
