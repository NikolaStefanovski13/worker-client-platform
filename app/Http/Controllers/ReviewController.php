<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Determine the reviewee (the user being reviewed)
        $revieweeId = $user->id === $job->client_id
            ? $job->applications()->where('status', 'accepted')->first()->worker_id
            : $job->client_id;

        // Check if the user is authorized to review this job
        if (
            $user->id !== $job->client_id &&
            !$job->applications()->where('worker_id', $user->id)->where('status', 'accepted')->exists()
        ) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to review this job.');
        }

        // Check if the job is completed
        if ($job->status !== 'completed') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You can only review completed jobs.');
        }

        // Check if the user has already reviewed this job
        if (Review::where('job_id', $job->id)
            ->where('reviewer_id', $user->id)
            ->exists()
        ) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You have already reviewed this job.');
        }

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        // Create the review
        Review::create([
            'job_id' => $job->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Your review has been submitted.');
    }
}
