# Role-Based Access Control (RBAC) System

## Overview

The PRT2 e-commerce platform implements a simple yet effective 3-tier role-based access control system to manage user permissions and restrict access to administrative functions.

**Implementation Date:** 2025
**Status:** ✅ Fully Implemented

## Role Hierarchy

```
┌─────────────┐
│   ADMIN     │ ← Full system access
└──────┬──────┘
       │
┌──────▼──────┐
│   MANAGER   │ ← Can manage inventory, view reports
└──────┬──────┘
       │
┌──────▼──────┐
│  CUSTOMER   │ ← Regular users (default)
└─────────────┘
```

## Roles and Permissions

### Customer (Default Role)
- **Level:** 1
- **Assigned:** Automatically on user registration
- **Permissions:**
  - Browse products
  - Add items to cart
  - Place orders
  - View own order history
  - Manage own account
  - Use wishlist features
  - Participate in loyalty program

### Manager
- **Level:** 2
- **Assigned:** Manually by administrators
- **Inherits:** All customer permissions
- **Additional Permissions:**
  - View inventory dashboard
  - Edit product stock levels
  - View stock alerts
  - Generate inventory reports
  - View all orders
  - Manage customer inquiries

### Admin
- **Level:** 3
- **Assigned:** Manually by existing administrators
- **Inherits:** All manager permissions
- **Additional Permissions:**
  - Manage user roles
  - Access all system settings
  - View system logs
  - Manage products and categories
  - Access all administrative functions
  - Configure payment gateways
  - Manage coupons and promotions

## Database Schema

### users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    oauth_provider VARCHAR(50) NULL,
    oauth_uid VARCHAR(255) NULL,
    oauth_token TEXT NULL,
    profile_picture VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    role ENUM('customer', 'manager', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_oauth (oauth_provider, oauth_uid),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Implementation Files

### Core Files
- `/includes/auth-functions.php` - Authentication and authorization helper functions
- `/admin/common.php` - Admin page protection (requires manager+ role)
- `/maintenance/setup_rbac_system.php` - Setup script for RBAC system

### Helper Functions

#### Check User Status
```php
isLoggedIn()                    // Returns: bool
getUserRole()                   // Returns: 'customer'|'manager'|'admin'|null
hasRole($role)                  // Returns: bool
isAdmin()                       // Returns: bool
isManager()                     // Returns: bool (manager OR admin)
hasMinRole($requiredRole)       // Returns: bool (checks hierarchy)
```

#### Enforce Access Control
```php
requireLogin($redirectUrl)      // Redirect to login if not authenticated
requireRole($role, $redirectUrl) // Enforce minimum role requirement
requireAdmin()                  // Shortcut for admin-only pages
requireManager()                // Shortcut for manager+ pages
```

#### User Information
```php
getCurrentUser()                // Returns: array of user data
clearRoleCache()                // Clear cached role from session
```

## Usage Examples

### Protecting Admin Pages

**Automatic Protection (All /admin/ pages):**
```php
<?php
require_once(__DIR__ . '/common.php');
// Manager or admin access automatically enforced
?>
```

**Admin-Only Page:**
```php
<?php
require_once(__DIR__ . '/common.php');
requireAdmin();  // Additional check for admin-only
?>
```

### Conditional UI Elements

```php
<?php if (isManager()): ?>
    <a href="/admin/inventory-dashboard.php">Inventory Management</a>
<?php endif; ?>

<?php if (isAdmin()): ?>
    <a href="/admin/user-management.php">User Management</a>
<?php endif; ?>
```

### Role-Based Logic

```php
<?php
$userRole = getUserRole();

if ($userRole === 'admin') {
    // Show all products including inactive
    $sql = "SELECT * FROM Products";
} else {
    // Show only active products
    $sql = "SELECT * FROM Products WHERE Active = 1";
}
?>
```

## Access Denied Handling

When a user attempts to access a page they don't have permission for, they see a styled 403 error page with:
- Clear explanation of insufficient permissions
- Their current role displayed
- Required role displayed
- Links to return home or their account page

## Promoting Users

### Via SQL
```sql
-- Promote to manager
UPDATE users SET role = 'manager' WHERE email = 'user@example.com';

-- Promote to admin
UPDATE users SET role = 'admin' WHERE email = 'user@example.com';

-- Demote to customer
UPDATE users SET role = 'customer' WHERE email = 'user@example.com';
```

### Via PHP (Future Implementation)
```php
// Admin interface for user management
// /admin/users.php - coming soon
```

## Security Considerations

### Session Management
- User role cached in `$_SESSION['user_role']` for performance
- Role cache automatically refreshed from database if not in session
- Call `clearRoleCache()` after role changes

### Database Security
- Role changes require direct database access or admin interface
- ENUM type prevents invalid role values
- Index on role column for efficient queries

### Page Protection
- All `/admin/` pages automatically require manager+ via common.php
- Individual pages can add stricter requirements (admin-only)
- Unauthenticated users redirected to login
- Unauthorized users see 403 error page

## Testing

### Test User Roles
```sql
-- Create test users for each role
INSERT INTO users (email, password, first_name, last_name, role) VALUES
('customer@test.com', '$2y$10$...', 'Test', 'Customer', 'customer'),
('manager@test.com', '$2y$10$...', 'Test', 'Manager', 'manager'),
('admin@test.com', '$2y$10$...', 'Test', 'Admin', 'admin');
```

### Test Scenarios
1. ✅ Customer cannot access /admin/inventory-dashboard.php
2. ✅ Manager can access /admin/inventory-dashboard.php
3. ✅ Admin can access all /admin/ pages
4. ✅ Unauthenticated users redirected to login
5. ✅ Role changes take effect immediately after session cache clear

## Future Enhancements

### Planned Features
- [ ] Admin UI for user management (promote/demote users)
- [ ] Permission-level granularity (separate from roles)
- [ ] Role assignment audit log
- [ ] Temporary role elevation
- [ ] API token-based authentication for external integrations

### Not Planned
- Complex permission trees (overkill for this application)
- Multi-tenant role separation (single store)
- Dynamic role creation (3 roles sufficient)

## Maintenance

### Setup/Installation
```bash
# Run RBAC setup script
php C:\xampp\htdocs\PRT2\maintenance\setup_rbac_system.php

# Promote first admin
# UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

### Monitoring
- Check active roles: `SELECT role, COUNT(*) FROM users GROUP BY role;`
- List all admins: `SELECT email, first_name, last_name FROM users WHERE role = 'admin';`
- Find unauthorized access attempts: Check web server error logs for 403 responses

## References

- Database Schema: `/docs/DATABASE.md`
- Auth Functions: `/includes/auth-functions.php`
- Setup Script: `/maintenance/setup_rbac_system.php`
- Admin Common: `/admin/common.php`
