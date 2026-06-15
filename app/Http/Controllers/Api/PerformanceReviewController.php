<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePerformanceReviewRequest;
use App\Http\Requests\Api\UpdatePerformanceReviewRequest;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class PerformanceReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admins and HR managers can see all reviews
        $user = $request->user();
        if (! in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
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
    public function store(StorePerformanceReviewRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->has('reviewer_id')) {
            $request->validate(['reviewer_id' => 'exists:users,id']);
            $validated['reviewer_id'] = $request->reviewer_id;
        } else {
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
    public function update(UpdatePerformanceReviewRequest $request, string $id)
    {
        $performanceReview = PerformanceReview::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'ADMIN' && $user->role !== 'HR_MANAGER' && $performanceReview->reviewer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $performanceReview->update($request->validated());

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
