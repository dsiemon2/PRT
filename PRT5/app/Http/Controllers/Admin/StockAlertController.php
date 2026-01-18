<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Traits\HasGridFeatures;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    use HasGridFeatures;

    public function index(Request $request)
    {
        $query = StockAlert::with('product');

        // Apply status filter (resolved/active)
        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'resolved') {
            $query->where('is_resolved', true);
        }

        // Apply alert type filter
        if ($request->filled('type') && $request->get('type') !== 'all') {
            $query->where('alert_type', $request->get('type'));
        }

        // Apply sorting
        $this->applySorting($query, $request, 'created_at', 'desc');

        // Get paginated results
        $alerts = $this->getPaginated($query, $request);

        // Get stats
        $stats = [
            'total' => StockAlert::count(),
            'active' => StockAlert::active()->count(),
            'low_stock' => StockAlert::active()->where('alert_type', 'low_stock')->count(),
            'out_of_stock' => StockAlert::active()->where('alert_type', 'out_of_stock')->count(),
            'resolved' => StockAlert::where('is_resolved', true)->count(),
        ];

        $filters = $this->getFilterOptions($request, [
            'type' => $request->get('type', 'all'),
        ]);

        return view('admin.stock-alerts.index', compact('alerts', 'stats', 'filters'));
    }

    public function update(Request $request, StockAlert $alert)
    {
        $validated = $request->validate([
            'is_resolved' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $alert->update([
            'is_resolved' => $validated['is_resolved'] ?? $alert->is_resolved,
            'resolved_at' => $validated['is_resolved'] ? now() : null,
            'resolved_by' => $validated['is_resolved'] ? auth()->id() : null,
            'notes' => $validated['notes'] ?? $alert->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alert updated.',
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with('success', 'Alert updated.');
    }

    public function bulkResolve(Request $request)
    {
        // Handle "resolve_all" to resolve all active alerts (prt4 style)
        if ($request->boolean('resolve_all')) {
            $count = StockAlert::active()->update([
                'is_resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => auth()->id(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Resolved {$count} alerts.",
                    'count' => $count,
                    'stats' => $this->getStats(),
                ]);
            }

            return back()->with('success', "Resolved {$count} alerts successfully!");
        }

        // Handle bulk action from selected checkboxes
        $result = $this->handleBulkAction(
            $request,
            StockAlert::class,
            ['resolve']
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'count' => $result['count'],
                'stats' => $this->getStats(),
            ]);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function getStats(): array
    {
        return [
            'total' => StockAlert::count(),
            'active' => StockAlert::active()->count(),
            'low_stock' => StockAlert::active()->where('alert_type', 'low_stock')->count(),
            'out_of_stock' => StockAlert::active()->where('alert_type', 'out_of_stock')->count(),
            'resolved' => StockAlert::where('is_resolved', true)->count(),
        ];
    }
}
