<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Skill;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Job::where('status', 'posted');

        // Apply filters if they exist
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $jobs = $query->latest()->paginate(10);
        $categories = Skill::where('is_category', true)->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Job::class);

        $categories = Skill::where('is_category', true)->get();
        return view('jobs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:skills,id',
            'budget' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
        ]);

        $job = new Job($validated);
        $job->client_id = Auth::id();
        $job->status = 'posted';
        $job->save();

        return redirect()->route('jobs.show', $job)->with('success', 'Job posted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $job->load(['client', 'category', 'applications.worker']);
        $hasApplied = false;
        $userApplication = null;

        if (Auth::check() && Auth::user()->isWorker()) {
            $hasApplied = $job->applications->where('worker_id', Auth::id())->count() > 0;
            $userApplication = $job->applications->where('worker_id', Auth::id())->first();
        }

        return view('jobs.show', compact('job', 'hasApplied', 'userApplication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $this->authorize('update', $job);

        $categories = Skill::where('is_category', true)->get();
        return view('jobs.edit', compact('job', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:skills,id',
            'budget' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully!');
    }

    /**
     * Update job status
     */
    public function updateStatus(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'status' => 'required|in:posted,in_progress,completed',
        ]);

        $job->status = $validated['status'];

        if ($validated['status'] === 'completed') {
            $job->completed_at = now();
        }

        $job->save();

        return redirect()->route('jobs.show', $job)->with('success', 'Job status updated successfully!');
    }
}
