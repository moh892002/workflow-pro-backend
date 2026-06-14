<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceReview;
use App\Models\User;
use Carbon\Carbon;

class PerformanceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create reviews for
        $users = User::all();
        $adminsAndHr = $users->filter(fn($user) => in_array($user->role, ['ADMIN', 'HR_MANAGER']));
        $employees = $users->filter(fn($user) => $user->role === 'EMPLOYEE');

        // If we don't have enough users, we'll use what we have
        if ($employees->isEmpty()) {
            $employees = $users;
        }

        // Create a few performance reviews
        foreach ($employees->take(3) as $employee) {
            // Pick a random reviewer from admins/hr or fallback to any user
            $reviewer = $adminsAndHr->random() ?? $users->random();

            // Ensure we don't have the user reviewing themselves unless they are admin/hr
            if ($reviewer->id === $employee->id && !in_array($reviewer->role, ['ADMIN', 'HR_MANAGER'])) {
                // Try to get a different reviewer
                $reviewer = $users->where('id', '!=', $employee->id)->random() ?? $reviewer;
            }

            PerformanceReview::create([
                'user_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'score' => rand(60, 95),
                'review_period' => 'Q2 2024',
                'ai_generated_feedback' => 'The employee has shown good performance in their assigned tasks. Areas for improvement include communication and time management.',
                'final_feedback' => 'Overall, a solid performer. With some focus on the suggested areas, they can excel further.',
                'status' => 'completed',
            ]);
        }

        // Create one pending review for demonstration
        if (!$employees->isEmpty()) {
            $employee = $employees->first();
            $reviewer = $adminsAndHr->random() ?? $users->random();

            PerformanceReview::create([
                'user_id' => $employee->id,
                'reviewer_id' => $reviewer->id,
                'score' => 85,
                'review_period' => 'Q3 2024',
                'ai_generated_feedback' => 'The employee has been proactive in taking on new responsibilities.',
                'final_feedback' => null, // Not yet finalized
                'status' => 'pending',
            ]);
        }
    }
}
