<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="States",
 *     description="US States endpoints"
 * )
 */
class StateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/states",
     *     summary="Get all US states",
     *     tags={"States"},
     *     @OA\Response(
     *         response=200,
     *         description="List of US states"
     *     )
     * )
     */
    public function index()
    {
        $states = DB::table('State')
            ->select('Abbr', 'State')
            ->orderBy('State', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $states
        ]);
    }
}
