<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the jobs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = Job::where('status', 'posted')
            ->latest()
            ->paginate(10);

        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only clients can create jobs
        if (!Auth::user()->isClient()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only clients can post jobs.');
        }

        $categories = Skill::select('category')
            ->distinct()
            ->orderBy('category')
            ->get();

        return view('jobs.create', compact('categories'));
    }

    /**
     * Store a newly created job in storage.
     *
     * @param  \App\Http\Requests\StoreJobRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJobRequest $request)
    {
        // Only clients can create jobs
        if (!Auth::user()->isClient()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only clients can post jobs.');
        }

        $job = new Job();
        $job->client_id = Auth::id();
        $job->title = $request->title;
        $job->description = $request->description;
        $job->category_id = $request->category_id;
        $job->budget = $request->budget;
        $job->location = $request->location;
        $job->status = 'posted';
        $job->save();

        // Handle file uploads if present
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $job->addMedia($file)
                    ->toMediaCollection('attachments');
            }
        }

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted successfully.');
    }

    /**
     * Display the specified job.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        $hasApplied = false;

        if (Auth::user()->isWorker()) {
            $hasApplied = JobApplication::where('job_id', $job->id)
                ->where('worker_id', Auth::id())
                ->exists();
        }

        $isOwner = Auth::id() === $job->client_id;

        return view('jobs.show', compact('job', 'hasApplied', 'isOwner'));
    }

    /**
     * Show the form for editing the specified job.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        // Only the job owner can edit it
        if (Auth::id() !== $job->client_id) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You are not authorized to edit this job.');
        }

        // Only allow editing if the job is still in posted status
        if ($job->status !== 'posted') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'This job can no longer be edited.');
        }

        $categories = Skill::select('category')
            ->distinct()
            ->orderBy('category')
            ->get();

        return view('jobs.edit', compact('job', 'categories'));
    }

    /**
     * Update the specified job in storage.
     *
     * @param  \App\Http\Requests\UpdateJobRequest  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        // Only the job owner can update it
        if (Auth::id() !== $job->client_id) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You are not authorized to update this job.');
        }

        // Only allow updating if the job is still in posted status
        if ($job->status !== 'posted') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'This job can no longer be updated.');
        }

        $job->title = $request->title;
        $job->description = $request->description;
        $job->category_id = $request->category_id;
        $job->budget = $request->budget;
        $job->location = $request->location;
        $job->save();

        // Handle file uploads if present
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $job->addMedia($file)
                    ->toMediaCollection('attachments');
            }
        }

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        // Only the job owner can delete it
        if (Auth::id() !== $job->client_id) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You are not authorized to delete this job.');
        }

        // Only allow deleting if the job is still in posted status
        if ($job->status !== 'posted') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'This job can no longer be deleted.');
        }

        $job->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Job deleted successfully.');
    }

    /**
     * Apply for a job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function apply(Request $request, Job $job)
    {
        // Only workers can apply for jobs
        if (!Auth::user()->isWorker()) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'Only workers can apply for jobs.');
        }

        // Check if the worker has already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('worker_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You have already applied for this job.');
        }

        // Validate the request
        $request->validate([
            'proposal' => 'required|string|min:10|max:1000',
        ]);

        // Create job application
        $application = new JobApplication();
        $application->job_id = $job->id;
        $application->worker_id = Auth::id();
        $application->proposal = $request->proposal;
        $application->status = 'pending';
        $application->save();

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * Mark a job as complete.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function complete(Job $job)
    {
        // Only the job owner can mark it as complete
        if (Auth::id() !== $job->client_id) {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'You are not authorized to mark this job as complete.');
        }

        // Only allow completing if the job is in progress
        if ($job->status !== 'in_progress') {
            return redirect()->route('jobs.show', $job)
                ->with('error', 'This job is not currently in progress.');
        }

        $job->status = 'completed';
        $job->save();

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job marked as complete. Please leave a review for the worker.');
    }
}
