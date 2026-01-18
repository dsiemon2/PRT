<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HasGridFeatures
{
    protected int $defaultPerPage = 20;
    protected array $allowedPerPage = [10, 20, 50, 100];

    /**
     * Get per page value from request, validated against allowed values
     */
    protected function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', $this->defaultPerPage);
        return in_array($perPage, $this->allowedPerPage) ? $perPage : $this->defaultPerPage;
    }

    /**
     * Apply common search filter to query
     */
    protected function applySearch(Builder $query, Request $request, array $searchableColumns): Builder
    {
        $search = $request->get('search');

        if ($search) {
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Apply status filter to query
     */
    protected function applyStatusFilter(Builder $query, Request $request, string $column = 'status'): Builder
    {
        $status = $request->get('status');

        if ($status && $status !== 'all') {
            $query->where($column, $status);
        }

        return $query;
    }

    /**
     * Apply date range filter to query
     */
    protected function applyDateFilter(Builder $query, Request $request, string $column = 'created_at'): Builder
    {
        if ($request->filled('date_from')) {
            $query->whereDate($column, '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate($column, '<=', $request->get('date_to'));
        }

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request, string $defaultSort = 'created_at', string $defaultDirection = 'desc'): Builder
    {
        $sortBy = $request->get('sort', $defaultSort);
        $sortDir = $request->get('direction', $defaultDirection);

        // Validate direction
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : $defaultDirection;

        return $query->orderBy($sortBy, $sortDir);
    }

    /**
     * Get paginated results with preserved query string
     */
    protected function getPaginated(Builder $query, Request $request): LengthAwarePaginator
    {
        return $query->paginate($this->getPerPage($request))
                     ->appends($request->except('page'));
    }

    /**
     * Handle bulk action request
     * Returns array with success status and message
     */
    protected function handleBulkAction(Request $request, string $modelClass, array $allowedActions): array
    {
        $request->validate([
            'action' => 'required|in:' . implode(',', $allowedActions),
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');
        $count = 0;

        switch ($action) {
            case 'delete':
                $count = $modelClass::whereIn('id', $ids)->delete();
                $message = "{$count} item(s) deleted successfully.";
                break;

            case 'approve':
                $count = $modelClass::whereIn('id', $ids)->update(['status' => 'approved']);
                $message = "{$count} item(s) approved.";
                break;

            case 'reject':
                $count = $modelClass::whereIn('id', $ids)->update(['status' => 'rejected']);
                $message = "{$count} item(s) rejected.";
                break;

            case 'spam':
                $count = $modelClass::whereIn('id', $ids)->update(['status' => 'spam']);
                $message = "{$count} item(s) marked as spam.";
                break;

            case 'resolve':
                $count = $modelClass::whereIn('id', $ids)->update([
                    'is_resolved' => true,
                    'resolved_at' => now(),
                    'resolved_by' => auth()->id(),
                ]);
                $message = "{$count} item(s) resolved.";
                break;

            case 'archive':
                $count = $modelClass::whereIn('id', $ids)->update(['status' => 'archived']);
                $message = "{$count} item(s) archived.";
                break;

            case 'read':
                $count = $modelClass::whereIn('id', $ids)->update(['status' => 'read']);
                $message = "{$count} item(s) marked as read.";
                break;

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown action.',
                    'count' => 0,
                ];
        }

        return [
            'success' => true,
            'message' => $message,
            'count' => $count,
        ];
    }

    /**
     * Get filter options for view
     */
    protected function getFilterOptions(Request $request, array $additionalFilters = []): array
    {
        return array_merge([
            'search' => $request->get('search', ''),
            'status' => $request->get('status', 'all'),
            'per_page' => $this->getPerPage($request),
            'sort' => $request->get('sort', ''),
            'direction' => $request->get('direction', 'desc'),
        ], $additionalFilters);
    }
}
