<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePerformanceReviewRequest;
use App\Http\Requests\Api\UpdatePerformanceReviewRequest;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class PerformanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
            return PerformanceReview::where('user_id', $user->id)
                ->with(['user', 'reviewer'])
                ->get();
        }

        return PerformanceReview::with(['user', 'reviewer'])->get();
    }

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
