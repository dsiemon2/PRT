# Geolocation System Documentation

**Last Updated:** November 25, 2025
**Status:** ✅ Fully Implemented

---

## Table of Contents

1. [Overview](#overview)
2. [What Is Geolocation?](#what-is-geolocation)
3. [Use Cases](#use-cases)
4. [How It Works](#how-it-works)
5. [User Experience](#user-experience)
6. [Technical Implementation](#technical-implementation)
7. [Files Created](#files-created)
8. [Database Tables](#database-tables)
9. [API Endpoints](#api-endpoints)
10. [Configuration Options](#configuration-options)
11. [Privacy & Security](#privacy--security)
12. [Testing](#testing)
13. [Analytics](#analytics)
14. [Troubleshooting](#troubleshooting)

---

## Overview

The Geolocation System for Pecos River Traders enables location-aware features that enhance the shopping experience by providing:

- **Accurate shipping cost estimates** based on customer location
- **Automatic address pre-filling** at checkout
- **Local tax calculations** for proper sales tax rates
- **Delivery availability** checks for the customer's area
- **Location-based analytics** to understand customer demographics

The system uses the browser's Geolocation API with IP-based fallback for maximum compatibility.

---

## What Is Geolocation?

Geolocation is the process of determining a user's physical location using their device. When you visit a website, you may see a prompt like:

```
📍 Know your location

Allow while visiting the site
Allow this time
Never allow
```

This prompt appears because the website is requesting access to your location through the browser's **Geolocation API**.

### How Browsers Determine Location:

1. **GPS** - Mobile devices with GPS hardware
2. **WiFi Positioning** - Based on nearby WiFi networks
3. **Cell Tower Triangulation** - Mobile networks
4. **IP Address** - Approximate location based on internet connection

---

## Use Cases

### For E-Commerce Sites:

| Feature | Description | Implementation |
|---------|-------------|----------------|
| **Shipping Estimates** | Calculate accurate shipping costs based on distance | `estimateShipping()` |
| **Tax Calculation** | Apply correct sales tax rates by state/region | `getSalesTaxByLocation()` |
| **Address Pre-fill** | Auto-complete city, state, zip at checkout | `suggestCheckoutAddress()` |
| **Delivery Availability** | Check if shipping is available in customer's area | `isDeliveryAvailable()` |
| **Store Locator** | Find nearest physical location | `calculateDistance()` |
| **Regional Pricing** | Show prices in local currency | Custom implementation |
| **Fraud Prevention** | Verify location matches billing address | Analytics comparison |
| **Analytics** | Understand customer geographic distribution | `location_analytics` table |

---

## How It Works

### 1. First Visit Flow

```
User visits site
    ↓
After 2 seconds delay
    ↓
Custom modal appears
    ↓
User chooses:
    - Allow Location (stored for session)
    - Allow This Time (one-time only)
    - Don't Allow (use IP fallback)
    ↓
If GPS allowed:
    - Browser requests permission
    - Get latitude/longitude
    - Save to session
    - Send to server
    - Reverse geocode to address
    ↓
If GPS denied:
    - Fall back to IP geolocation
    - Get approximate location
    - Less accurate but still useful
```

### 2. Subsequent Visits

- **Location cached** in session storage (1 hour)
- **No re-prompting** during same session
- **Automatic reuse** of stored coordinates
- **Privacy-first** - cleared on session end

### 3. Data Flow

```
Frontend (JavaScript)
    ↓ (sends coordinates)
Backend API (save-location.php)
    ↓ (stores in session & database)
Database (user_locations table)
    ↓ (retrieves for features)
Checkout/Cart/etc (uses location data)
```

---

## User Experience

### Custom Location Modal

When implemented, users see a professional modal instead of the browser's basic prompt:

**Modal Features:**
- Clear explanation of benefits
- Three action options (Allow / This Time / Never)
- Privacy assurance message
- Professional design matching your brand
- Mobile-responsive

**Benefits Listed:**
- ✅ Accurate shipping estimates
- ✅ Local tax calculations
- ✅ Faster checkout with pre-filled address
- ✅ Delivery availability in your area

### Permissions:

| User Choice | Behavior | Storage |
|-------------|----------|---------|
| **Allow** | Stores location for session | Session storage |
| **This Time** | Uses location once, doesn't save | Memory only |
| **Don't Allow** | Falls back to IP, marks preference | LocalStorage flag |

---

## Technical Implementation

### Frontend (JavaScript)

**File:** `assets/js/geolocation.js`

**Key Class:** `GeolocationManager`

```javascript
// Initialize automatically on page load
window.geoManager = new GeolocationManager({
    autoPrompt: true,        // Show prompt on first visit
    customModal: true,       // Use custom UI instead of browser prompt
    ipFallback: true,        // Fall back to IP if GPS denied
    onSuccess: (location) => {
        // Handle successful location retrieval
    },
    onError: (error) => {
        // Handle errors
    },
    onDenied: () => {
        // User denied permission
    }
});
```

**Key Methods:**

```javascript
// Manually request location
geoManager.getLocation()
    .then(location => console.log(location))
    .catch(error => console.error(error));

// Get shipping estimate
geoManager.getShippingEstimate(weight)
    .then(estimate => {
        console.log(`Standard: $${estimate.standard.cost}`);
        console.log(`Express: $${estimate.express.cost}`);
    });

// Clear stored location
geoManager.clearLocation();
```

### Backend (PHP)

**File:** `includes/geolocation-functions.php`

**Key Functions:**

```php
// Store user's location in session
storeUserLocation($latitude, $longitude, 'gps');

// Get stored location
$location = getUserLocation();

// Calculate distance between two points
$distance = calculateDistance($lat1, $lon1, $lat2, $lon2, 'miles');

// Estimate shipping cost
$estimate = estimateShipping($userLat, $userLon, $weight);

// Check delivery availability
$available = isDeliveryAvailable($userLat, $userLon);

// Get location from IP (fallback)
$location = getLocationFromIP($ipAddress);

// Reverse geocode coordinates to address
$address = reverseGeocode($latitude, $longitude);

// Suggest address for checkout
$suggested = suggestCheckoutAddress();

// Get sales tax by location
$taxRate = getSalesTaxByLocation($latitude, $longitude);
```

---

## Files Created

### JavaScript Files

| File | Purpose | Size |
|------|---------|------|
| `assets/js/geolocation.js` | Frontend geolocation handler class | ~600 lines |

### PHP Backend Files

| File | Purpose | Size |
|------|---------|------|
| `includes/geolocation-functions.php` | Core location functions | ~400 lines |
| `api/save-location.php` | Save user location endpoint | ~60 lines |
| `api/get-location-from-ip.php` | IP-based location fallback | ~60 lines |
| `api/estimate-shipping.php` | Calculate shipping costs | ~40 lines |
| `api/suggest-address.php` | Pre-fill checkout address | ~30 lines |
| `api/log-location-action.php` | Analytics logging | ~50 lines |

### Modified Files

| File | Change | Line |
|------|--------|------|
| `includes/footer.php` | Added geolocation script include | 89 |

---

## Database Tables

### 1. user_locations

Stores location history for logged-in users.

```sql
CREATE TABLE user_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    source ENUM('gps', 'ip', 'manual') DEFAULT 'gps',
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);
```

**Columns:**
- `id` - Primary key
- `user_id` - Reference to users table
- `latitude` - GPS latitude (-90 to 90)
- `longitude` - GPS longitude (-180 to 180)
- `source` - How location was obtained (gps, ip, manual)
- `city` - Reverse geocoded city name
- `state` - State/province
- `country` - Country name
- `postal_code` - ZIP/postal code
- `ip_address` - User's IP at time of location
- `created_at` - Timestamp

**Indexes:**
- `user_id` - Fast user lookup
- `created_at` - Time-based queries

### 2. location_analytics

Tracks user responses to location prompts.

```sql
CREATE TABLE location_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    action ENUM('prompt_shown', 'allowed', 'denied', 'timeout', 'error') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);
```

**Tracked Actions:**
- `prompt_shown` - Modal displayed to user
- `allowed` - User granted permission
- `denied` - User denied permission
- `timeout` - Request timed out
- `error` - Error occurred

**Analytics Queries:**

```php
// Acceptance rate
$acceptanceRate = (allowed / (allowed + denied)) * 100;

// Common errors
SELECT action, COUNT(*) as count
FROM location_analytics
WHERE action = 'error'
GROUP BY user_agent;

// Geographic distribution
SELECT city, state, COUNT(*) as customers
FROM user_locations
GROUP BY city, state
ORDER BY customers DESC;
```

---

## API Endpoints

### 1. POST /api/save-location.php

Save user's geolocation coordinates.

**Request:**
```javascript
POST /prt2/api/save-location.php
Content-Type: multipart/form-data

{
    latitude: 32.7767,
    longitude: -96.7970,
    source: 'gps',
    csrf_token: '...'
}
```

**Response:**
```json
{
    "success": true,
    "message": "Location saved successfully",
    "location": {
        "latitude": 32.7767,
        "longitude": -96.7970,
        "source": "gps",
        "timestamp": 1700000000,
        "address": {
            "city": "Dallas",
            "state": "Texas",
            "country": "United States",
            "postal_code": "75201"
        }
    }
}
```

### 2. GET /api/get-location-from-ip.php

Get approximate location from IP address (fallback).

**Request:**
```javascript
GET /prt2/api/get-location-from-ip.php
```

**Response:**
```json
{
    "success": true,
    "location": {
        "latitude": 32.7767,
        "longitude": -96.7970,
        "city": "Dallas",
        "state": "Texas",
        "state_code": "TX",
        "country": "United States",
        "country_code": "US",
        "postal_code": "75201",
        "timezone": "America/Chicago",
        "source": "ip"
    },
    "message": "Location determined from IP address"
}
```

### 3. GET /api/estimate-shipping.php

Calculate shipping cost based on distance.

**Request:**
```javascript
GET /prt2/api/estimate-shipping.php?lat=32.7767&lon=-96.7970&weight=1
```

**Response:**
```json
{
    "success": true,
    "estimate": {
        "distance": 125.5,
        "standard": {
            "cost": 12.27,
            "days": "3-5",
            "name": "Standard Shipping"
        },
        "express": {
            "cost": 30.68,
            "days": "1-2",
            "name": "Express Shipping"
        }
    },
    "delivery_available": true,
    "distance_miles": 125.5
}
```

**Shipping Calculation:**
```
Base Rate: $5.99
Distance Rate: Distance × $0.05/mile
Weight Rate: Weight × $0.50/lb

Standard = Base + Distance + Weight
Express = Standard × 2.5
```

### 4. GET /api/suggest-address.php

Get suggested address fields for checkout.

**Request:**
```javascript
GET /prt2/api/suggest-address.php
```

**Response:**
```json
{
    "success": true,
    "address": {
        "city": "Dallas",
        "state": "Texas",
        "country": "United States",
        "postal_code": "75201"
    }
}
```

### 5. POST /api/log-location-action.php

Log user action for analytics.

**Request:**
```javascript
POST /prt2/api/log-location-action.php
Content-Type: multipart/form-data

{
    action: 'allowed',
    csrf_token: '...'
}
```

**Response:**
```json
{
    "success": true,
    "message": "Action logged successfully"
}
```

---

## Configuration Options

### JavaScript Configuration

```javascript
window.geoManager = new GeolocationManager({
    // Auto-show prompt on first visit (default: true)
    autoPrompt: true,

    // Use custom modal instead of browser prompt (default: false)
    customModal: true,

    // Fall back to IP geolocation if GPS denied (default: true)
    ipFallback: true,

    // Session storage key (default: 'prt_user_location')
    storageKey: 'prt_user_location',

    // Callbacks
    onSuccess: function(location) {
        console.log('Location obtained:', location);
    },

    onError: function(error) {
        console.error('Location error:', error);
    },

    onDenied: function() {
        console.log('User denied location access');
    }
});
```

### PHP Configuration

**Warehouse Location** (for shipping calculations):

Edit `includes/geolocation-functions.php`:

```php
// Line 189 - Your warehouse/shipping location
$warehouseLat = 32.7767;  // Update with your latitude
$warehouseLon = -96.7970; // Update with your longitude
```

**Delivery Radius:**

```php
// Line 238 - Maximum delivery distance
return $distance <= 2500; // 2500 mile radius
```

**Shipping Rates:**

```php
// Lines 193-195 - Adjust shipping costs
$baseRate = 5.99;              // Base shipping cost
$distanceRate = $distance * 0.05;  // 5 cents per mile
$weightRate = $weight * 0.50;      // 50 cents per lb
```

**State Tax Rates:**

```php
// Lines 262-268 - Add your state tax rates
$taxRates = [
    'TX' => 0.0825,  // Texas 8.25%
    'CA' => 0.0725,  // California 7.25%
    'NY' => 0.08,    // New York 8%
    // Add more states...
];
```

---

## Privacy & Security

### User Privacy

✅ **Session-only storage** - Coordinates stored in session, cleared on logout
✅ **No persistent tracking** - Location not saved without user login
✅ **Explicit consent** - Clear modal explains usage before requesting
✅ **Easy opt-out** - Users can deny with one click
✅ **IP fallback optional** - Can disable if privacy is paramount

### Security Measures

✅ **CSRF protection** - All API endpoints require valid CSRF token
✅ **Input validation** - Coordinates validated for proper ranges
✅ **SQL injection protection** - Prepared statements for all queries
✅ **XSS protection** - All output properly escaped
✅ **Rate limiting** - IP API has built-in rate limits (45 req/min)
✅ **HTTPS recommended** - Geolocation API requires secure context

### Data Retention

- **Session data:** Cleared when browser closed
- **Database records:** Kept for analytics (can configure retention policy)
- **Analytics logs:** Recommend 90-day retention

### GDPR Compliance

If serving EU customers:

1. **Update privacy policy** to mention location tracking
2. **Provide opt-out** mechanism (already included)
3. **Allow data deletion** - add endpoint to delete user locations
4. **Get explicit consent** - modal already does this
5. **Document purpose** - clearly state why location is needed

**Example deletion endpoint:**

```php
// api/delete-my-location.php
if (isset($_SESSION['user_id'])) {
    $stmt = $dbConnect->prepare("
        DELETE FROM user_locations
        WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
}
```

---

## Testing

### Browser Testing

**Chrome/Edge:**
```
1. Open DevTools (F12)
2. Press Ctrl+Shift+P (Command palette)
3. Type "sensor"
4. Select "Show Sensors"
5. Set custom location or use presets
```

**Firefox:**
```
1. Type about:config in address bar
2. Search "geo.enabled"
3. Toggle to false/true to test denied state
4. Use browser extensions for custom coordinates
```

**Safari:**
```
1. Safari → Preferences → Privacy
2. Enable "Prevent cross-site tracking"
3. Test location prompts
```

### Testing Checklist

- [ ] Modal appears 2 seconds after page load (first visit)
- [ ] "Allow" button requests browser permission
- [ ] "This Time" button doesn't save to storage
- [ ] "Don't Allow" falls back to IP geolocation
- [ ] Location saved to session correctly
- [ ] Shipping estimate calculates properly
- [ ] Address pre-fill works on checkout
- [ ] Analytics logging works
- [ ] CSRF protection blocks invalid requests
- [ ] IP fallback works when GPS denied
- [ ] Location cleared on logout

### Manual Testing

**Test GPS Location:**
```javascript
// In browser console
geoManager.getLocation().then(loc => console.log(loc));
```

**Test Shipping Estimate:**
```javascript
// Test with 5 lb package
geoManager.getShippingEstimate(5).then(est => console.log(est));
```

**Test IP Fallback:**
```javascript
// Deny browser permission, then check:
fetch('/prt2/api/get-location-from-ip.php')
    .then(r => r.json())
    .then(d => console.log(d));
```

### Curl Testing

**Test save location:**
```bash
curl -X POST http://localhost:8300/api/save-location.php \
  -d "latitude=32.7767" \
  -d "longitude=-96.7970" \
  -d "source=gps" \
  -d "csrf_token=YOUR_TOKEN"
```

**Test shipping estimate:**
```bash
curl "http://localhost:8300/api/estimate-shipping.php?lat=32.7767&lon=-96.7970&weight=1"
```

---

## Analytics

### Key Metrics to Track

**Acceptance Rates:**
```sql
SELECT
    action,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM location_analytics WHERE action IN ('allowed', 'denied')), 2) as percentage
FROM location_analytics
WHERE action IN ('allowed', 'denied')
GROUP BY action;
```

**Geographic Distribution:**
```sql
SELECT
    state,
    city,
    COUNT(*) as customers,
    COUNT(DISTINCT user_id) as unique_users
FROM user_locations
GROUP BY state, city
ORDER BY customers DESC
LIMIT 20;
```

**Location Sources:**
```sql
SELECT
    source,
    COUNT(*) as count
FROM user_locations
GROUP BY source;
```

**Daily Location Requests:**
```sql
SELECT
    DATE(created_at) as date,
    action,
    COUNT(*) as count
FROM location_analytics
GROUP BY DATE(created_at), action
ORDER BY date DESC;
```

### Analytics Dashboard Queries

**Popular Shipping Zones:**
```php
// Find most common delivery distances
$stmt = $dbConnect->query("
    SELECT
        CASE
            WHEN distance < 100 THEN 'Local (< 100 mi)'
            WHEN distance < 500 THEN 'Regional (100-500 mi)'
            WHEN distance < 1500 THEN 'National (500-1500 mi)'
            ELSE 'Long Distance (> 1500 mi)'
        END as zone,
        COUNT(*) as customers
    FROM (
        SELECT
            ROUND(
                3959 * ACOS(
                    COS(RADIANS(32.7767)) * COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(-96.7970)) +
                    SIN(RADIANS(32.7767)) * SIN(RADIANS(latitude))
                )
            ) as distance
        FROM user_locations
    ) as distances
    GROUP BY zone
");
```

---

## Troubleshooting

### Common Issues

#### 1. Modal Not Appearing

**Symptoms:** Location modal doesn't show on first visit

**Solutions:**
- Check browser console for JavaScript errors
- Verify `geolocation.js` is loaded correctly
- Check if `sessionStorage.getItem('prt_location_prompted')` is set
- Clear session storage and refresh page

**Debug:**
```javascript
// In console
console.log(window.geoManager);
sessionStorage.clear();
location.reload();
```

#### 2. "Geolocation Not Supported"

**Symptoms:** Browser doesn't support Geolocation API

**Solutions:**
- Only works on HTTPS (or localhost)
- Some browsers require secure context
- IP fallback should activate automatically

**Check:**
```javascript
console.log('Geolocation supported:', 'geolocation' in navigator);
console.log('Secure context:', window.isSecureContext);
```

#### 3. Permission Always Denied

**Symptoms:** User previously denied, can't re-allow

**Solutions:**
- Browser remembers permission per domain
- User must manually reset in browser settings
- Clear site data and try again

**Chrome:** `chrome://settings/content/location`
**Firefox:** Click lock icon → Clear permissions
**Safari:** Preferences → Websites → Location

#### 4. IP Geolocation Not Working

**Symptoms:** Fallback returns no data

**Solutions:**
- Check if localhost (IP APIs skip local IPs)
- Verify API is not rate-limited (45 req/min)
- Check curl access to ip-api.com

**Test:**
```bash
curl "http://ip-api.com/json/?fields=status,country,lat,lon"
```

#### 5. Shipping Estimates Incorrect

**Symptoms:** Shipping costs seem wrong

**Solutions:**
- Verify warehouse coordinates in `geolocation-functions.php` line 189
- Check shipping rate multipliers (lines 193-195)
- Confirm distance calculation (Haversine formula)

**Debug:**
```javascript
geoManager.getShippingEstimate(1).then(est => {
    console.log('Distance:', est.distance_miles);
    console.log('Standard:', est.estimate.standard);
    console.log('Express:', est.estimate.express);
});
```

#### 6. CSRF Token Errors

**Symptoms:** API returns 403 Forbidden

**Solutions:**
- Verify CSRF token is included in requests
- Check if session has started
- Confirm CSRF token is valid

**Debug:**
```javascript
console.log('CSRF Token:', CSRF_TOKEN);
```

#### 7. Database Tables Not Created

**Symptoms:** SQL errors about missing tables

**Solutions:**
- Tables are auto-created on first use
- Verify database permissions allow CREATE TABLE
- Manually run CREATE TABLE statements

**Manual creation:**
```php
php -r "require 'includes/geolocation-functions.php'; saveLocationToDatabase(1, 32.7767, -96.7970, 'gps');"
```

---

## Future Enhancements

### Planned Features

- [ ] **Multi-warehouse support** - Calculate from nearest warehouse
- [ ] **Real-time carrier rates** - Integrate with USPS/FedEx/UPS APIs
- [ ] **Geofencing** - Special deals for local customers
- [ ] **Store pickup availability** - Show if local pickup is available
- [ ] **Weather-based alerts** - "Shipping may be delayed due to weather in your area"
- [ ] **Location-based product recommendations** - "Popular in your area"
- [ ] **Distance-based free shipping** - "Free shipping within 100 miles"
- [ ] **Heat maps** - Admin dashboard showing customer density
- [ ] **A/B testing** - Test different modal designs
- [ ] **Progressive disclosure** - Show benefits based on user behavior

### Advanced Integrations

**Google Maps API:**
```javascript
// Display user location on map
const map = new google.maps.Map(element, {
    center: { lat: location.latitude, lng: location.longitude },
    zoom: 12
});
```

**Timezone Detection:**
```php
// Use location to detect timezone
$timezone = getTimezoneFromLocation($latitude, $longitude);
date_default_timezone_set($timezone);
```

**Weather API:**
```php
// Show weather at customer location
$weather = getWeatherByLocation($latitude, $longitude);
echo "Current weather in {$city}: {$weather['temp']}°F";
```

---

## Best Practices

### Do's ✅

- **Do explain benefits clearly** before requesting permission
- **Do provide fallback options** (IP geolocation)
- **Do respect user privacy** and provide opt-out
- **Do cache location** to avoid re-prompting
- **Do use HTTPS** for geolocation to work properly
- **Do test on mobile devices** - different UX
- **Do log analytics** to measure adoption
- **Do have clear privacy policy** mentioning location usage

### Don'ts ❌

- **Don't prompt immediately** - wait 2-3 seconds after page load
- **Don't require location** - make it optional and beneficial
- **Don't track without consent** - always ask first
- **Don't persist sensitive data** - use session storage
- **Don't ignore errors** - handle all error cases gracefully
- **Don't forget mobile** - test on phones/tablets
- **Don't over-request** - cache and reuse location data
- **Don't ignore GDPR** - comply with privacy regulations

---

## Summary

The Geolocation System provides a foundation for location-aware features that enhance the shopping experience while respecting user privacy. Key highlights:

✅ **Implemented:** GPS and IP-based location detection
✅ **Functional:** Shipping estimates, address pre-fill, tax calculation
✅ **Secure:** CSRF protection, input validation, session-only storage
✅ **User-friendly:** Custom modal with clear benefits
✅ **Fallback:** IP geolocation when GPS unavailable
✅ **Analytics:** Track acceptance rates and usage patterns
✅ **Privacy-first:** Explicit consent, easy opt-out, no tracking without permission

**Access Points:**

- **Live Site:** http://localhost:8300 (modal appears on first visit)
- **API Endpoints:** http://localhost:8300/api/
- **Session Data:** `$_SESSION['user_location']`
- **Analytics:** `location_analytics` database table

**Next Steps:**

1. Update warehouse coordinates for accurate shipping
2. Customize shipping rates for your business
3. Add state tax rates for all states you ship to
4. Test on mobile devices
5. Monitor analytics to optimize acceptance rate
6. Update privacy policy to mention location usage

---

**Documentation Version:** 1.0
**Last Updated:** November 25, 2025
**Maintained By:** Development Team
**Questions?** See troubleshooting section or contact support

---

## Quick Reference

### JavaScript API

```javascript
// Get current location
const location = await geoManager.getLocation();

// Get shipping estimate
const shipping = await geoManager.getShippingEstimate(weight);

// Show location prompt manually
geoManager.promptUser();

// Clear stored location
geoManager.clearLocation();
```

### PHP API

```php
// Get user location
$location = getUserLocation();

// Calculate shipping
$estimate = estimateShipping($lat, $lon, $weight);

// Check delivery
$available = isDeliveryAvailable($lat, $lon);

// Suggest address
$address = suggestCheckoutAddress();
```

### Database Queries

```sql
-- User's location history
SELECT * FROM user_locations WHERE user_id = ?;

-- Recent location requests
SELECT * FROM location_analytics ORDER BY created_at DESC LIMIT 100;

-- Acceptance rate
SELECT action, COUNT(*) FROM location_analytics
WHERE action IN ('allowed', 'denied') GROUP BY action;
```

---

**End of Documentation**
