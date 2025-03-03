<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['client', 'category']);

        // Filter by status (default to open/posted jobs only)
        $query->where('status', 'posted');

        // Filter by search term
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $category = $request->category;
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category', $category);
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $location = $request->location;
            $query->where('location', 'like', "%{$location}%");
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        return view('jobs.index', compact('jobs'));
    }

    public function workers(Request $request)
    {
        $query = User::where('user_type', 'worker')
            ->with(['skills', 'receivedReviews']);

        // Filter by skill
        if ($request->filled('skill')) {
            $skill = $request->skill;
            $query->whereHas('skills', function ($q) use ($skill) {
                $q->where('name', 'like', "%{$skill}%")
                    ->orWhere('category', 'like', "%{$skill}%");
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('serviceAreas', function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('state', 'like', "%{$location}%")
                    ->orWhere('postal_code', 'like', "%{$location}%");
            });
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $minRating = $request->min_rating;
            $query->whereHas('receivedReviews', function ($q) use ($minRating) {
                $q->selectRaw('avg(rating) as average_rating')
                    ->havingRaw('average_rating >= ?', [$minRating]);
            });
        }

        $workers = $query->paginate(12)->withQueryString();
        $skills = Skill::select('category')->distinct()->get();

        return view('search.workers', compact('workers', 'skills'));
    }
}
