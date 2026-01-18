# Grid & Table Standards

**Version**: 1.0.0
**Last Updated**: December 2, 2025
**Reference Implementation**: Backend admin `/admin/purchase-orders`

---

## Table of Contents

1. [Overview](#overview)
2. [Row Selection](#row-selection)
3. [Action Buttons with Tooltips](#action-buttons-with-tooltips)
4. [Pagination](#pagination)
5. [Complete Implementation Example](#complete-implementation-example)
6. [Checklist](#checklist)

---

## Overview

This document defines the standard behavior for all data grids and tables across the Pecos River Traders interfaces. All tables should follow these patterns for consistency.

### Reference Implementation

The **Purchase Orders** page in the backend admin (`/admin/purchase-orders`) serves as the canonical reference for grid behavior. When in doubt, match this page's behavior.

### Key Features

1. **Row Selection** - Light blue background on click
2. **Action Buttons** - Icon buttons with tooltips
3. **Pagination** - "Showing X to Y of Z entries" with Previous/Next controls

---

## Row Selection

### Behavior

- Clicking anywhere on a row highlights it with a light blue background
- Only one row can be selected at a time
- Clicking a different row deselects the previous selection
- Clicking on buttons, links, or form controls does NOT trigger row selection
- Cursor changes to pointer on hover

### CSS Implementation

```css
/* Row selection styles - Add to page <style> section */
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong,
.table tbody tr.row-selected td .fw-semibold,
.table tbody tr.row-selected td .fw-bold {
    color: #333 !important;
}
.table tbody tr {
    cursor: pointer;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
```

### JavaScript Implementation

```javascript
// Row selection function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;

    // Don't highlight if clicking on interactive elements
    if (target.tagName === 'BUTTON' || target.tagName === 'A' ||
        target.tagName === 'SELECT' || target.tagName === 'INPUT' ||
        target.tagName === 'I' || target.closest('button') ||
        target.closest('a') || target.closest('select') ||
        target.closest('.btn-group')) {
        return;
    }

    // Remove selection from all other rows
    document.querySelectorAll('.table tbody tr.row-selected').forEach(function(r) {
        r.classList.remove('row-selected');
    });

    // Add selection to clicked row
    row.classList.add('row-selected');
}
```

### HTML Implementation

```html
<tbody id="tableBody">
    <tr onclick="highlightRow(event)">
        <td>Row content...</td>
        <td>More content...</td>
        <td>Actions...</td>
    </tr>
</tbody>
```

### Color Specifications

| State | Background Color | Text Color |
|-------|-----------------|------------|
| Default | transparent | inherit |
| Hover | #f8f9fa (light gray) | inherit |
| Selected | #e3f2fd (light blue) | #333 (dark) |

---

## Action Buttons with Tooltips

### Standard Action Button Pattern

All action buttons should:
- Use Bootstrap icon buttons (`btn-outline-*` or `btn-sm`)
- Include a `title` attribute for tooltip
- Be grouped in a `btn-group` for multiple actions
- Use appropriate icons from Bootstrap Icons

### HTML Implementation

```html
<!-- Single Action Button -->
<a href="/admin/items/123" class="btn btn-sm btn-outline-primary" title="View item details">
    <i class="bi bi-eye"></i>
</a>

<!-- Multiple Action Buttons -->
<div class="btn-group btn-group-sm">
    <a href="/admin/items/123" class="btn btn-outline-primary" title="View details">
        <i class="bi bi-eye"></i>
    </a>
    <button class="btn btn-outline-warning" onclick="editItem(123)" title="Edit this item">
        <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-outline-danger" onclick="deleteItem(123)" title="Delete this item">
        <i class="bi bi-trash"></i>
    </button>
</div>
```

### Standard Icons

| Action | Icon | Button Style | Tooltip Text |
|--------|------|--------------|--------------|
| View | `bi-eye` | `btn-outline-primary` | "View [item] details" |
| Edit | `bi-pencil` | `btn-outline-warning` | "Edit this [item]" |
| Delete | `bi-trash` | `btn-outline-danger` | "Delete this [item]" |
| Approve | `bi-check-lg` | `btn-outline-success` | "Approve this [item]" |
| Receive | `bi-box-arrow-in-down` | `btn-outline-info` | "Receive [item]" |
| Print | `bi-printer` | `btn-outline-secondary` | "Print [item]" |
| Download | `bi-download` | `btn-outline-secondary` | "Download [item]" |

### Tooltip Guidelines

1. **Be Descriptive**: "View order details" not just "View"
2. **Include Context**: "Approve this wholesale account" not just "Approve"
3. **Use Action Verbs**: Start with verbs like View, Edit, Delete, Approve
4. **Keep Brief**: Maximum 5-6 words

---

## Pagination

### Standard Layout

```
[Showing 1 to 10 of 45 entries]                    [Previous] [1] [2] [3] [4] [5] [Next]
```

### HTML Structure

```html
<!-- Pagination Footer -->
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small" id="paginationInfo">Showing 0 entries</div>
        <nav aria-label="Table pagination">
            <ul class="pagination pagination-sm mb-0" id="tablePagination">
            </ul>
        </nav>
    </div>
</div>
```

### JavaScript Implementation

```javascript
// Pagination variables
var currentPage = 1;
var itemsPerPage = 10;  // Standard: 10 items per page
var allData = [];       // All data to paginate

function renderTable() {
    var tbody = document.getElementById('tableBody');
    var start = (currentPage - 1) * itemsPerPage;
    var end = start + itemsPerPage;
    var pageData = allData.slice(start, end);

    // Render rows
    var html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="X" class="text-center py-5">' +
               '<div class="text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>' +
               '<p class="mb-0">No items found</p></div></td></tr>';
    } else {
        pageData.forEach(function(item) {
            html += '<tr onclick="highlightRow(event)">';
            // ... build row HTML
            html += '</tr>';
        });
    }
    tbody.innerHTML = html;

    renderPagination();
}

function renderPagination() {
    var totalPages = Math.ceil(allData.length / itemsPerPage);
    var total = allData.length;
    var start = (currentPage - 1) * itemsPerPage;
    var end = Math.min(start + itemsPerPage, total);

    // Update info text
    if (total > 0) {
        document.getElementById('paginationInfo').textContent =
            'Showing ' + (start + 1) + ' to ' + end + ' of ' + total + ' entries';
    } else {
        document.getElementById('paginationInfo').textContent = 'Showing 0 entries';
    }

    // Build pagination buttons
    var pagination = document.getElementById('tablePagination');
    var html = '';

    // Previous button
    html += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToPage(' + (currentPage - 1) + '); return false;">Previous</a></li>';

    // Page numbers
    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="goToPage(' + i + '); return false;">' + i + '</a></li>';
    }

    // Next button
    html += '<li class="page-item ' + (currentPage === totalPages || totalPages === 0 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToPage(' + (currentPage + 1) + '); return false;">Next</a></li>';

    pagination.innerHTML = html;
}

function goToPage(page) {
    var totalPages = Math.ceil(allData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTable();
});
```

### Pagination Standards

| Setting | Value | Notes |
|---------|-------|-------|
| Items per page | 5-10 | Use 5 for sample data, 10-20 for production |
| Info text format | "Showing X to Y of Z entries" | Always show this |
| Previous/Next | Text buttons | Use "Previous" and "Next" text |
| Page numbers | Show all for < 7 pages | Truncate with "..." for many pages |
| Position | Bottom of card | Inside `card-footer` |

---

## Complete Implementation Example

### PHP Page Template

```php
<?php
/**
 * Items List Page
 *
 * @package PRT2
 * @since December 2025
 */

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/layout.php');

requireAuth();

// Fetch items from database
$items = [];
try {
    $stmt = $dbConnect->query("SELECT * FROM items ORDER BY created_at DESC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching items: " . $e->getMessage());
}

startPage(['title' => 'Items List']);
?>

<style>
/* Row selection styles */
.table tbody tr.row-selected td {
    background-color: #e3f2fd !important;
    color: #333 !important;
}
.table tbody tr.row-selected td strong,
.table tbody tr.row-selected td .fw-semibold {
    color: #333 !important;
}
.table tbody tr {
    cursor: pointer;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
</style>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-list me-2"></i>All Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Status</th>
                        <th class="text-end">Value</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- Rendered by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <div class="card-footer bg-white border-top py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small" id="itemsInfo">Showing 0 entries</div>
            <nav aria-label="Items pagination">
                <ul class="pagination pagination-sm mb-0" id="itemsPagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
// Sample data (replace with server data)
var sampleItems = [
    { id: 1, name: 'Item One', status: 'active', value: 100 },
    { id: 2, name: 'Item Two', status: 'pending', value: 200 },
    // ... more items
];

// Pagination variables
var itemsPerPage = 5;
var currentItemsPage = 1;
var serverItems = <?php echo json_encode($items); ?>;
var allItemsData = serverItems.length > 0 ? serverItems : sampleItems;

// Row selection function
function highlightRow(event) {
    var target = event.target;
    var row = target.closest('tr');
    if (!row) return;
    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'I' ||
        target.closest('button') || target.closest('a') || target.closest('.btn-group')) {
        return;
    }
    document.querySelectorAll('#itemsTableBody tr.row-selected').forEach(function(r) {
        r.classList.remove('row-selected');
    });
    row.classList.add('row-selected');
}

function renderItemsTable() {
    var tbody = document.getElementById('itemsTableBody');
    var start = (currentItemsPage - 1) * itemsPerPage;
    var end = start + itemsPerPage;
    var pageData = allItemsData.slice(start, end);

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5">' +
            '<div class="text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>' +
            '<p class="mb-0">No items found</p></div></td></tr>';
        document.getElementById('itemsInfo').textContent = 'Showing 0 entries';
        document.getElementById('itemsPagination').innerHTML = '';
        return;
    }

    var html = '';
    pageData.forEach(function(item) {
        html += '<tr onclick="highlightRow(event)">';
        html += '<td class="ps-4"><strong>' + item.name + '</strong></td>';
        html += '<td><span class="badge bg-' + (item.status === 'active' ? 'success' : 'warning') + '">' +
                item.status + '</span></td>';
        html += '<td class="text-end">$' + item.value.toLocaleString() + '</td>';
        html += '<td class="text-end pe-4">';
        html += '<div class="btn-group btn-group-sm">';
        html += '<a href="/admin/items/' + item.id + '" class="btn btn-outline-primary" title="View item details"><i class="bi bi-eye"></i></a>';
        html += '<button class="btn btn-outline-warning" onclick="editItem(' + item.id + ')" title="Edit this item"><i class="bi bi-pencil"></i></button>';
        html += '</div></td>';
        html += '</tr>';
    });
    tbody.innerHTML = html;

    renderItemsPagination();
}

function renderItemsPagination() {
    var totalPages = Math.ceil(allItemsData.length / itemsPerPage);
    var total = allItemsData.length;
    var start = (currentItemsPage - 1) * itemsPerPage;
    var end = Math.min(start + itemsPerPage, total);

    document.getElementById('itemsInfo').textContent =
        'Showing ' + (start + 1) + ' to ' + end + ' of ' + total + ' entries';

    var pagination = document.getElementById('itemsPagination');
    var html = '';

    html += '<li class="page-item ' + (currentItemsPage === 1 ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToItemsPage(' + (currentItemsPage - 1) + '); return false;">Previous</a></li>';

    for (var i = 1; i <= totalPages; i++) {
        html += '<li class="page-item ' + (i === currentItemsPage ? 'active' : '') + '">';
        html += '<a class="page-link" href="#" onclick="goToItemsPage(' + i + '); return false;">' + i + '</a></li>';
    }

    html += '<li class="page-item ' + (currentItemsPage === totalPages ? 'disabled' : '') + '">';
    html += '<a class="page-link" href="#" onclick="goToItemsPage(' + (currentItemsPage + 1) + '); return false;">Next</a></li>';

    pagination.innerHTML = html;
}

function goToItemsPage(page) {
    var totalPages = Math.ceil(allItemsData.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentItemsPage = page;
    renderItemsTable();
}

document.addEventListener('DOMContentLoaded', function() {
    renderItemsTable();
});
</script>

<?php
endPage();
?>
```

---

## Checklist

Use this checklist when implementing or reviewing grid components:

### Row Selection
- [ ] CSS styles for `.row-selected` added
- [ ] CSS for hover state (`:hover:not(.row-selected)`) added
- [ ] `cursor: pointer` on table rows
- [ ] `onclick="highlightRow(event)"` on each `<tr>`
- [ ] `highlightRow()` function ignores button/link clicks
- [ ] Only one row selected at a time

### Action Buttons
- [ ] All buttons have `title` attribute with descriptive tooltip
- [ ] Buttons use appropriate Bootstrap icon
- [ ] Buttons use correct style (`btn-outline-primary`, etc.)
- [ ] Multiple buttons grouped in `btn-group`
- [ ] Buttons don't trigger row selection

### Pagination
- [ ] Info text shows "Showing X to Y of Z entries"
- [ ] Previous/Next buttons present
- [ ] Page numbers displayed
- [ ] Disabled state for Previous (page 1) and Next (last page)
- [ ] Active state for current page
- [ ] Pagination inside `card-footer`
- [ ] Uses `pagination-sm` class

### General
- [ ] Follows purchase-orders page pattern
- [ ] Works with server data AND sample data fallback
- [ ] Empty state shows appropriate message
- [ ] Responsive design (table-responsive wrapper)

---

## Related Documentation

- [CODING_STANDARDS.md](CODING_STANDARDS.md) - General coding standards
- Backend Admin: [UI_ENHANCEMENTS.md](../../pecos-backend-admin-site/docs/UI_ENHANCEMENTS.md) - UI enhancement details
- Backend Admin: [GRID_STANDARDS.md](../../pecos-backend-admin-site/docs/GRID_STANDARDS.md) - Laravel Blade version

---

**Questions?** Contact the development team.
