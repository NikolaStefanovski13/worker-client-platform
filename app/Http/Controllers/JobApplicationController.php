<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user.type:worker')->only(['store']);
        $this->middleware('user.type:client')->only(['accept', 'reject']);
    }

    public function store(Request $request, Job $job)
    {
        // Check if worker has already applied
        $exists = JobApplication::where('job_id', $job->id)
            ->where('worker_id', auth()->id())
            ->exists();

        if ($exists) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You have already applied for this job.');
        }

        // Validate the request
        $validated = $request->validate([
            'proposal' => 'required|string',
            'price_quoted' => 'nullable|numeric|min:1',
        ]);

        // Create the application
        JobApplication::create([
            'job_id' => $job->id,
            'worker_id' => auth()->id(),
            'proposal' => $validated['proposal'],
            'price_quoted' => $validated['price_quoted'] ?? $job->budget,
            'status' => 'pending',
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Your application has been submitted successfully.');
    }

    public function accept(JobApplication $application)
    {
        $job = $application->job;

        // Check if the authenticated user is the job owner
        if ($job->client_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to accept this application.');
        }

        // Update application status
        $application->update(['status' => 'accepted']);

        // Update job status
        $job->update(['status' => 'in_progress']);

        // Reject all other applications
        $job->applications()
            ->where('id', '!=', $application->id)
            ->update(['status' => 'rejected']);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Application accepted. The worker has been notified.');
    }

    public function reject(JobApplication $application)
    {
        $job = $application->job;

        // Check if the authenticated user is the job owner
        if ($job->client_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to reject this application.');
        }

        // Update application status
        $application->update(['status' => 'rejected']);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Application rejected.');
    }
}
