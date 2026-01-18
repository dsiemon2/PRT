/**
 * TableControls - Grid functionality for admin tables
 * Handles row selection, bulk actions, and pagination
 */
class TableControls {
    constructor(options = {}) {
        this.tableId = options.tableId || 'dataTable';
        this.bulkActionsId = options.bulkActionsId || 'bulkActions';
        this.selectAllId = options.selectAllId || 'selectAll';
        this.selectedCountId = options.selectedCountId || 'selectedCount';
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
        this.bulkActionUrl = options.bulkActionUrl || '';
        this.onActionComplete = options.onActionComplete || null;

        this.selectedIds = new Set();
        this.init();
    }

    init() {
        this.table = document.getElementById(this.tableId);
        this.bulkActions = document.getElementById(this.bulkActionsId);
        this.selectAllCheckbox = document.getElementById(this.selectAllId);
        this.selectedCountElement = document.getElementById(this.selectedCountId);

        if (!this.table) return;

        this.bindEvents();
    }

    bindEvents() {
        // Select all checkbox
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', (e) => {
                this.toggleSelectAll(e.target.checked);
            });
        }

        // Individual row checkboxes
        this.table.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleRow(e.target);
            });
        });

        // Row click highlighting (without checkbox toggle)
        this.table.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', (e) => {
                // Don't highlight if clicking on a checkbox, link, or button
                if (e.target.closest('input, a, button')) return;
                row.classList.toggle('table-active');
            });
        });

        // Bulk action buttons
        document.querySelectorAll('[data-bulk-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = e.target.closest('[data-bulk-action]').dataset.bulkAction;
                this.executeBulkAction(action);
            });
        });

        // Page size selector
        const pageSizeSelect = document.getElementById('pageSize');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (e) => {
                this.changePageSize(e.target.value);
            });
        }
    }

    toggleSelectAll(checked) {
        this.table.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
            const id = checkbox.value;
            if (checked) {
                this.selectedIds.add(id);
                checkbox.closest('tr').classList.add('table-warning');
            } else {
                this.selectedIds.delete(id);
                checkbox.closest('tr').classList.remove('table-warning');
            }
        });
        this.updateBulkActionsVisibility();
    }

    toggleRow(checkbox) {
        const id = checkbox.value;
        const row = checkbox.closest('tr');

        if (checkbox.checked) {
            this.selectedIds.add(id);
            row.classList.add('table-warning');
        } else {
            this.selectedIds.delete(id);
            row.classList.remove('table-warning');
        }

        // Update select all checkbox
        if (this.selectAllCheckbox) {
            const allCheckboxes = this.table.querySelectorAll('.row-checkbox');
            const checkedCheckboxes = this.table.querySelectorAll('.row-checkbox:checked');
            this.selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
            this.selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }

        this.updateBulkActionsVisibility();
    }

    updateBulkActionsVisibility() {
        const count = this.selectedIds.size;

        if (this.bulkActions) {
            if (count > 0) {
                this.bulkActions.classList.remove('d-none');
                this.bulkActions.style.display = 'block';
            } else {
                this.bulkActions.classList.add('d-none');
                this.bulkActions.style.display = 'none';
            }
        }

        if (this.selectedCountElement) {
            this.selectedCountElement.textContent = count;
        }
    }

    async executeBulkAction(action) {
        if (this.selectedIds.size === 0) {
            this.showToast('Please select at least one item.', 'warning');
            return;
        }

        // Confirmation for destructive actions
        const destructiveActions = ['delete', 'spam', 'reject'];
        if (destructiveActions.includes(action)) {
            if (!confirm(`Are you sure you want to ${action} ${this.selectedIds.size} item(s)?`)) {
                return;
            }
        }

        try {
            const response = await fetch(this.bulkActionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    ids: Array.from(this.selectedIds)
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showToast(result.message || 'Action completed successfully.', 'success');

                // Update UI based on action
                this.handleActionResult(action, result);

                // Clear selection
                this.selectedIds.clear();
                this.updateBulkActionsVisibility();

                // Reset checkboxes
                if (this.selectAllCheckbox) {
                    this.selectAllCheckbox.checked = false;
                    this.selectAllCheckbox.indeterminate = false;
                }

                // Callback
                if (this.onActionComplete) {
                    this.onActionComplete(action, result);
                }
            } else {
                this.showToast(result.message || 'Action failed.', 'danger');
            }
        } catch (error) {
            console.error('Bulk action error:', error);
            this.showToast('An error occurred. Please try again.', 'danger');
        }
    }

    handleActionResult(action, result) {
        const idsToUpdate = Array.from(this.selectedIds);

        idsToUpdate.forEach(id => {
            const row = this.table.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;

            switch (action) {
                case 'delete':
                    // Fade out and remove row
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                    break;

                case 'approve':
                    this.updateRowStatus(row, 'approved', 'success');
                    break;

                case 'reject':
                    this.updateRowStatus(row, 'rejected', 'danger');
                    break;

                case 'spam':
                    this.updateRowStatus(row, 'spam', 'secondary');
                    break;

                case 'resolve':
                    this.updateRowStatus(row, 'resolved', 'success');
                    break;

                default:
                    // Just remove highlighting
                    row.classList.remove('table-warning');
            }
        });

        // Update stats if provided
        if (result.stats) {
            this.updateStats(result.stats);
        }
    }

    updateRowStatus(row, status, badgeClass) {
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `badge bg-${badgeClass} status-badge`;
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        }
        row.classList.remove('table-warning');

        const checkbox = row.querySelector('.row-checkbox');
        if (checkbox) {
            checkbox.checked = false;
        }
    }

    updateStats(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    }

    changePageSize(size) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', size);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }

    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
        }

        // Create toast
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toast);

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();

        // Remove after hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Static method for quick initialization
    static init(options) {
        return new TableControls(options);
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TableControls;
}
