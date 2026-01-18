# Backend Admin Site Documentation

Last Updated: November 29, 2025

## 📖 Documentation Index

### Getting Started
- [Setup Guide](SETUP.md) - Installation and configuration
- [Features](FEATURES.md) - Complete feature documentation
- [UI Enhancements](UI_ENHANCEMENTS.md) - Recent UI improvements

### Integration
- [API Integration Tracker](api-integration-tracker.md) - API connection status
- [Tax System Guide](TAX_SYSTEM_GUIDE.md) - Complete tax system documentation
- [International Tax Guide](international-tax-guide.md) - Country-specific tax details

### Architecture
- [Backend Admin Details](backend-admin.md) - System architecture
- [Pecos Backend Admin Site](pecos-backend-admin-site.md) - Comprehensive guide

## 🎯 Quick Links

### Admin Pages
- **Dashboard**: http://localhost:8301/admin/dashboard
- **Products**: http://localhost:8301/admin/products
- **Orders**: http://localhost:8301/admin/orders
- **Inventory**: http://localhost:8301/admin/inventory
- **Customers**: http://localhost:8301/admin/customers
- **Profile**: http://localhost:8301/admin/profile
- **Settings**: http://localhost:8301/admin/settings

### Recent Updates (November 2025)

#### November 29, 2025
- 📋 **Documentation Updates** - Comprehensive documentation refresh
  - Updated all MD files across frontend, backend, and API projects
  - Added planned features for product page enhancements
  - Added customer support request system documentation
  - Marked completed features and identified next steps
- 🎫 **Support Ticket System Enhancement (Planned)**
  - Create Ticket modal API integration needed
  - Customer lookup by email in modal
  - Frontend customer support request page planned

#### November 28, 2025
- ✅ **Settings Consolidation** - Streamlined settings organization
  - Removed Site Name/Description from General tab (now in Store Info)
  - Added Store Tagline/Description to Store Info tab
  - Removed Site Title from Branding (uses Store Name from Store Info)
  - Removed Payment tab from Settings (available in Features only)
  - Store Name font size and color controls in Store Info
- ✅ **Admin Store Name Display** - Store name centered in admin top navigation
  - Dynamic styling from Store Info settings
  - Font size dropdown (1rem - 2rem)
  - Color picker for text color
- ✅ **Logo Alignment** - New option in Branding section
  - Left, Center, Right alignment options
  - Frontend header.php updated with alignment wrapper
  - CSS classes for logo positioning
- ✅ **Navigation Bar Height** - New control in Header Styling
  - Dropdown with 50px to 100px options
  - Controls overall header and logo sizing
  - Removed Logo Max Height (nav height controls sizing)
- ✅ **Color Picker Tooltips** - Added to all color inputs in Branding
  - "Click on color to see Color Wheel" tooltip on hover
  - Bootstrap tooltips initialized on page load
- ✅ **Tax Calculation Provider** - New section in Feature Configuration page
  - Custom Tax Table (admin-configured rates) - Free
  - Stripe Tax integration - 0.5% per transaction
  - TaxJar integration - $19-99/month with auto filing
  - Comparison table showing features
  - Automatic fallback to Custom if API fails
  - Provider badge shown at checkout
- ✅ **Payment Gateway Configuration** - New section in Feature Configuration page
  - Master payment processing toggle
  - Stripe integration (Cards + Apple Pay + Google Pay)
    - Publishable Key and Secret Key fields
    - Test Mode toggle
    - ACH Bank Transfers toggle (low-cost US bank payments)
  - Braintree integration (Cards + PayPal + Venmo + Wallets)
    - Merchant ID, Public Key, Private Key fields
    - Sandbox Mode toggle
  - PayPal Checkout integration
    - Client ID and Client Secret fields
    - Sandbox Mode toggle
  - Multiple gateways can be enabled simultaneously
  - Frontend checkout dynamically loads enabled gateways from admin API
- ✅ **Stock Alerts Fix** - Add Stock functionality now works correctly
  - Fixed API endpoint from `/inventory/{id}/adjust` to `/inventory/adjust-stock`
  - Modal now displays correct "available" stock (not total stock_quantity)
  - Supports out of stock and low stock items
- ✅ **Purchase Orders Enhancement**
  - Added tooltips to all action buttons (View, Edit, Delete, Receive)
  - View PO modal now displays line items properly
  - Added Edit PO function for draft orders
  - Sample line item data populated for existing POs
- ✅ **Notification Configuration** - New section in Feature Configuration page
  - Master notification toggle
  - Channel toggles (Email, SMS, Push)
  - Category toggles (Delivery, Promotional, Payment, Security)
  - Settings saved to API and loaded by frontend
  - Frontend respects admin settings
- ✅ **Live Chat Configuration** - New section in Feature Configuration page
  - Master Live Chat toggle
  - Tawk.to support with Property ID and Widget ID
  - Tidio support with Public Key
  - Auto-disable logic (only one provider active at a time)
- ✅ **Global Modal Accessibility Fix** - Fixed aria-hidden focus issues across all modals

#### November 26, 2025
- ✅ **Profile Page** - New admin profile page with account info, notification preferences, activity log
- ✅ **Functional Top Nav Notifications** - Stock alerts dropdown now pulls real data from API
- ✅ **Functional Top Nav Messages** - Messages dropdown shows contact messages (with sample data fallback)
- ✅ **Accessibility Fixes** - Added autocomplete attributes to all password fields
- ✅ **Removed Debug Logging** - Cleaned up console.log statements from production code

#### UI Enhancements
- ✅ Row highlighting feature on all tables (light blue #e3f2fd)
- ✅ Improved table interactions
- ✅ Better visual feedback for user actions
- ✅ Consistent styling across all admin pages

#### Features Added
- Interactive table rows with click-to-highlight
- Enhanced navigation with functional notification dropdowns
- Improved mobile responsiveness
- Better error handling
- Admin profile management

## 📝 Documentation Files

- `SETUP.md` - Installation and setup
- `FEATURES.md` - Feature documentation
- `UI_ENHANCEMENTS.md` - UI/UX improvements
- `api-integration-tracker.md` - API integration status
- `backend-admin.md` - Admin system details
- `international-tax-guide.md` - Tax configuration
- `pecos-backend-admin-site.md` - Complete guide

---

For the main project README, see [../README.md](../README.md).