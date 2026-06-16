<?php

use App\Models\PerformanceReview;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->admin = makeUser(['role' => 'ADMIN']);
    $this->employee = makeUser(['role' => 'EMPLOYEE']);
    $this->other = makeUser(['role' => 'EMPLOYEE']);
});

test('admin can list performance reviews', function () {
    PerformanceReview::create([
        'user_id' => $this->employee->id,
        'reviewer_id' => $this->admin->id,
        'score' => 80,
        'review_period' => '2026-Q1',
        'status' => 'completed',
        'final_feedback' => 'Great work',
        'review_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson('/api/performance-reviews');

    $response->assertStatus(200);
});

test('admin can create a performance review', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/performance-reviews', [
            'user_id' => $this->employee->id,
            'reviewer_id' => $this->admin->id,
            'score' => 85,
            'review_period' => '2026-Q2',
            'status' => 'pending',
            'final_feedback' => 'Excellent performance',
            'review_date' => now()->toDateString(),
        ]);

    $response->assertStatus(201);

    expect(PerformanceReview::where('user_id', $this->employee->id)->exists())->toBeTrue();
});

test('admin can view a performance review', function () {
    $review = PerformanceReview::create([
        'user_id' => $this->employee->id,
        'reviewer_id' => $this->admin->id,
        'score' => 75,
        'review_period' => '2026-Q1',
        'status' => 'completed',
        'final_feedback' => 'Good',
        'review_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson("/api/performance-reviews/{$review->id}");

    $response->assertStatus(200);
});

test('admin can update a performance review', function () {
    $review = PerformanceReview::create([
        'user_id' => $this->employee->id,
        'reviewer_id' => $this->admin->id,
        'score' => 50,
        'review_period' => '2026-Q1',
        'status' => 'draft',
        'final_feedback' => 'Needs improvement',
        'review_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->putJson("/api/performance-reviews/{$review->id}", [
            'user_id' => $this->employee->id,
            'reviewer_id' => $this->admin->id,
            'score' => 80,
            'review_period' => '2026-Q1',
            'status' => 'completed',
            'final_feedback' => 'Improved a lot',
            'review_date' => now()->toDateString(),
        ]);

    $response->assertStatus(200);

    expect(PerformanceReview::find($review->id)->score)->toBe(80);
});

test('admin can delete a performance review', function () {
    $review = PerformanceReview::create([
        'user_id' => $this->employee->id,
        'reviewer_id' => $this->admin->id,
        'score' => 95,
        'review_period' => '2026-Q1',
        'status' => 'completed',
        'final_feedback' => 'Perfect',
        'review_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/performance-reviews/{$review->id}");

    $response->assertStatus(200);
});

test('employee cannot review themselves', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/performance-reviews', [
            'user_id' => $this->employee->id,
            'reviewer_id' => $this->admin->id,
            'score' => 90,
            'review_period' => '2026-Q1',
            'status' => 'pending',
            'final_feedback' => 'Self review',
            'review_date' => now()->toDateString(),
        ]);

    $response->assertStatus(422);
});
