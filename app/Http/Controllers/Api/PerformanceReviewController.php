<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PerformanceReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admins and HR managers can see all reviews
        $user = $request->user();
        if (!in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
            // Regular users can only see their own reviews
            return PerformanceReview::where('user_id', $user->id)
                ->with(['user', 'reviewer'])
                ->get();
        }

        // Admins and HR can see all reviews
        return PerformanceReview::with(['user', 'reviewer'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validate the request
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([$user->id]) // You cannot review yourself unless you're admin/hr?
            ],
            'score' => 'required|integer|min:0|max:100',
            'review_period' => 'required|string',
            'ai_generated_feedback' => 'nullable|string',
            'final_feedback' => 'nullable|string',
            'status' => ['required', Rule::in(['draft', 'pending', 'completed'])],
        ]);

        // Set the reviewer to the current user if not specified (but we expect it in the request)
        // For security, we set the reviewer to the current user unless the user is admin/hr specifying another reviewer
        if (!in_array($user->role, ['ADMIN', 'HR_MANAGER']) && !isset($validated['reviewer_id'])) {
            $validated['reviewer_id'] = $user->id;
        } elseif (isset($validated['reviewer_id'])) {
            // Validate that the reviewer exists
            $validated['reviewer_id'] = $request->validate([
                'reviewer_id' => 'required|exists:users,id'
            ])['reviewer_id'];
        } else {
            // Default to current user as reviewer
            $validated['reviewer_id'] = $user->id;
        }

        $performanceReview = PerformanceReview::create($validated);

        return response()->json($performanceReview->load(['user', 'reviewer']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $performanceReview = PerformanceReview::with(['user', 'reviewer'])->findOrFail($id);

        // Check authorization: user can see their own reviews, or admin/hr can see any
        $user = request()->user();
        if ($user->role !== 'ADMIN' && $user->role !== 'HR_MANAGER' && $performanceReview->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $performanceReview;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $performanceReview = PerformanceReview::findOrFail($id);
        $user = $request->user();

        // Authorization: only the reviewer can update (unless admin/hr)
        if ($user->role !== 'ADMIN' && $user->role !== 'HR_MANAGER' && $performanceReview->reviewer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'score' => 'sometimes|integer|min:0|max:100',
            'review_period' => 'sometimes|string',
            'ai_generated_feedback' => 'sometimes|string',
            'final_feedback' => 'sometimes|string',
            'status' => ['sometimes', Rule::in(['draft', 'pending', 'completed'])],
        ]);

        $performanceReview->update($validated);

        return response()->json($performanceReview->load(['user', 'reviewer']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $performanceReview = PerformanceReview::findOrFail($id);
        $user = request()->user();

        // Only admin/hr can delete, or the reviewer if it's their own review? 
        // Typically, only admin/hr can delete reviews.
        if ($user->role !== 'ADMIN' && $user->role !== 'HR_MANAGER') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $performanceReview->delete();

        return response()->json(['message' => 'Performance review deleted successfully']);
    }
}
