# Settings Handler Documentation

## Overview

`auth/settings-handler.php` is an AJAX endpoint that handles all user account settings operations for the Pecos River Traders frontend. It processes POST requests from the account settings page and returns JSON responses.

## Location
`C:\xampp\htdocs\PRT2\auth\settings-handler.php`

## Security Features

- **Session Authentication**: Requires `$_SESSION['user_id']` to be set
- **CSRF Protection**: Validates CSRF token on every request
- **Rate Limiting**: Uses `rateLimitAttempt('update_account')` to prevent abuse
- **Input Validation**: Uses the `Validator` class for all user inputs
- **Prepared Statements**: All database queries use PDO prepared statements to prevent SQL injection

## Actions Supported

The handler supports 17 different actions via the `action` POST parameter:

### Address Management
| Action | Description |
|--------|-------------|
| `add_address` | Add a new shipping/billing address with full validation |
| `delete_address` | Remove an address from user's account |
| `set_default_address` | Set an address as the default for its type |

### Payment Methods
| Action | Description |
|--------|-------------|
| `add_card` | Add a new credit/debit card (stores only last 4 digits) |
| `delete_card` | Remove a saved card |
| `set_default_card` | Set a card as the default payment method |

### Gift Cards
| Action | Description |
|--------|-------------|
| `add_gift_card` | Add a gift card to the account |
| `delete_gift_card` | Remove a gift card |

### User Preferences
| Action | Description |
|--------|-------------|
| `save_delivery_prefs` | Save delivery preferences (door-to-door, signature required, vacation mode, etc.) |
| `save_notifications` | Save notification preferences (email, SMS, push for various categories) |

### Profile Updates
| Action | Description |
|--------|-------------|
| `update_name` | Update first and last name |
| `update_email` | Update email address (requires password verification) |
| `update_phone` | Update phone number |
| `change_password` | Change account password (requires current password) |

### Promotional Codes
| Action | Description |
|--------|-------------|
| `apply_promo` | Apply a promotional code to the account |

## Database Tables Used

- `users` - User profile information
- `user_addresses` - Shipping and billing addresses
- `user_payment_methods` - Saved credit/debit cards
- `user_gift_cards` - Gift card balances
- `user_delivery_preferences` - Delivery settings
- `user_notification_preferences` - Email/SMS/Push settings
- `user_promo_codes` - Applied promotional codes

## Request Format

All requests must be POST with the following required fields:
- `csrf_token` - Valid CSRF token
- `action` - One of the supported action types

Additional fields depend on the action being performed.

## Response Format

All responses are JSON with the following structure:

```json
{
    "success": true|false,
    "message": "Human-readable status message"
}
```

## Example Usage

### Adding an Address
```javascript
const formData = new FormData();
formData.append('action', 'add_address');
formData.append('csrf_token', csrfToken);
formData.append('address_type', 'shipping');
formData.append('full_name', 'John Doe');
formData.append('address_line1', '123 Main St');
formData.append('city', 'Austin');
formData.append('state', 'TX');
formData.append('zip_code', '78701');
formData.append('phone', '555-123-4567');
formData.append('is_default', '1');

fetch('settings-handler.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert(data.message);
    }
});
```

### Changing Password
```javascript
const formData = new FormData();
formData.append('action', 'change_password');
formData.append('csrf_token', csrfToken);
formData.append('current_password', 'oldPassword123');
formData.append('new_password', 'newPassword456');

fetch('settings-handler.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    alert(data.message);
});
```

## Validation Rules

### Address Fields
- **Full Name**: Required, max 100 characters
- **Address Line 1**: Required, max 200 characters
- **Address Line 2**: Optional, max 200 characters
- **City**: Required, max 100 characters
- **State**: Required, valid US state code
- **ZIP Code**: Required, valid US ZIP format
- **Phone**: Required, valid phone format

### Payment Card
- Card number is parsed to detect type (Visa, Mastercard, Amex, Discover)
- Only last 4 digits are stored

### Password
- Minimum 8 characters
- Current password must be verified before changing

### Email
- Must be valid email format
- Must not be in use by another account
- Requires password verification to change

## Why Direct SQL is Used

This handler uses direct SQL queries instead of the Laravel API for several reasons:

1. **Transaction Safety**: Multiple related updates (like setting default addresses) need atomic operations
2. **Session Management**: Updates to `$_SESSION` variables happen immediately after database changes
3. **Validation Chain**: Complex validation with immediate error feedback
4. **Password Handling**: Secure password verification and hashing with `password_verify()` and `password_hash()`

## Related Files

- `auth/account-settings.php` - The frontend page that calls this handler
- `config/validation.php` - The `Validator` class used for input validation
- `config/csrf.php` - CSRF token generation and validation
- `config/ratelimit.php` - Rate limiting functionality

## Error Handling

All database operations are wrapped in a try-catch block. Errors return:
```json
{
    "success": false,
    "message": "An error occurred: [error details]"
}
```

---

*Last Updated: 2025-11-20*
