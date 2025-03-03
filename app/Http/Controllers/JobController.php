<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Skill;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user.type:client')->only(['create', 'store']);
    }

    public function index()
    {
        $jobs = Job::with(['client', 'category'])
            ->where('status', 'posted')
            ->latest()
            ->paginate(10);

        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        $categories = Skill::select('id', 'name')
            ->distinct('category')
            ->get()
            ->groupBy('category');

        return view('jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:skills,id',
            'budget' => 'required|numeric|min:1',
            'location' => 'required|string|max:255',
        ]);

        $job = Job::create([
            'client_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'budget' => $validated['budget'],
            'location' => $validated['location'],
            'status' => 'posted',
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted successfully!');
    }

    public function show(Job $job)
    {
        $job->load(['client', 'category', 'applications.worker']);

        $hasApplied = false;
        if (auth()->user()->isWorker()) {
            $hasApplied = $job->applications()->where('worker_id', auth()->id())->exists();
        }

        return view('jobs.show', compact('job', 'hasApplied'));
    }

    public function edit(Job $job)
    {
        if ($job->client_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit this job.');
        }

        $categories = Skill::select('id', 'name')
            ->distinct('category')
            ->get()
            ->groupBy('category');

        return view('jobs.edit', compact('job', 'categories'));
    }

    public function update(Request $request, Job $job)
    {
        if ($job->client_id !== auth()->id()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit this job.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:skills,id',
            'budget' => 'required|numeric|min:1',
            'location' => 'required|string|max:255',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully!');
    }

    public function complete(Job $job)
    {
        if ($job->client_id !== auth()->id() || $job->status !== 'in_progress') {
            return redirect()->route('dashboard')
                ->with('error', 'You cannot mark this job as complete.');
        }

        $job->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job marked as complete. Please leave a review for the worker.');
    }
}
