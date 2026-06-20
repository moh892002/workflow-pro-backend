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
        return $this->success($this->reviewService->all($request->user()));
    }

    public function store(StorePerformanceReviewRequest $request)
    {
        $review = $this->reviewService->create($request->user(), $request->validated());

        return $this->created($review->load(['user', 'reviewer']));
    }

    public function show(PerformanceReview $performanceReview)
    {
        $this->authorize('view', $performanceReview);

        return $this->success($performanceReview->load(['user', 'reviewer']));
    }

    public function update(UpdatePerformanceReviewRequest $request, PerformanceReview $performanceReview)
    {
        $this->authorize('update', $performanceReview);

        $performanceReview->update($request->validated());

        return $this->success($performanceReview->load(['user', 'reviewer']));
    }

    public function destroy(PerformanceReview $performanceReview)
    {
        $this->authorize('delete', $performanceReview);

        $performanceReview->delete();

        return $this->message('Performance review deleted successfully');
    }
}
