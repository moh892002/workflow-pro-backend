<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePerformanceReviewRequest;
use App\Http\Requests\Api\UpdatePerformanceReviewRequest;
use App\Models\PerformanceReview;
use App\Services\PerformanceReviewService;
use Illuminate\Http\Request;

class PerformanceReviewController extends Controller
{
    public function __construct(
        private readonly PerformanceReviewService $reviewService,
    ) {}

    public function index(Request $request)
    {
        return $this->reviewService->all($request->user());
    }

    public function store(StorePerformanceReviewRequest $request)
    {
        $validated = $request->validated();

        if ($request->has('reviewer_id')) {
            $request->validate(['reviewer_id' => 'exists:users,id']);
        }

        $review = $this->reviewService->create($request->user(), $validated);

        return response()->json($review->load(['user', 'reviewer']), 201);
    }

    public function show(PerformanceReview $performanceReview)
    {
        $this->authorize('view', $performanceReview);

        return $performanceReview->load(['user', 'reviewer']);
    }

    public function update(UpdatePerformanceReviewRequest $request, PerformanceReview $performanceReview)
    {
        $this->authorize('update', $performanceReview);

        $performanceReview->update($request->validated());

        return response()->json($performanceReview->load(['user', 'reviewer']));
    }

    public function destroy(PerformanceReview $performanceReview)
    {
        $this->authorize('delete', $performanceReview);

        $performanceReview->delete();

        return response()->json(['message' => 'Performance review deleted successfully']);
    }
}
