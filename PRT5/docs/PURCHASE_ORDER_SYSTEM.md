# Purchase Order Management System

**Status**: ✅ FULLY IMPLEMENTED
**Implementation Date**: November 25, 2025
**Version**: 1.0.0

## Overview

Complete supplier purchase order management system with barcode scanner support, automatic inventory updates, and full API integration. This system allows you to create purchase orders to suppliers, track their status through the fulfillment process, and receive incoming inventory with automatic stock quantity updates.

## System Architecture

### Database Schema

#### 1. purchase_orders
Main purchase order records tracking orders placed with suppliers.

**Key Fields**:
- `id` - Primary key
- `po_number` - Unique PO number (format: PO-YYYYMMDD-XXX)
- `supplier_name`, `supplier_email`, `supplier_phone`, `supplier_address` - Supplier information
- `order_date` - Date PO was created
- `expected_delivery_date` - When shipment is expected
- `actual_delivery_date` - When items were actually received
- `status` - Current status (draft, ordered, shipped, partially_received, received, cancelled)
- `subtotal`, `shipping_cost`, `tax`, `total_cost` - Cost tracking
- `notes` - Additional notes

#### 2. purchase_order_items
Line items for each purchase order (what products, how many, at what cost).

**Key Fields**:
- `id` - Primary key
- `purchase_order_id` - FK to purchase_orders
- `product_id` - FK to products3
- `product_name` - Snapshot of product name
- `sku` - Product UPC/SKU
- `quantity_ordered` - How many ordered
- `quantity_received` - How many received so far
- `unit_cost` - Cost per unit
- `line_total` - quantity_ordered × unit_cost

#### 3. purchase_order_receiving
Detailed log of when items were received (supports multiple receiving sessions per PO).

**Key Fields**:
- `id` - Primary key
- `purchase_order_id` - FK to purchase_orders
- `purchase_order_item_id` - FK to purchase_order_items
- `product_id` - FK to products3
- `quantity_received` - How many received in this session
- `received_date` - When received
- `condition` - good, damaged, or defective
- `notes` - Any issues or notes
- `received_by` - User ID who processed receiving

### Status Workflow

```
draft → ordered → shipped → partially_received → received
                                       ↓
                                   cancelled
```

- **draft**: PO created but not yet sent to supplier
- **ordered**: PO sent to supplier, awaiting shipment
- **shipped**: Supplier has shipped the items
- **partially_received**: Some items received, some still pending
- **received**: All items received (PO complete)
- **cancelled**: PO was cancelled

## API Endpoints

**Base URL**: `http://localhost:8300/api/v1/admin/purchase-orders`

### 1. List Purchase Orders
```
GET /api/v1/admin/purchase-orders
```
**Query Parameters**:
- `status` - Filter by status
- `supplier` - Search by supplier name
- `date_from` - Filter by order date (from)
- `date_to` - Filter by order date (to)
- `page` - Page number
- `per_page` - Results per page (max 100)

**Response**: Paginated list of POs with item counts and received percentages

### 2. Get Statistics
```
GET /api/v1/admin/purchase-orders/stats
```
**Response**: Dashboard statistics including:
- Total purchase orders
- Count by status (draft, ordered, shipped, received)
- Pending orders count
- Total value of all POs

### 3. Get Pending for Receiving
```
GET /api/v1/admin/purchase-orders/pending-receiving
```
Returns POs that are ready to receive (status: ordered, shipped, or partially_received) with their pending items.

### 4. Get Suppliers List
```
GET /api/v1/admin/purchase-orders/suppliers
```
Returns list of suppliers from existing POs (for autocomplete/dropdown).

### 5. Get Purchase Order Details
```
GET /api/v1/admin/purchase-orders/{id}
```
Returns complete PO details including all line items with current stock levels.

### 6. Create Purchase Order
```
POST /api/v1/admin/purchase-orders
```
**Request Body**:
```json
{
  "supplier_name": "ABC Supply Co.",
  "supplier_email": "orders@abcsupply.com",
  "supplier_phone": "555-1234",
  "order_date": "2025-11-25",
  "expected_delivery_date": "2025-12-02",
  "shipping_cost": 25.00,
  "tax": 0,
  "notes": "Rush order",
  "items": [
    {
      "product_id": 123,
      "quantity_ordered": 50,
      "unit_cost": 12.99
    }
  ]
}
```

### 7. Update Purchase Order
```
PUT /api/v1/admin/purchase-orders/{id}
```
Update supplier info, expected delivery date, or notes. Cannot update items (create new PO instead).

### 8. Update Status
```
PUT /api/v1/admin/purchase-orders/{id}/status
```
**Request Body**:
```json
{
  "status": "ordered"
}
```

### 9. Receive Items
```
POST /api/v1/admin/purchase-orders/{id}/receive
```
**Request Body**:
```json
{
  "items": [
    {
      "purchase_order_item_id": 456,
      "quantity_received": 45,
      "condition": "good",
      "notes": "5 units damaged in shipping"
    }
  ]
}
```

**Actions Performed**:
1. Updates `purchase_order_items.quantity_received`
2. Logs to `purchase_order_receiving` table
3. For "good" condition items:
   - Updates `products3.stock_quantity` (adds to existing stock)
   - Creates `inventory_transactions` record (type: purchase)
4. Auto-updates PO status to `partially_received` or `received`
5. Sets `actual_delivery_date` when fully received

### 10. Delete Purchase Order
```
DELETE /api/v1/admin/purchase-orders/{id}
```
Can only delete POs with status `draft` or `cancelled`. Cascade deletes items and receiving logs.

## Frontend Pages

### 1. Purchase Orders Management
**URL**: `http://localhost:8301/admin/purchase-orders`
**File**: `pecos-backend-admin-site/resources/views/admin/purchase-orders.blade.php`

**Features**:
- **Statistics Dashboard**: 6 stat cards showing total POs, counts by status, and total value
- **Filters**: Filter by status and supplier name
- **Create New PO**:
  - Modal-based creation form
  - Product dropdown with all inventory items
  - Auto-fill cost from product data
  - Dynamic line items (add/remove rows)
  - Real-time total calculation (subtotal + shipping + tax)
  - Supplier information capture
  - Date pickers for order and expected delivery
- **PO List**:
  - Shows all POs with status badges
  - Items count and received percentage progress bar
  - Row highlighting on click
  - Pagination support
- **View Details**: Modal showing complete PO information with all line items
- **Delete**: Can delete draft POs

### 2. Inventory Receiving
**URL**: `http://localhost:8301/admin/inventory-receive`
**File**: `pecos-backend-admin-site/resources/views/admin/inventory-receive.blade.php`

**Features**:
- **Pending POs List**: Shows all POs awaiting receiving with pending item counts
- **Barcode Scanner Support**:
  - Large UPC input field
  - Press Enter or click Find to lookup product
  - Auto-increments quantity when UPC found
  - Row highlighting animation on match
  - Auto-clear and refocus for rapid scanning
- **Receiving Interface**:
  - Shows all items in selected PO
  - Displays: Ordered / Already Received / Remaining quantities
  - Input fields for quantity to receive
  - Condition dropdown (good/damaged/defective)
  - Notes field for each item
  - Process button to submit receiving
- **Automatic Updates**:
  - Updates stock quantities for "good" items
  - Logs inventory transactions
  - Updates PO status automatically
  - Shows completion message
- **Receiving Log**: Shows items received in current session with timestamps
- **Smart Workflow**:
  - Supports partial receiving (multiple sessions)
  - Auto-closes PO when fully received
  - Reloads remaining items after each receiving

## Integration with Existing Systems

### Inventory Management
- **Stock Updates**: When items are received with "good" condition, `products3.stock_quantity` is incremented
- **Transaction Logging**: All receives logged to `inventory_transactions` with type "purchase"
- **Stock Alerts**: Low stock alerts automatically clear when stock is replenished via PO receiving
- **Reports**: Received items appear in inventory movement reports

### Audit Trail
Every receiving action creates:
1. Entry in `purchase_order_receiving` (detailed log)
2. Update to `purchase_order_items.quantity_received`
3. Entry in `inventory_transactions` (for inventory audit)
4. Update to `products3.stock_quantity`

### API-First Architecture
- All operations go through REST API
- No inline SQL in frontend pages
- Proper validation and error handling
- Transaction support for data integrity
- Consistent JSON responses

## User Workflows

### Creating a Purchase Order

1. Navigate to **Purchase Orders** page
2. Click **New PO** button
3. Fill in supplier information
4. Set order date and expected delivery date
5. Click **Add Item** to add products:
   - Select product from dropdown
   - Enter quantity to order
   - Cost auto-fills from product data (editable)
   - Line total calculates automatically
6. Add shipping cost and tax if applicable
7. Review calculated total
8. Click **Create Purchase Order**
9. PO is created with status "draft"
10. Update status to "ordered" when sent to supplier

### Receiving Inventory

1. Navigate to **Receive Inventory** page
2. See list of pending purchase orders
3. Click **Select** on the PO you're receiving
4. Receiving interface loads with all pending items
5. **Option A - Barcode Scanning**:
   - Scan product UPC with barcode scanner
   - System finds matching item and highlights row
   - Quantity auto-increments
   - Repeat for each item
6. **Option B - Manual Entry**:
   - Enter quantities in "Receive Now" column
   - Select condition for each item
   - Add notes if needed
7. Click **Process Receiving**
8. System confirms action
9. Stock quantities update automatically
10. PO status updates to "partially_received" or "received"
11. Continue receiving remaining items in future sessions

## Condition Tracking

The system tracks three conditions for received items:

### Good
- Item received in perfect condition
- **Added to stock**: YES
- **Logged to inventory_transactions**: YES
- Most common condition

### Damaged
- Item received with minor damage (packaging, cosmetic)
- **Added to stock**: NO
- **Logged to receiving table**: YES with notes
- Use for items that can't be sold but might be repairable

### Defective
- Item completely unusable, must be returned/discarded
- **Added to stock**: NO
- **Logged to receiving table**: YES with notes
- Use for items that need to be returned to supplier

## Reporting & Analytics

### Available Data

From the system, you can extract:
- **PO Value Trends**: Total cost of POs over time
- **Supplier Performance**: Average delivery time by supplier
- **Receiving Accuracy**: Ordered vs received quantities
- **Damaged Goods Rate**: Percentage of items received damaged/defective
- **Stock Replenishment**: How POs affect stock levels
- **Cost Analysis**: Compare PO costs to sales prices

### API Stats Endpoint

The `/admin/purchase-orders/stats` endpoint provides:
- Total purchase orders
- Count by status
- Total value of all POs
- Pending orders count

Can be used for dashboard widgets or reporting tools.

## Security & Permissions

**Current Implementation**: Public endpoints (no authentication)

**Recommended for Production**:
- Add authentication middleware to all PO endpoints
- Restrict to users with "manager" or "admin" role
- Add permission check for PO creation/deletion
- Log all PO actions with user ID
- Add user signature to receiving (tracked in `received_by` field)

## Future Enhancements

Potential additions for future versions:

### Phase 1 (Quick Wins)
- [ ] Email notifications when PO status changes
- [ ] Print-friendly PO format for sending to suppliers
- [ ] Export PO list to CSV
- [ ] Supplier management page (add/edit/delete suppliers)
- [ ] Product search in PO creation modal

### Phase 2 (Advanced)
- [ ] Automatic reorder suggestions based on low stock
- [ ] Supplier performance metrics dashboard
- [ ] PO approval workflow (requires approval before "ordered")
- [ ] Integration with supplier APIs for electronic ordering
- [ ] Barcode label printing for received items

### Phase 3 (Enterprise)
- [ ] Multi-warehouse support (track which location receives items)
- [ ] Landed cost calculation (duties, freight, etc.)
- [ ] Currency conversion for international suppliers
- [ ] Purchase requisition system (request → approval → PO)
- [ ] Vendor portal (suppliers can view PO status)

## Troubleshooting

### Common Issues

**Issue**: PO created but items not showing
- **Cause**: Items array empty or malformed
- **Fix**: Ensure at least one item with valid product_id in POST request

**Issue**: Stock not updating after receiving
- **Cause**: Condition set to "damaged" or "defective"
- **Fix**: Only "good" condition items update stock. This is by design.

**Issue**: Cannot delete purchase order
- **Cause**: PO status is not "draft" or "cancelled"
- **Fix**: Can only delete POs that haven't been ordered yet

**Issue**: Barcode scanner not finding products
- **Cause**: UPC doesn't match any items in the PO
- **Fix**: Verify you're scanning items that are in the selected PO

**Issue**: Receiving fails with "Product not found"
- **Cause**: product_id in PO items doesn't match products3 table
- **Fix**: Data integrity issue - check database

## Database Maintenance

### Indexes
Ensure these indexes exist for performance:
```sql
-- On purchase_orders
CREATE INDEX idx_status ON purchase_orders(status);
CREATE INDEX idx_order_date ON purchase_orders(order_date);
CREATE INDEX idx_supplier ON purchase_orders(supplier_name);

-- On purchase_order_items
CREATE INDEX idx_po ON purchase_order_items(purchase_order_id);
CREATE INDEX idx_product ON purchase_order_items(product_id);

-- On purchase_order_receiving
CREATE INDEX idx_po ON purchase_order_receiving(purchase_order_id);
CREATE INDEX idx_date ON purchase_order_receiving(received_date);
```

### Data Retention
Consider archiving old POs:
- Keep "received" POs for 2 years
- Archive to separate table or export to cold storage
- Maintain receiving logs for audit requirements (typically 7 years)

## Testing

### Manual Test Scenarios

1. **Create PO with Multiple Items**
   - Create PO with 5 different products
   - Verify totals calculate correctly
   - Verify PO appears in list

2. **Partial Receiving**
   - Receive 50% of items from PO
   - Verify status = "partially_received"
   - Verify stock updated for received items
   - Receive remaining 50%
   - Verify status = "received"

3. **Damaged Items**
   - Receive items with condition "damaged"
   - Verify stock did NOT increase
   - Verify receiving log shows damaged items

4. **Barcode Scanning**
   - Scan multiple UPCs rapidly
   - Verify quantities increment correctly
   - Verify row highlighting works

5. **Status Workflow**
   - Create PO (draft)
   - Update to "ordered"
   - Update to "shipped"
   - Receive items (auto → received)
   - Verify status transitions

## Support & Documentation

**Documentation Files**:
- This file: `PRT2/docs/PURCHASE_ORDER_SYSTEM.md`
- Inventory Plan: `PRT2/docs/INVENTORY_MANAGEMENT_PLAN.md`
- Inventory Summary: `PRT2/docs/INVENTORY_IMPLEMENTATION_SUMMARY.md`
- API Endpoints: `pecos-backendadmin-api/docs/API_ENDPOINTS.md`
- Feature Locations: `PRT2/docs/FEATURE_LOCATIONS.md`

**Code Locations**:
- Controller: `pecos-backendadmin-api/app/Http/Controllers/Api/V1/Admin/PurchaseOrderController.php`
- Routes: `pecos-backendadmin-api/routes/api.php` (lines 285-295)
- Frontend PO Page: `pecos-backend-admin-site/resources/views/admin/purchase-orders.blade.php`
- Frontend Receiving: `pecos-backend-admin-site/resources/views/admin/inventory-receive.blade.php`
- Database Script: `PRT2/maintenance/create_purchase_orders_tables.php`

**Access URLs**:
- Purchase Orders: http://localhost:8301/admin/purchase-orders
- Receive Inventory: http://localhost:8301/admin/inventory-receive
- API: http://localhost:8300/api/v1/admin/purchase-orders

---

**Last Updated**: November 25, 2025
**Version**: 1.0.0
**Status**: ✅ Production Ready
