<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QAController extends Controller
{
    /**
     * Get questions for a product (public).
     */
    public function productQuestions(Request $request, $productId)
    {
        $questions = DB::table('product_questions as q')
            ->leftJoin('product_answers as a', 'q.id', '=', 'a.question_id')
            ->where('q.product_id', $productId)
            ->where('q.status', 'answered')
            ->select(
                'q.id',
                'q.product_id',
                'q.customer_id',
                'q.customer_name',
                'q.question',
                'q.status',
                'q.helpful_votes',
                'q.unhelpful_votes',
                'q.created_at',
                'q.updated_at',
                DB::raw('COUNT(DISTINCT a.id) as answer_count')
            )
            ->groupBy('q.id', 'q.product_id', 'q.customer_id', 'q.customer_name', 'q.question', 'q.status', 'q.helpful_votes', 'q.unhelpful_votes', 'q.created_at', 'q.updated_at')
            ->orderBy('q.helpful_votes', 'desc')
            ->orderBy('q.created_at', 'desc')
            ->limit(20)
            ->get();

        // Get answers for each question
        foreach ($questions as $question) {
            $question->answers = DB::table('product_answers')
                ->where('question_id', $question->id)
                ->orderBy('is_verified', 'desc')
                ->orderBy('helpful_votes', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json(['data' => $questions]);
    }

    /**
     * Submit a question (customer).
     */
    public function askQuestion(Request $request, $productId)
    {
        $request->validate([
            'question' => 'required|string|min:10|max:1000',
            'customer_name' => 'required_without:customer_id|string|max:100',
            'customer_email' => 'required_without:customer_id|email|max:255',
            'customer_id' => 'nullable|integer',
        ]);

        $questionId = DB::table('product_questions')->insertGetId([
            'product_id' => $productId,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'question' => $request->question,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Your question has been submitted and is pending review.',
            'data' => ['id' => $questionId],
        ], 201);
    }

    /**
     * Vote on a question or answer.
     */
    public function vote(Request $request)
    {
        $request->validate([
            'vote_type' => 'required|in:question,answer',
            'item_id' => 'required|integer',
            'vote' => 'required|in:helpful,unhelpful',
        ]);

        $voterIp = $request->ip();

        // Check if already voted
        $existingVote = DB::table('qa_votes')
            ->where('vote_type', $request->vote_type)
            ->where('item_id', $request->item_id)
            ->where('voter_ip', $voterIp)
            ->first();

        if ($existingVote) {
            return response()->json(['message' => 'You have already voted on this item.'], 400);
        }

        // Record vote
        DB::table('qa_votes')->insert([
            'vote_type' => $request->vote_type,
            'item_id' => $request->item_id,
            'voter_ip' => $voterIp,
            'customer_id' => $request->customer_id,
            'vote' => $request->vote,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update vote count on question or answer
        $table = $request->vote_type === 'question' ? 'product_questions' : 'product_answers';
        $column = $request->vote === 'helpful' ? 'helpful_votes' : 'unhelpful_votes';

        DB::table($table)
            ->where('id', $request->item_id)
            ->increment($column);

        return response()->json(['message' => 'Vote recorded successfully.']);
    }

    // =============================================
    // ADMIN METHODS
    // =============================================

    /**
     * Get all questions (admin).
     */
    public function adminIndex(Request $request)
    {
        $query = DB::table('product_questions as q')
            ->leftJoin('products3 as p', 'q.product_id', '=', 'p.ID')
            ->select(
                'q.*',
                'p.ShortDescription as product_name',
                'p.ItemNumber as product_sku'
            );

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('q.status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('q.question', 'like', "%{$search}%")
                    ->orWhere('q.customer_name', 'like', "%{$search}%")
                    ->orWhere('q.customer_email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $questions = $query->orderBy('q.created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $questions->items(),
            'meta' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    /**
     * Get Q&A stats (admin).
     */
    public function adminStats()
    {
        $stats = [
            'total' => DB::table('product_questions')->count(),
            'pending' => DB::table('product_questions')->where('status', 'pending')->count(),
            'approved' => DB::table('product_questions')->where('status', 'approved')->count(),
            'answered' => DB::table('product_questions')->where('status', 'answered')->count(),
            'rejected' => DB::table('product_questions')->where('status', 'rejected')->count(),
            'total_answers' => DB::table('product_answers')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Get single question with answers (admin).
     */
    public function adminShow($id)
    {
        $question = DB::table('product_questions as q')
            ->leftJoin('products3 as p', 'q.product_id', '=', 'p.ID')
            ->where('q.id', $id)
            ->select(
                'q.*',
                'p.ShortDescription as product_name',
                'p.ItemNumber as product_sku',
                'p.Image as product_image'
            )
            ->first();

        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        $answers = DB::table('product_answers')
            ->where('question_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => [
                'question' => $question,
                'answers' => $answers,
            ],
        ]);
    }

    /**
     * Update question status (admin).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,answered',
        ]);

        DB::table('product_questions')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Question status updated successfully.']);
    }

    /**
     * Answer a question (admin).
     */
    public function answerQuestion(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string|min:10',
            'answered_by' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
        ]);

        // Insert answer
        $answerId = DB::table('product_answers')->insertGetId([
            'question_id' => $id,
            'user_id' => $request->user_id,
            'answered_by' => $request->answered_by ?? 'Pecos River Trading Post',
            'answer_type' => 'official',
            'answer' => $request->answer,
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update question status to answered
        DB::table('product_questions')
            ->where('id', $id)
            ->update([
                'status' => 'answered',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Answer submitted successfully.',
            'data' => ['id' => $answerId],
        ], 201);
    }

    /**
     * Delete a question (admin).
     */
    public function destroy($id)
    {
        DB::table('product_questions')->where('id', $id)->delete();

        return response()->json(['message' => 'Question deleted successfully.']);
    }
}
