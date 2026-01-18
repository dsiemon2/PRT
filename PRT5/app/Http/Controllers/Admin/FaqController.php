<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function statistics(Request $request)
    {
        $sortBy = $request->get('sort', 'helpful_ratio');

        // Build ORDER BY clause
        $orderBy = match($sortBy) {
            'views' => 'views DESC',
            'helpful' => 'helpful_count DESC',
            'not_helpful' => 'not_helpful_count DESC',
            'helpful_ratio' => 'helpful_percentage DESC',
            'total_votes' => 'total_votes DESC',
            default => 'views DESC'
        };

        // Get all FAQs with stats for insights section
        $allFaqs = DB::select("
            SELECT
                f.id,
                f.question,
                f.views,
                f.helpful_count,
                f.not_helpful_count,
                (f.helpful_count + f.not_helpful_count) as total_votes,
                CASE
                    WHEN (f.helpful_count + f.not_helpful_count) > 0
                    THEN ROUND((f.helpful_count / (f.helpful_count + f.not_helpful_count)) * 100, 1)
                    ELSE 0
                END as helpful_percentage,
                fc.name as category_name,
                fc.icon as category_icon
            FROM faqs f
            LEFT JOIN faq_categories fc ON f.category_id = fc.id
            WHERE f.status = 'active'
            ORDER BY {$orderBy}
        ");

        // Get summary statistics
        $stats = DB::selectOne("
            SELECT
                COUNT(*) as total_faqs,
                COALESCE(SUM(views), 0) as total_views,
                COALESCE(SUM(helpful_count), 0) as total_helpful,
                COALESCE(SUM(not_helpful_count), 0) as total_not_helpful,
                COALESCE(AVG(views), 0) as avg_views,
                COALESCE(ROUND(SUM(helpful_count) / NULLIF(SUM(helpful_count) + SUM(not_helpful_count), 0) * 100, 1), 0) as overall_helpful_percentage
            FROM faqs
            WHERE status = 'active'
        ");

        // Paginate manually
        $perPage = 20;
        $page = max(1, (int) $request->get('page', 1));
        $totalFaqs = count($allFaqs);
        $totalPages = max(1, ceil($totalFaqs / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $faqs = array_slice($allFaqs, $offset, $perPage);

        // Get best performing FAQs (min 3 votes, highest helpful %)
        $bestFaqs = collect($allFaqs)
            ->filter(fn($f) => $f->total_votes >= 3)
            ->sortByDesc('helpful_percentage')
            ->take(5);

        // Get worst performing FAQs (min 3 votes, lowest helpful %)
        $worstFaqs = collect($allFaqs)
            ->filter(fn($f) => $f->total_votes >= 3)
            ->sortBy('helpful_percentage')
            ->take(5);

        return view('admin.faq.statistics', compact(
            'faqs',
            'allFaqs',
            'stats',
            'sortBy',
            'page',
            'perPage',
            'totalFaqs',
            'totalPages',
            'bestFaqs',
            'worstFaqs'
        ));
    }
}
