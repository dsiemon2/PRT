/**
 * TableControls - Reusable Table Controls with Pagination, Selection, and Bulk Actions
 * Ported from NaggingWifeAI project for Laravel PRT5
 */

class TableControls {
    constructor(options = {}) {
        this.tableBodyId = options.tableBodyId || 'tableBody';
        this.paginationId = options.paginationId || 'pagination';
        this.pageSizeId = options.pageSizeId || 'pageSize';
        this.selectAllId = options.selectAllId || 'selectAll';
        this.bulkActionsId = options.bulkActionsId || 'bulkActions';
        this.selectedCountId = options.selectedCountId || 'selectedCount';
        this.showingStartId = options.showingStartId || 'showingStart';
        this.showingEndId = options.showingEndId || 'showingEnd';
        this.totalRowsId = options.totalRowsId || 'totalRows';
        this.hasCheckboxes = options.hasCheckboxes !== false;
        this.onBulkAction = options.onBulkAction || null;
        this.serverSidePagination = options.serverSidePagination || false;

        this.currentPage = 1;
        this.pageSize = 10;
        this.allRows = [];
        this.totalRows = 0;

        this.init();
    }

    init() {
        const tbody = document.getElementById(this.tableBodyId);
        if (tbody) {
            if (this.hasCheckboxes) {
                this.allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.querySelector('.row-checkbox'));
            } else {
                this.allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('[colspan]'));
            }
            this.totalRows = this.allRows.length;
        }

        // Initialize page size
        const pageSizeSelect = document.getElementById(this.pageSizeId);
        if (pageSizeSelect) {
            this.pageSize = parseInt(pageSizeSelect.value) || 10;
            pageSizeSelect.addEventListener('change', (e) => {
                this.pageSize = parseInt(e.target.value);
                this.currentPage = 1;
                if (this.serverSidePagination) {
                    this.triggerServerPagination();
                } else {
                    this.render();
                }
            });
        }

        // Initialize select all
        const selectAll = document.getElementById(this.selectAllId);
        if (selectAll) {
            selectAll.addEventListener('click', (e) => {
                this.toggleSelectAll(e.target.checked);
            });
        }

        // Initialize row checkboxes
        this.initRowCheckboxes();

        // Initial render (only for client-side pagination)
        if (!this.serverSidePagination) {
            this.render();
        } else {
            this.updateBulkActions();
        }
    }

    initRowCheckboxes() {
        if (this.hasCheckboxes) {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    this.updateBulkActions();
                    this.updateSelectAllState();
                });
            });
        }

        // Row click - highlights the row
        this.allRows.forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', (e) => {
                this.handleRowClick(e, row);
            });
        });
    }

    handleRowClick(event, row) {
        const target = event.target;

        // Don't highlight if clicking on interactive elements
        if (target.tagName === 'BUTTON' || target.tagName === 'A' ||
            target.tagName === 'INPUT' || target.tagName === 'SELECT' ||
            target.tagName === 'I' || target.closest('button') ||
            target.closest('a') || target.closest('.btn-group') ||
            target.closest('.dropdown')) {
            return;
        }

        // Clear all other row highlights
        this.allRows.forEach(r => r.classList.remove('selected'));

        // Highlight the clicked row
        row.classList.add('selected');
    }

    toggleSelectAll(checked) {
        const visibleCheckboxes = this.getVisibleCheckboxes();
        visibleCheckboxes.forEach(cb => {
            cb.checked = checked;
        });
        this.updateBulkActions();
    }

    getVisibleCheckboxes() {
        if (this.serverSidePagination) {
            return Array.from(document.querySelectorAll('.row-checkbox'));
        }
        const start = (this.currentPage - 1) * this.pageSize;
        const end = start + this.pageSize;
        const visibleRows = this.allRows.slice(start, end);
        return visibleRows.map(row => row.querySelector('.row-checkbox')).filter(Boolean);
    }

    updateSelectAllState() {
        const selectAll = document.getElementById(this.selectAllId);
        if (!selectAll) return;

        const visibleCheckboxes = this.getVisibleCheckboxes();
        const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;

        if (checkedCount === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checkedCount === visibleCheckboxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    updateBulkActions() {
        const bulkActions = document.getElementById(this.bulkActionsId);
        const selectedCount = document.getElementById(this.selectedCountId);
        const allChecked = document.querySelectorAll('.row-checkbox:checked');

        if (selectedCount) {
            selectedCount.textContent = allChecked.length;
        }
        if (bulkActions) {
            if (allChecked.length > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }
    }

    getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked'))
            .map(cb => cb.dataset.id);
    }

    clearSelection() {
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
            cb.checked = false;
        });
        this.updateSelectAllState();
        this.updateBulkActions();
    }

    removeRows(ids) {
        ids.forEach(id => {
            const checkbox = document.querySelector(`.row-checkbox[data-id="${id}"]`);
            if (checkbox) {
                const row = checkbox.closest('tr');
                if (row) {
                    row.style.transition = 'opacity 0.3s, transform 0.3s';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        row.remove();
                        this.totalRows--;
                        this.allRows = this.allRows.filter(r => r !== row);
                        this.render();
                    }, 300);
                }
            }
        });
    }

    updateRowStatus(id, statusHtml, statusClass) {
        const checkbox = document.querySelector(`.row-checkbox[data-id="${id}"]`);
        if (checkbox) {
            const row = checkbox.closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.outerHTML = statusHtml;
            }
            // Flash effect
            row.style.transition = 'background-color 0.5s';
            row.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 1000);
        }
    }

    triggerServerPagination() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.pageSize);
        url.searchParams.set('page', this.currentPage);
        window.location.href = url.toString();
    }

    render() {
        if (this.serverSidePagination) return;

        const tbody = document.getElementById(this.tableBodyId);
        if (!tbody) return;

        const start = (this.currentPage - 1) * this.pageSize;
        const end = start + this.pageSize;

        // Show/hide rows based on pagination
        this.allRows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });

        // Update showing info
        const showingStart = document.getElementById(this.showingStartId);
        const showingEnd = document.getElementById(this.showingEndId);
        const totalRows = document.getElementById(this.totalRowsId);

        if (showingStart) showingStart.textContent = this.totalRows > 0 ? start + 1 : 0;
        if (showingEnd) showingEnd.textContent = Math.min(end, this.totalRows);
        if (totalRows) totalRows.textContent = this.totalRows;

        // Render pagination
        this.renderPagination();

        // Update select all state
        this.updateSelectAllState();
    }

    renderPagination() {
        const pagination = document.getElementById(this.paginationId);
        if (!pagination) return;

        const totalPages = Math.ceil(this.totalRows / this.pageSize);
        let html = '';

        // Previous button
        html += `<li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${this.currentPage - 1}">&laquo;</a>
        </li>`;

        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === this.currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        html += `<li class="page-item ${this.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${this.currentPage + 1}">&raquo;</a>
        </li>`;

        pagination.innerHTML = html;

        // Add click handlers
        pagination.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page >= 1 && page <= totalPages) {
                    this.currentPage = page;
                    if (this.serverSidePagination) {
                        this.triggerServerPagination();
                    } else {
                        this.render();
                    }
                }
            });
        });
    }
}

/**
 * Bulk Action Handler
 * Sends AJAX request for bulk actions
 */
async function handleBulkAction(action, url, tableControls = null) {
    if (!tableControls) {
        console.error('TableControls instance required');
        return;
    }

    const ids = tableControls.getSelectedIds();
    if (ids.length === 0) {
        showToast('Please select at least one item', 'warning');
        return;
    }

    const actionName = action.charAt(0).toUpperCase() + action.slice(1);
    if (!confirm(`Are you sure you want to ${action} ${ids.length} item(s)?`)) {
        return;
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ action, ids })
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');

            // Update UI based on action
            if (action === 'delete') {
                tableControls.removeRows(ids);
            } else {
                // Reload the page to get fresh data for status updates
                setTimeout(() => location.reload(), 500);
            }

            // Update stats if provided
            if (result.stats) {
                updateStats(result.stats);
            }

            tableControls.clearSelection();
        } else {
            showToast(result.message || 'Action failed', 'error');
        }
    } catch (error) {
        console.error('Bulk action error:', error);
        showToast('An error occurred. Please try again.', 'error');
    }
}

/**
 * Update stat cards with new values
 */
function updateStats(stats) {
    Object.keys(stats).forEach(key => {
        const element = document.getElementById(`stat-${key}`);
        if (element) {
            element.textContent = stats[key];
        }
    });
}

// Initialize tooltips on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
});
