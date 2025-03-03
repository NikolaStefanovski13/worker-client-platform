<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isWorker()) {



            // Get worker's applied jobs
            $appliedJobs = JobApplication::where('worker_id', $user->id)
                ->with('job')
                ->get()
                ->groupBy('status');

            // Get jobs that match worker's skills
            $skillIds = $user->skills->pluck('id')->toArray() ?? [0];
            $matchingJobs = Job::where('status', 'posted')
                ->whereHas('category', function ($query) use ($skillIds) {
                    $query->whereIn('id', $skillIds);
                })
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.worker', compact('appliedJobs', 'matchingJobs'));
        } else {
            // Get client's posted jobs
            $postedJobs = Job::where('client_id', $user->id)
                ->with(['applications' => function ($query) {
                    $query->with('worker');
                }])
                ->latest()
                ->get()
                ->groupBy('status');

            return view('dashboard.client', compact('postedJobs'));
        }
    }
}
